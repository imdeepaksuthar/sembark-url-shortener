<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200/80">
        <thead class="bg-slate-50/80 text-slate-500">
            <tr>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        Short Link
                    </span>
                </th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9" />
                        </svg>
                        Destination URL
                    </span>
                </th>
                @if ($role === 'super_admin')
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">
                        Company
                    </th>
                @endif
                @if ($role === 'super_admin' || $role === 'admin')
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">
                        Created By
                    </th>
                @endif
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                        </svg>
                        Clicks
                    </span>
                </th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">
                    Date Created
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($shortUrls as $url)
                <tr class="hover:bg-slate-50/70 transition-colors duration-150 group">
                    <!-- Short URL -->
                    <td class="whitespace-nowrap px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="inline-flex items-center gap-1.5 bg-indigo-50/70 border border-indigo-100 px-3 py-1.5 rounded-xl">
                                <a class="font-semibold text-sm text-indigo-600 hover:text-indigo-800 transition-colors inline-flex items-center gap-1.5"
                                   href="{{ route('short-urls.redirect', $url->code) }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   title="Open short link">
                                    <span>{{ url('/r/' . $url->code) }}</span>
                                    <svg class="h-3.5 w-3.5 text-indigo-400 flex-shrink-0 group-hover:text-indigo-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>

                                <!-- Copy button with instant feedback -->
                                <button type="button"
                                        x-data="{ copied: false }"
                                        x-on:click.stop="navigator.clipboard.writeText('{{ route('short-urls.redirect', $url->code) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="relative p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-white transition shadow-none hover:shadow-sm"
                                        title="Copy to clipboard">
                                    <span x-show="!copied" class="flex items-center">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </span>
                                    <span x-show="copied" style="display: none;" class="flex items-center gap-1 text-[11px] font-bold text-emerald-600">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                        <span>Copied</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </td>

                    <!-- Original URL -->
                    <td class="max-w-md px-6 py-4">
                        @php
                            $parsedHost = parse_url($url->original_url, PHP_URL_HOST) ?? 'Destination URL';
                            $parsedPath = parse_url($url->original_url, PHP_URL_PATH) ?? '';
                            if ($queryStr = parse_url($url->original_url, PHP_URL_QUERY)) {
                                $parsedPath .= '?' . $queryStr;
                            }
                        @endphp
                        <div class="flex items-start gap-2">
                            <div class="mt-0.5 p-1 rounded-md bg-slate-100 text-slate-500 shrink-0">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <a class="font-medium text-xs text-slate-800 hover:text-indigo-600 hover:underline flex items-center gap-1 group/link"
                                   href="{{ $url->original_url }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   title="{{ $url->original_url }}">
                                    <span class="font-semibold text-slate-900 truncate">{{ $parsedHost }}</span>
                                    <span class="text-slate-400 truncate max-w-xs">{{ $parsedPath }}</span>
                                </a>
                                <p class="text-[11px] text-slate-400 truncate mt-0.5 max-w-xs font-mono">
                                    {{ Str::limit($url->original_url, 60) }}
                                </p>
                            </div>
                        </div>
                    </td>

                    @if ($role === 'super_admin')
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-700">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-medium">
                                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                {{ $url->company?->name ?? '—' }}
                            </span>
                        </td>
                    @endif

                    @if ($role === 'super_admin' || $role === 'admin')
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">
                            @if ($url->user)
                                @php
                                    $creatorInitials = collect(explode(' ', $url->user->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                                @endphp
                                <div class="flex items-center gap-2">
                                    <div class="h-6 w-6 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px] flex items-center justify-center border border-slate-200">
                                        {{ $creatorInitials ?: 'U' }}
                                    </div>
                                    <span class="font-medium text-xs text-slate-800">{{ $url->user->name }}</span>
                                </div>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                    @endif

                    <!-- Clicks Badge -->
                    <td class="whitespace-nowrap px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold tabular-nums {{ $url->clicks > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-slate-100 text-slate-600 border border-slate-200/60' }}">
                            <svg class="h-3 w-3 {{ $url->clicks > 0 ? 'text-emerald-500' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5" />
                            </svg>
                            <span>{{ number_format($url->clicks) }} {{ Str::plural('click', $url->clicks) }}</span>
                        </span>
                    </td>

                    <!-- Created Date -->
                    <td class="whitespace-nowrap px-6 py-4 text-xs text-slate-500">
                        <div class="flex flex-col">
                            <span class="font-medium text-slate-700">{{ $url->created_at->format('d M Y') }}</span>
                            <span class="text-[11px] text-slate-400">{{ $url->created_at->diffForHumans() }}</span>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $role === 'super_admin' ? 6 : ($role === 'admin' ? 5 : 4) }}"
                        class="px-6 py-12 text-center">
                        <div class="max-w-sm mx-auto flex flex-col items-center justify-center text-center">
                            <div class="h-12 w-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-500 mb-3 shadow-sm">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-slate-800">No short URLs found</h4>
                            <p class="text-xs text-slate-500 mt-1">
                                No links match the selected date range. Try changing your filter or generate a new short URL.
                            </p>
                            @if ($role !== 'super_admin')
                                <button type="button"
                                        x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'generate-short-url-modal')"
                                        class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Generate Short URL
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($shortUrls->total() > 0)
    <div class="flex flex-wrap items-center justify-between gap-4 border-t border-slate-200/80 px-6 py-3.5 bg-slate-50/60">
        <div class="text-xs font-medium text-slate-600">
            Showing <span class="font-bold text-slate-800">{{ $shortUrls->count() }}</span> of total <span class="font-bold text-slate-800">{{ $shortUrls->total() }}</span>
        </div>

        <div class="flex items-center gap-3">
            @if ($shortUrls->total() > 2)
                @if (!empty($viewAll))
                    <a href="{{ request()->fullUrlWithQuery(['view_all' => null, 'urls_page' => 1]) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg border border-blue-600 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition shadow-sm">
                        <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <span>Show Less (2)</span>
                    </a>
                @else
                    <a href="{{ request()->fullUrlWithQuery(['view_all' => 1, 'urls_page' => null]) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg border border-blue-600 bg-blue-100 px-3.5 py-1.5 text-xs font-bold text-blue-800 hover:bg-blue-200 hover:border-blue-700 transition shadow-sm">
                        <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <span>View All</span>
                    </a>
                @endif
            @endif

            @if (empty($viewAll) && $shortUrls->hasPages())
                {{ $shortUrls->links() }}
            @endif
        </div>
    </div>
@endif