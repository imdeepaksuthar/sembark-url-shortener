
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    @if ($role === 'super_admin')
                        Super Admin Dashboard
                    @elseif ($role === 'admin')
                        Client Admin Dashboard
                    @else
                        Client Member Dashboard
                    @endif
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Manage companies, short URLs and activity.
                </p>
            </div>

            <span class="rounded-full bg-indigo-50 px-3 py-1
                         text-sm font-medium text-indigo-700">
                {{ str_replace('_', ' ', ucfirst($role)) }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

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
                     class="relative overflow-hidden rounded-xl border border-emerald-200/80 bg-emerald-50 px-4 py-3.5 text-sm text-emerald-900 shadow-sm">
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

                    <!-- Auto-dismiss animated progress bar -->
                    <div class="absolute bottom-0 left-0 h-0.5 bg-emerald-500/40 w-full transition-all duration-[5000ms] ease-linear"
                         x-init="$nextTick(() => { $el.style.width = '0%'; })"
                         style="width: 100%;"></div>
                </div>
            @endif

            @if ($errors->any())
                <div x-data="{ show: true }"
                     x-init="setTimeout(() => show = false, 7000)"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-300 transform"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="relative overflow-hidden rounded-xl border border-rose-200/80 bg-rose-50 px-4 py-3.5 text-sm text-rose-900 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-rose-100 text-rose-600 mt-0.5">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-rose-900">Please correct the following errors:</p>
                                <ul class="mt-1 list-inside list-disc space-y-0.5 text-rose-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <button type="button"
                                @click="show = false"
                                class="rounded-lg p-1.5 text-rose-500 hover:bg-rose-100 hover:text-rose-700 transition"
                                title="Dismiss error alert">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Auto-dismiss animated progress bar -->
                    <div class="absolute bottom-0 left-0 h-0.5 bg-rose-500/40 w-full transition-all duration-[7000ms] ease-linear"
                         x-init="$nextTick(() => { $el.style.width = '0%'; })"
                         style="width: 100%;"></div>
                </div>
            @endif

            <!-- Summary cards -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 @if ($role === 'super_admin') lg:grid-cols-4 @elseif ($role === 'admin') lg:grid-cols-3 @else lg:grid-cols-2 @endif">
                @if ($role === 'super_admin')
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">
                            Total Companies
                        </p>
                        <p class="mt-3 text-3xl font-bold text-gray-900">
                            {{ number_format($totalCompanies) }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">
                            Total System Users
                        </p>
                        <p class="mt-3 text-3xl font-bold text-gray-900">
                            {{ number_format($totalUsers) }}
                        </p>
                    </div>
                @elseif ($role === 'admin')
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">
                            Team Members
                        </p>
                        <p class="mt-3 text-3xl font-bold text-gray-900">
                            {{ number_format($totalUsers) }}
                        </p>
                    </div>
                @endif

                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        {{ $role === 'member' ? 'My Short URLs' : 'Total Short URLs' }}
                    </p>
                    <p class="mt-3 text-3xl font-bold text-gray-900">
                        {{ number_format($totalUrls) }}
                    </p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        {{ $role === 'member' ? 'My URL Clicks' : 'Total URL Clicks' }}
                    </p>
                    <p class="mt-3 text-3xl font-bold text-gray-900">
                        {{ number_format($totalClicks) }}
                    </p>
                </div>
            </div>

            <!-- SUPER ADMIN: Companies -->
            @if ($role === 'super_admin')
                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-6 py-5">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Clients
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Manage companies using the service.
                            </p>
                        </div>

                        <button type="button"
                                x-data=""
                                x-on:click.prevent="$dispatch('select-company', { companyId: '', companyName: '', isDirect: false }); $dispatch('open-modal', 'invite-client-modal')"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                            + Invite Client
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Company
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Users
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Generated URLs
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Total Clicks
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                                        Actions
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200">
                                @forelse ($companies as $company)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <p class="font-medium text-gray-900">
                                                {{ $company->name }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ $company->slug }}
                                            </p>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $company->users_count }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $company->short_urls_count }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ number_format($company->short_urls_sum_clicks ?? 0) }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                            <button type="button"
                                                    x-data=""
                                                    x-on:click.prevent="$dispatch('select-company', { companyId: '{{ $company->id }}', companyName: '{{ addslashes($company->name) }}', isDirect: true }); $dispatch('open-modal', 'invite-client-modal')"
                                                    class="inline-flex items-center rounded-md bg-indigo-50 px-2.5 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition">
                                                + Invite Admin
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                            No companies have been created yet.
                                            <button type="button"
                                                    x-data=""
                                                    x-on:click.prevent="$dispatch('select-company', { companyId: '', companyName: '', isDirect: false }); $dispatch('open-modal', 'invite-client-modal')"
                                                    class="font-medium text-indigo-600 hover:text-indigo-800 underline">
                                                Invite Client
                                            </button> to get started.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($companies->total() > 0)
                        <div class="flex flex-wrap items-center justify-between gap-4 border-t border-gray-200 px-6 py-3.5 bg-gray-50/50">
                            <div class="text-xs font-medium text-gray-600">
                                Showing <span class="font-bold text-gray-900">{{ $companies->count() }}</span> of total <span class="font-bold text-gray-900">{{ $companies->total() }}</span>
                            </div>

                            <div class="flex items-center gap-3">
                                @if ($companies->total() > 2)
                                    @if (!empty($viewAllCompanies))
                                        <a href="{{ request()->fullUrlWithQuery(['view_all_companies' => null, 'companies_page' => 1]) }}"
                                           class="inline-flex items-center gap-1.5 rounded-lg border border-blue-600 bg-blue-50 px-3.5 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition shadow-sm">
                                            <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                            <span>Show Less (2)</span>
                                        </a>
                                    @else
                                        <a href="{{ request()->fullUrlWithQuery(['view_all_companies' => 1, 'companies_page' => null]) }}"
                                           class="inline-flex items-center gap-1.5 rounded-lg border border-blue-600 bg-blue-100 px-3.5 py-1.5 text-xs font-bold text-blue-800 hover:bg-blue-200 hover:border-blue-700 transition shadow-sm">
                                            <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                            </svg>
                                            <span>View All</span>
                                        </a>
                                    @endif
                                @endif

                                @if (empty($viewAllCompanies) && $companies->hasPages())
                                    {{ $companies->links() }}
                                @endif
                            </div>
                        </div>
                    @endif
                </section>

                <!-- SUPER ADMIN: System Users -->
                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">
                            System Users
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            All registered users and activity across every company.
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Company
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Role
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        URLs
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Clicks
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($allUsers as $systemUser)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $systemUser->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $systemUser->email }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $systemUser->company?->name ?? 'Platform (System)' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                                @if ($systemUser->role === 'super_admin') bg-indigo-100 text-indigo-800
                                                @elseif ($systemUser->role === 'admin') bg-purple-100 text-purple-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst(str_replace('_', ' ', $systemUser->role)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $systemUser->short_urls_count }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ number_format($systemUser->short_urls_sum_clicks ?? 0) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($allUsers->total() > 0)
                        <div class="flex flex-wrap items-center justify-between gap-4 border-t border-gray-200 px-6 py-3.5 bg-gray-50/50">
                            <div class="text-xs font-medium text-gray-600">
                                Showing <span class="font-bold text-gray-900">{{ $allUsers->count() }}</span> of total <span class="font-bold text-gray-900">{{ $allUsers->total() }}</span>
                            </div>

                            <div class="flex items-center gap-3">
                                @if ($allUsers->total() > 2)
                                    @if (!empty($viewAllUsers))
                                        <a href="{{ request()->fullUrlWithQuery(['view_all_users' => null, 'users_page' => 1]) }}"
                                           class="inline-flex items-center gap-1.5 rounded-lg border border-blue-600 bg-blue-50 px-3.5 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition shadow-sm">
                                            <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                            <span>Show Less (2)</span>
                                        </a>
                                    @else
                                        <a href="{{ request()->fullUrlWithQuery(['view_all_users' => 1, 'users_page' => null]) }}"
                                           class="inline-flex items-center gap-1.5 rounded-lg border border-blue-600 bg-blue-100 px-3.5 py-1.5 text-xs font-bold text-blue-800 hover:bg-blue-200 hover:border-blue-700 transition shadow-sm">
                                            <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                            </svg>
                                            <span>View All</span>
                                        </a>
                                    @endif
                                @endif

                                @if (empty($viewAllUsers) && $allUsers->hasPages())
                                    {{ $allUsers->links() }}
                                @endif
                            </div>
                        </div>
                    @endif
                </section>

                <!-- Invite Client Modal -->
                <script>
                    window.allCompaniesMap = @json($allCompanies->pluck('name', 'id'));
                </script>
                <x-modal name="invite-client-modal" :show="$errors->has('company_name') || $errors->has('email') || $errors->has('company_id')" focusable>
                    <div class="p-6" x-data="{
                        isDirect: {{ (old('company_id') && !old('company_name')) ? 'true' : 'false' }},
                        companyName: '{{ old('company_id') ? addslashes($allCompanies->firstWhere('id', old('company_id'))?->name ?? '') : '' }}',
                        inviteType: '{{ old('company_id') ? 'existing' : 'new' }}',
                        selectedCompany: '{{ old('company_id', '') }}',
                        companiesMap: window.allCompaniesMap || {}
                    }"
                    x-on:select-company.window="
                        isDirect = Boolean($event.detail && $event.detail.isDirect);
                        selectedCompany = ($event.detail && $event.detail.companyId) ? String($event.detail.companyId) : '';
                        companyName = ($event.detail && $event.detail.companyName) ? $event.detail.companyName : (companiesMap[selectedCompany] || '');
                        inviteType = isDirect ? 'existing' : (selectedCompany ? 'existing' : 'new');
                    ">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    <span x-show="isDirect">Invite Admin to <span x-text="companyName"></span></span>
                                    <span x-show="!isDirect">Invite New Client</span>
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    <span x-show="isDirect">Send an email invitation to an administrator for <span class="font-medium text-gray-700" x-text="companyName"></span>.</span>
                                    <span x-show="!isDirect">Create a company and invite its first Admin, or invite an Admin to an existing company.</span>
                                </p>
                            </div>

                            <button type="button"
                                    x-on:click="$dispatch('close')"
                                    class="rounded-md p-1 text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Direct Company Banner (When inviting directly from company row) -->
                        <div x-show="isDirect" class="mt-4 rounded-lg bg-indigo-50 border border-indigo-100 p-3.5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="block text-xs font-semibold uppercase tracking-wider text-indigo-600">Company</span>
                                    <span class="text-base font-bold text-gray-900" x-text="companyName"></span>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800">
                                    Existing Client
                                </span>
                            </div>
                        </div>

                        @if ($allCompanies->isNotEmpty())
                            <div x-show="!isDirect" class="mt-4 flex gap-4 border-b border-gray-200 pb-3 text-sm">
                                <label class="flex items-center gap-2 cursor-pointer font-medium text-gray-700">
                                    <input type="radio" name="modal_type_choice" value="new" x-model="inviteType" class="text-indigo-600 focus:ring-indigo-500">
                                    <span>Create New Company</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer font-medium text-gray-700">
                                    <input type="radio" name="modal_type_choice" value="existing" x-model="inviteType" class="text-indigo-600 focus:ring-indigo-500">
                                    <span>Existing Company</span>
                                </label>
                            </div>
                        @endif

                        <form method="POST"
                              action="{{ route('invitations.company-admin') }}"
                              class="mt-4 space-y-4">
                            @csrf

                            <!-- Hidden company_id when inviting directly from row -->
                            <input type="hidden" name="company_id" :value="selectedCompany" x-bind:disabled="!isDirect">

                            <!-- Create New Company fields (only when not direct and inviteType is new) -->
                            <div x-show="!isDirect && inviteType === 'new'">
                                <label for="modal_company_name"
                                       class="mb-1 block text-sm font-medium text-gray-700">
                                    Company Name
                                </label>
                                <input id="modal_company_name"
                                       name="company_name"
                                       value="{{ old('company_name') }}"
                                       :required="!isDirect && inviteType === 'new'"
                                       :disabled="isDirect || inviteType !== 'new'"
                                       placeholder="Acme Corp"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @error('company_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Existing Company select dropdown (only when not direct and inviteType is existing) -->
                            @if ($allCompanies->isNotEmpty())
                                <div x-show="!isDirect && inviteType === 'existing'" style="display: none;">
                                    <label for="modal_company_id"
                                           class="mb-1 block text-sm font-medium text-gray-700">
                                        Select Company
                                    </label>
                                    <select id="modal_company_id"
                                            name="company_id"
                                            x-model="selectedCompany"
                                            x-on:change="companyName = companiesMap[selectedCompany] || ''"
                                            :required="!isDirect && inviteType === 'existing'"
                                            :disabled="isDirect || inviteType !== 'existing'"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <option value="">-- Choose Company --</option>
                                        @foreach ($allCompanies as $comp)
                                            <option value="{{ $comp->id }}" @selected(old('company_id') == $comp->id)>
                                                {{ $comp->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <div>
                                <label for="modal_client_email"
                                       class="mb-1 block text-sm font-medium text-gray-700">
                                    Admin Email
                                </label>
                                <input id="modal_client_email"
                                       type="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       :required="isDirect || inviteType === 'existing'"
                                       placeholder="admin@acme.com"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <p class="mt-1 text-xs text-gray-500" x-show="isDirect">
                                    Enter the email of the administrator you want to invite to <strong x-text="companyName"></strong>.
                                </p>
                                <p class="mt-1 text-xs text-gray-500" x-show="!isDirect && inviteType === 'new'">
                                    Leave blank to create company only, or enter email to invite its Admin.
                                </p>
                                <p class="mt-1 text-xs text-gray-500" x-show="!isDirect && inviteType === 'existing'">
                                    Enter the email of the administrator to invite to the selected company.
                                </p>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4">
                                <button type="button"
                                        x-on:click="$dispatch('close')"
                                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none">
                                    <span x-show="isDirect">Send Invitation</span>
                                    <span x-show="!isDirect">Save & Send</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </x-modal>
            @endif

            <!-- ADMIN: Team members -->
            @if ($role === 'admin')
                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-6 py-5">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Team Members
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Manage team members and their roles.
                            </p>
                        </div>

                        <button type="button"
                                x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'invite-team-modal')"
                                class="rounded-md bg-indigo-600 px-4 py-2
                                       text-sm font-semibold text-white
                                       hover:bg-indigo-700 transition">
                            + Invite Team Member
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs
                                               font-semibold uppercase text-gray-500">
                                        Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs
                                               font-semibold uppercase text-gray-500">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs
                                               font-semibold uppercase text-gray-500">
                                        Role
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs
                                               font-semibold uppercase text-gray-500">
                                        URLs
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs
                                               font-semibold uppercase text-gray-500">
                                        Clicks
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($teamMembers as $member)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $member->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $member->email }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $member->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($member->role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $member->short_urls_count }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ number_format($member->short_urls_sum_clicks ?? 0) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                            No team members found.
                                            <button type="button"
                                                    x-data=""
                                                    x-on:click.prevent="$dispatch('open-modal', 'invite-team-modal')"
                                                    class="font-medium text-indigo-600 hover:text-indigo-800 underline">
                                                Invite Team Member
                                            </button> to get started.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($teamMembers->total() > 0)
                        <div class="flex flex-wrap items-center justify-between gap-4 border-t border-gray-200 px-6 py-3.5 bg-gray-50/50">
                            <div class="text-xs font-medium text-gray-600">
                                Showing <span class="font-bold text-gray-900">{{ $teamMembers->count() }}</span> of total <span class="font-bold text-gray-900">{{ $teamMembers->total() }}</span>
                            </div>

                            <div class="flex items-center gap-3">
                                @if ($teamMembers->total() > 2)
                                    @if (!empty($viewAllMembers))
                                        <a href="{{ request()->fullUrlWithQuery(['view_all_members' => null, 'members_page' => 1]) }}"
                                           class="inline-flex items-center gap-1.5 rounded-lg border border-blue-600 bg-blue-50 px-3.5 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition shadow-sm">
                                            <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                            <span>Show Less (2)</span>
                                        </a>
                                    @else
                                        <a href="{{ request()->fullUrlWithQuery(['view_all_members' => 1, 'members_page' => null]) }}"
                                           class="inline-flex items-center gap-1.5 rounded-lg border border-blue-600 bg-blue-100 px-3.5 py-1.5 text-xs font-bold text-blue-800 hover:bg-blue-200 hover:border-blue-700 transition shadow-sm">
                                            <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                            </svg>
                                            <span>View All</span>
                                        </a>
                                    @endif
                                @endif

                                @if (empty($viewAllMembers) && $teamMembers->hasPages())
                                    {{ $teamMembers->links() }}
                                @endif
                            </div>
                        </div>
                    @endif
                </section>

                <!-- Invite Team Member Modal -->
                <x-modal name="invite-team-modal" :show="$errors->has('email') || $errors->has('role')" focusable>
                    <div class="p-6">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Invite New Team Member
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Send an invitation to join your company team.
                                </p>
                            </div>

                            <button type="button"
                                    x-on:click="$dispatch('close')"
                                    class="rounded-md p-1 text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form method="POST"
                              action="{{ route('invitations.team') }}"
                              class="mt-5 space-y-4">
                            @csrf

                            <div>
                                <label for="modal_team_email"
                                       class="mb-1 block text-sm font-medium text-gray-700">
                                    Email Address
                                </label>
                                <input id="modal_team_email"
                                       type="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       required
                                       placeholder="colleague@example.com"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="modal_team_role"
                                       class="mb-1 block text-sm font-medium text-gray-700">
                                    Role
                                </label>
                                <select id="modal_team_role"
                                        name="role"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="member" @selected(old('role') === 'member')>Member</option>
                                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                                </select>
                                @error('role')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4">
                                <button type="button"
                                        x-on:click="$dispatch('close')"
                                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none">
                                    Send Invitation
                                </button>
                            </div>
                        </form>
                    </div>
                </x-modal>
            @endif

            <!-- Generated URLs: Super Admin, Admin and Member -->
            <script>
                window.shortUrlsManager = window.shortUrlsManager || function(initialPeriod, dashboardUrl) {
                    return {
                        period: initialPeriod,
                        loading: false,
                        init() {
                            window.addEventListener('popstate', () => {
                                const params = new URLSearchParams(window.location.search);
                                this.period = params.get('period') || 'this_month';
                                this.applyFilter(window.location.href, false);
                            });
                        },
                        async applyFilter(url = null, push = true) {
                            this.loading = true;
                            const targetUrl = url || `${dashboardUrl}?period=${this.period}`;
                            try {
                                const response = await fetch(targetUrl, {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json',
                                    }
                                });
                                if (response.ok) {
                                    const data = await response.json();
                                    if (this.$refs.tableContainer) {
                                        this.$refs.tableContainer.innerHTML = data.html;
                                    }
                                    if (data.exportUrl && this.$refs.exportBtn) {
                                        this.$refs.exportBtn.href = data.exportUrl;
                                    }
                                    if (push && window.location.href !== targetUrl) {
                                        window.history.pushState({}, '', targetUrl);
                                    }
                                }
                            } catch (error) {
                                console.error('Filter request failed', error);
                            } finally {
                                this.loading = false;
                            }
                        },
                        handlePagination(event) {
                            const anchor = event.target.closest('a');
                            if (!anchor) return;

                            if (anchor.target === '_blank' || anchor.hasAttribute('download')) {
                                return;
                            }

                            const isPaginationLink = anchor.closest("nav[role='navigation']") ||
                                                     anchor.closest('nav') ||
                                                     anchor.closest('.pagination') ||
                                                     (anchor.search && (anchor.search.includes('page=') || anchor.search.includes('view_all=')));

                            if (isPaginationLink && anchor.href && this.$refs.tableContainer && this.$refs.tableContainer.contains(anchor)) {
                                event.preventDefault();
                                this.applyFilter(anchor.href);
                            }
                        }
                    };
                };
            </script>

            <section x-data="shortUrlsManager('{{ $period }}', '{{ route('dashboard') }}')"
                     class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
                <!-- Card Header -->
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200/80 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-slate-900">
                                    Generated Short URLs
                                </h3>
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600">
                                    {{ $shortUrls->total() ?? count($shortUrls) }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                @if ($role === 'super_admin')
                                    Network short URLs created across every company.
                                @elseif ($role === 'admin')
                                    Tracked short URLs generated within your company.
                                @else
                                    Short URLs created by your account.
                                @endif
                            </p>
                        </div>
                    </div>

                    @if ($role !== 'super_admin')
                        <button type="button"
                                x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'generate-short-url-modal')"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm shadow-indigo-600/20 hover:bg-indigo-700 active:scale-95 transition-all">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span>Generate Short URL</span>
                        </button>
                    @endif
                </div>

                <!-- Integrated Filter & Export Toolbar -->
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200/80 bg-slate-50/70 px-6 py-3.5">
                    <form @submit.prevent="applyFilter()"
                          class="flex flex-wrap items-center gap-2.5">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-slate-500 flex items-center gap-1">
                                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Period:
                            </span>
                            <select id="period"
                                    name="period"
                                    x-model="period"
                                    x-on:change="applyFilter()"
                                    class="rounded-xl border-slate-200 bg-white py-1.5 px-3 text-xs font-medium text-slate-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="this_month">This Month</option>
                                <option value="last_month">Last Month</option>
                                <option value="last_week">Last Week</option>
                                <option value="today">Today</option>
                                <option value="all">All Time</option>
                            </select>
                        </div>

                        <button type="submit"
                                :disabled="loading"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 shadow-sm disabled:opacity-50 transition">
                            <svg x-show="loading" class="animate-spin h-3.5 w-3.5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span x-text="loading ? 'Applying...' : 'Apply'">Apply</span>
                        </button>
                    </form>

                    <a x-ref="exportBtn"
                       href="{{ route('short-urls.export', ['period' => $period]) }}"
                       class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 shadow-sm transition">
                        <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Download CSV</span>
                    </a>
                </div>

                <div class="relative min-h-[160px]">
                    <!-- Loading overlay -->
                    <div x-show="loading"
                         x-transition:enter="ease-out duration-150"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="absolute inset-0 bg-white/80 backdrop-blur-[2px] flex items-center justify-center z-10"
                         style="display: none;">
                        <div class="flex items-center gap-2.5 px-4 py-2.5 bg-white rounded-2xl shadow-lg border border-slate-200/80 text-xs font-semibold text-slate-700">
                            <svg class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Loading URLs...</span>
                        </div>
                    </div>

                    <div x-ref="tableContainer" @click="handlePagination($event)">
                        @include('dashboard.partials.urls-table')
                    </div>
                </div>
            </section>

            <!-- Generate Short URL Modal -->
            @if ($role !== 'super_admin')
                <x-modal name="generate-short-url-modal" :show="$errors->has('original_url')" focusable>
                    <div class="p-6 sm:p-7">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-5">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">
                                        Generate Short URL
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Turn a long URL into an instantaneous, trackable short link.
                                    </p>
                                </div>
                            </div>

                            <button type="button"
                                    x-on:click="$dispatch('close')"
                                    class="rounded-xl p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form method="POST"
                              action="{{ route('short-urls.store') }}"
                              class="mt-6 space-y-4">
                            @csrf
                            <input type="hidden" name="redirect_to" value="{{ route('dashboard') }}">

                            <div>
                                <label for="modal_original_url"
                                       class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600">
                                    Destination URL <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative rounded-xl shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9" />
                                        </svg>
                                    </div>
                                    <input id="modal_original_url"
                                           type="url"
                                           name="original_url"
                                           value="{{ old('original_url') }}"
                                           required
                                           placeholder="https://example.com/your-destination-landing-page"
                                           class="w-full rounded-xl border-slate-200 pl-10 pr-4 py-2.5 text-sm placeholder-slate-400 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                </div>
                                <p class="mt-1.5 text-[11px] text-slate-400">
                                    Include full protocol like <span class="font-mono text-slate-500">https://</span>. The short link will automatically redirect visitors here.
                                </p>
                                @error('original_url')
                                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-6 flex justify-end gap-3 border-t border-slate-100 pt-5">
                                <button type="button"
                                        x-on:click="$dispatch('close')"
                                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition focus:outline-none shadow-sm">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-semibold text-white shadow-sm shadow-indigo-600/20 hover:bg-indigo-700 active:scale-95 transition-all focus:outline-none">
                                    Generate Link
                                </button>
                            </div>
                        </form>
                    </div>
                </x-modal>
            @endif

        </div>
    </div>
</x-app-layout>
