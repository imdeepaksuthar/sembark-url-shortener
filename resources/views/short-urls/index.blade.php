<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Generated Short URLs</h2>
    </x-slot>

    <div class="p-6">
        @if (session('status'))
            <div x-data="{ show: true }"
                 x-init="setTimeout(() => show = false, 5000)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-300 transform"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="relative mb-6 overflow-hidden rounded-xl border border-emerald-200/80 bg-emerald-50 px-4 py-3.5 text-sm text-emerald-900 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="font-medium text-emerald-900">
                            {{ session('status') }}
                        </p>
                    </div>

                    <button type="button"
                            @click="show = false"
                            class="rounded-lg p-1.5 text-emerald-500 hover:bg-emerald-100 hover:text-emerald-700 transition"
                            title="Dismiss notification">
                        <span class="sr-only">Dismiss</span>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="absolute bottom-0 left-0 h-0.5 bg-emerald-500/40 w-full transition-all duration-[5000ms] ease-linear"
                     x-init="$nextTick(() => { $el.style.width = '0%'; })"
                     style="width: 100%;"></div>
            </div>
        @endif

        @can('create', App\Models\ShortUrl::class)
            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'generate-short-url-modal')"
                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                + Generate Short URL
            </button>
        @endcan

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Short URL</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Original URL</th>
                        @if (Auth::user()?->role === 'super_admin')
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Company</th>
                        @endif
                        @if (Auth::user()?->role === 'super_admin' || Auth::user()?->role === 'admin')
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Created By</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Clicks</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Created</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse ($shortUrls as $url)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <a class="font-medium text-indigo-600 hover:text-indigo-800 hover:underline inline-flex items-center gap-1"
                                        href="{{ route('short-urls.redirect', $url->code) }}" target="_blank"
                                        rel="noopener noreferrer">
                                        <span>{{ route('short-urls.redirect', $url->code) }}</span>
                                        <svg class="h-3.5 w-3.5 text-indigo-400 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                    <button type="button" x-data="{ copied: false }"
                                        x-on:click.stop="navigator.clipboard.writeText('{{ route('short-urls.redirect', $url->code) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="text-gray-400 hover:text-gray-600 p-1 rounded hover:bg-gray-100 transition"
                                        title="Copy Short URL">
                                        <span x-show="!copied">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </span>
                                        <span x-show="copied" style="display: none;"
                                            class="text-xs font-medium text-green-600">
                                            Copied!
                                        </span>
                                    </button>
                                </div>
                            </td>

                            <td class="max-w-sm break-all px-6 py-4 text-sm text-gray-600">
                                <a class="text-gray-600 hover:text-indigo-600 hover:underline inline-flex items-center gap-1"
                                    href="{{ $url->original_url }}" target="_blank" rel="noopener noreferrer"
                                    title="{{ $url->original_url }}">
                                    <span>{{ $url->original_url }}</span>
                                    <svg class="h-3 w-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </td>
                            @if (Auth::user()?->role === 'super_admin')
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $url->company?->name ?? '—' }}</td>
                            @endif
                            @if (Auth::user()?->role === 'super_admin' || Auth::user()?->role === 'admin')
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $url->user?->name ?? '—' }}</td>
                            @endif
                            <td class="px-6 py-4 text-sm">{{ number_format($url->clicks) }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">{{ $url->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            @php
                                $colspan = Auth::user()?->role === 'super_admin' ? 6 : (Auth::user()?->role === 'admin' ? 5 : 4);
                               @endphp
                            <td colspan="{{ $colspan }}" class="px-6 py-8 text-center text-sm text-gray-500">No URLs created
                                yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($shortUrls->total() > 0)
            <div class="mt-4 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="text-sm font-medium text-gray-600">
                    Showing <span class="font-semibold text-gray-900">{{ $shortUrls->count() }}</span> of total <span class="font-semibold text-gray-900">{{ $shortUrls->total() }}</span>
                </div>

                <div class="flex items-center gap-3">
                    @if ($shortUrls->total() > 2)
                        @if (!empty($viewAll))
                            <a href="{{ request()->fullUrlWithQuery(['view_all' => null, 'page' => 1]) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg border border-blue-600 bg-blue-50 px-3.5 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition shadow-sm">
                                <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                <span>Show Less (2)</span>
                            </a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['view_all' => 1, 'page' => null]) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg border border-blue-600 bg-blue-100 px-3.5 py-1.5 text-xs font-bold text-blue-800 hover:bg-blue-200 hover:border-blue-700 transition shadow-sm">
                                <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                                <span>View All</span>
                            </a>
                        @endif
                    @endif

                    @if (empty($viewAll) && $shortUrls->hasPages())
                        <div class="inline-flex items-center">
                            {{ $shortUrls->links() }}
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @can('create', App\Models\ShortUrl::class)

            <x-modal name="generate-short-url-modal" :show="$errors->has('original_url')" focusable>
                <div class="p-6">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Generate Short URL
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Enter a destination URL to generate a short link.
                            </p>
                        </div>

                        <button type="button" x-on:click="$dispatch('close')"
                            class="rounded-md p-1 text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('short-urls.store') }}" class="mt-5 space-y-4">
                        @csrf

                        <div>
                            <label for="index_modal_original_url" class="mb-1 block text-sm font-medium text-gray-700">
                                Original URL
                            </label>
                            <input id="index_modal_original_url" type="url" name="original_url"
                                value="{{ old('original_url') }}" required placeholder="https://example.com/long-page-url"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('original_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4">
                            <button type="button" x-on:click="$dispatch('close')"
                                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                                Cancel
                            </button>
                            <button type="submit"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none">
                                Generate
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>
        @endcan
    </div>
</x-app-layout>