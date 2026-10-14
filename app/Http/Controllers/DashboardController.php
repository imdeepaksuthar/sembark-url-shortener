<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|JsonResponse
    {
        $user = $request->user();

        $period = $request->query('period', 'this_month');

        if (
            !in_array($period, [
                'all',
                'today',
                'last_week',
                'this_month',
                'last_month',
            ], true)
        ) {
            $period = 'this_month';
        }

        // Base URL query, restricted by the logged-in user's role.
        $baseQuery = ShortUrl::query()
            ->where(function (Builder $query) use ($user) {
                if ($user->role === User::ROLE_ADMIN) {
                    $query->where('company_id', $user->company_id);
                } elseif ($user->role === User::ROLE_MEMBER) {
                    $query->where('user_id', $user->id);
                }
            });

        $totalUrls = (clone $baseQuery)->count();
        $totalClicks = (clone $baseQuery)->sum('clicks');

        $urlQuery = (clone $baseQuery)
            ->with(['company', 'user'])
            ->latest();

        $this->applyPeriod($urlQuery, $period);

        $viewAll = $request->boolean('view_all') || $request->input('per_page') === 'all';
        $perPage = $viewAll ? 1000 : 2;

        $shortUrls = $urlQuery
            ->paginate($perPage, ['*'], 'urls_page')
            ->withQueryString();

        $companies = collect();
        $teamMembers = collect();
        $allUsers = collect();
        $allCompanies = collect();
        $totalCompanies = 0;
        $totalUsers = 0;

        $viewAllCompanies = $request->boolean('view_all_companies');
        $viewAllUsers = $request->boolean('view_all_users');
        $viewAllMembers = $request->boolean('view_all_members');

        if ($user->role === User::ROLE_SUPER_ADMIN) {
            $totalCompanies = Company::count();
            $totalUsers = User::count();

            $companies = Company::query()
                ->withCount(['users', 'shortUrls'])
                ->withSum('shortUrls', 'clicks')
                ->latest()
                ->paginate($viewAllCompanies ? 1000 : 2, ['*'], 'companies_page')
                ->withQueryString();

            $allUsers = User::query()
                ->with('company')
                ->withCount('shortUrls')
                ->withSum('shortUrls', 'clicks')
                ->latest()
                ->paginate($viewAllUsers ? 1000 : 2, ['*'], 'users_page')
                ->withQueryString();

            $allCompanies = Company::orderBy('name')->get(['id', 'name']);
        }

        if ($user->role === User::ROLE_ADMIN) {
            $totalUsers = User::where('company_id', $user->company_id)->count();

            $teamMembers = User::query()
                ->where('company_id', $user->company_id)
                ->withCount('shortUrls')
                ->withSum('shortUrls', 'clicks')
                ->latest()
                ->paginate($viewAllMembers ? 1000 : 2, ['*'], 'members_page')
                ->withQueryString();
        }

        if ($request->ajax()) {
            return response()->json([
                'html' => view('dashboard.partials.urls-table', [
                    'shortUrls' => $shortUrls,
                    'role' => $user->role,
                    'period' => $period,
                    'viewAll' => $viewAll,
                ])->render(),
                'exportUrl' => route('short-urls.export', ['period' => $period]),
            ]);
        }

        return view('dashboard', [
            'role' => $user->role,
            'companies' => $companies,
            'allCompanies' => $allCompanies,
            'allUsers' => $allUsers,
            'teamMembers' => $teamMembers,
            'shortUrls' => $shortUrls,
            'totalUrls' => $totalUrls,
            'totalClicks' => $totalClicks,
            'totalCompanies' => $totalCompanies,
            'totalUsers' => $totalUsers,
            'period' => $period,
            'viewAll' => $viewAll,
            'viewAllCompanies' => $viewAllCompanies,
            'viewAllUsers' => $viewAllUsers,
            'viewAllMembers' => $viewAllMembers,
        ]);
    }

    private function applyPeriod(
        Builder $query,
        string $period
    ): void {
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;

            case 'last_week':
                $start = now()->subWeek()->startOfWeek();
                $end = now()->subWeek()->endOfWeek();

                $query->whereBetween('created_at', [$start, $end]);
                break;

            case 'this_month':
                $query->whereBetween('created_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth(),
                ]);
                break;

            case 'last_month':
                $start = now()->subMonthNoOverflow()->startOfMonth();
                $end = now()->subMonthNoOverflow()->endOfMonth();

                $query->whereBetween('created_at', [$start, $end]);
                break;

            case 'all':
            default:
                // Do not restrict records by date.
                break;
        }
    }
}