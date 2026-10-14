<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShortUrlRequest;
use App\Models\ShortUrl;
use App\Models\User;
use App\Services\ShortUrlService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ShortUrlController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        Gate::authorize('viewAny', ShortUrl::class);

        $query = ShortUrl::with(['company', 'user'])->latest();

        if ($user->role === User::ROLE_ADMIN) {
            $query->where('company_id', $user->company_id);
        } elseif ($user->role === User::ROLE_MEMBER) {
            $query->where('user_id', $user->id);
        }

        $viewAll = $request->boolean('view_all') || $request->input('per_page') === 'all';
        $perPage = $viewAll ? 1000 : 2;

        $shortUrls = $query->paginate($perPage)->withQueryString();

        return view('short-urls.index', compact('shortUrls', 'viewAll'));
    }

    public function create(): View
    {
        Gate::authorize('create', ShortUrl::class);

        return view('short-urls.create');
    }

    public function store(
        StoreShortUrlRequest $request,
        ShortUrlService $service
    ): RedirectResponse {
        $service->create(
            $request->user(),
            $request->validated('original_url')
        );

        if ($request->filled('redirect_to')) {
            return redirect($request->input('redirect_to'))
                ->with('status', 'Short URL created successfully.');
        }

        return redirect()
            ->route('short-urls.index')
            ->with('status', 'Short URL created successfully.');
    }

    public function export(Request $request): StreamedResponse
    {
        Gate::authorize('viewAny', ShortUrl::class);

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

        $query = ShortUrl::with(['company', 'user'])->latest();

        if ($user->role === User::ROLE_ADMIN) {
            $query->where('company_id', $user->company_id);
        } elseif ($user->role === User::ROLE_MEMBER) {
            $query->where('user_id', $user->id);
        }

        $this->applyPeriod($query, $period);

        $filename = 'short-urls-' . $period . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($query, $user) {
            $handle = fopen('php://output', 'w');

            $headers = ['Short URL', 'Original URL'];
            if ($user->role === User::ROLE_SUPER_ADMIN) {
                $headers[] = 'Company';
                $headers[] = 'Created By';
            } elseif ($user->role === User::ROLE_ADMIN) {
                $headers[] = 'Created By';
            }
            $headers[] = 'Clicks';
            $headers[] = 'Created At';

            fputcsv($handle, $headers);

            $query->chunk(200, function ($urls) use ($handle, $user) {
                foreach ($urls as $url) {
                    $row = [
                        route('short-urls.redirect', $url->code),
                        $url->original_url,
                    ];
                    if ($user->role === User::ROLE_SUPER_ADMIN) {
                        $row[] = $url->company?->name ?? '—';
                        $row[] = $url->user?->name ?? '—';
                    } elseif ($user->role === User::ROLE_ADMIN) {
                        $row[] = $url->user?->name ?? '—';
                    }
                    $row[] = $url->clicks;
                    $row[] = $url->created_at->format('Y-m-d H:i:s');

                    fputcsv($handle, $row);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
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
                break;
        }
    }
}