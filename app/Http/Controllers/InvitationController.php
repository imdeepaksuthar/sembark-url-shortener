<?php

namespace App\Http\Controllers;

use App\Mail\UserInvitationMail;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class InvitationController extends Controller
{
    public function inviteCompanyAdmin(
        Request $request
    ): RedirectResponse {
        Gate::authorize('manage-companies');

        $data = $request->validate([
            'company_id' => ['nullable', 'exists:companies,id'],
            'company_name' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(!$request->filled('company_id')),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:users,email',
                Rule::requiredIf($request->filled('company_id')),
            ],
        ]);

        $token = Str::random(64);

        $result = DB::transaction(function () use ($data, $token, $request) {
            if (!empty($data['company_id'])) {
                $company = Company::findOrFail($data['company_id']);
            } else {
                $company = Company::create([
                    'name' => $data['company_name'],
                    'slug' => Str::slug($data['company_name'])
                        . '-' . Str::lower(Str::random(6)),
                ]);
            }

            $invitation = null;
            if (!empty($data['email'])) {
                $invitation = Invitation::create([
                    'company_id' => $company->id,
                    'email' => $data['email'],
                    'role' => User::ROLE_ADMIN,
                    'token_hash' => hash('sha256', $token),
                    'invited_by' => $request->user()->id,
                    'expires_at' => now()->addDays(7),
                ]);
            }

            return [
                'company' => $company,
                'invitation' => $invitation,
            ];
        });

        if ($result['invitation']) {
            Mail::to($data['email'])->send(
                new UserInvitationMail($result['company']->name, $token)
            );

            return back()->with('status', 'Company Admin invited.');
        }

        return back()->with('status', 'Company created successfully.');
    }

    public function inviteTeamMember(
        Request $request
    ): RedirectResponse {
        Gate::authorize('manage-team');

        $user = $request->user();

        $data = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'role' => [
                'required',
                Rule::in([
                    User::ROLE_ADMIN,
                    User::ROLE_MEMBER,
                ]),
            ],
        ]);

        $token = Str::random(64);

        $invitation = DB::transaction(function () use ($user, $data, $token) {
            return Invitation::create([
                'company_id' => $user->company_id,
                'email' => $data['email'],
                'role' => $data['role'],
                'token_hash' => hash('sha256', $token),
                'invited_by' => $user->id,
                'expires_at' => now()->addDays(7),
            ]);
        });

        Mail::to($invitation->email)->send(
            new UserInvitationMail(
                $invitation->company->name,
                $token
            )
        );

        return back()->with('status', 'Team invitation sent.');
    }

    public function show(string $token): View|RedirectResponse
    {
        $invitation = Invitation::where(
            'token_hash',
            hash('sha256', $token)
        )->firstOrFail();

        if ($invitation->accepted_at) {
            return redirect()
                ->route('login')
                ->with('status', 'This invitation has already been accepted. Please log in.');
        }

        abort_if(
            $invitation->expires_at->isPast(),
            410,
            'Invitation has expired.'
        );

        if (User::where('email', $invitation->email)->exists()) {
            return redirect()
                ->route('login')
                ->with('status', 'An account already exists for ' . $invitation->email . '. Please log in.');
        }

        return view('invitations.accept', compact('token', 'invitation'));
    }

    public function accept(
        Request $request,
        string $token
    ): RedirectResponse {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = DB::transaction(function () use ($data, $token) {
            $invitation = Invitation::query()
                ->where('token_hash', hash('sha256', $token))
                ->lockForUpdate()
                ->firstOrFail();

            abort_if(
                $invitation->accepted_at
                || $invitation->expires_at->isPast(),
                410,
                'Invitation has expired or was already used.'
            );

            abort_if(
                User::where('email', $invitation->email)->exists(),
                409,
                'An account already exists for this email.'
            );

            $user = User::create([
                'name' => $data['name'],
                'email' => $invitation->email,
                'password' => Hash::make($data['password']),
                'company_id' => $invitation->company_id,
                'role' => $invitation->role,
            ]);

            $invitation->update([
                'accepted_at' => now(),
            ]);

            return $user;
        });

        if (Auth::check()) {
            Auth::logout();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}