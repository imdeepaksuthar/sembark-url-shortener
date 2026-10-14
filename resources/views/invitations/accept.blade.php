<x-guest-layout>
    <div class="mb-5 text-center">
        <h2 class="text-xl font-bold text-gray-900">Accept Invitation</h2>
        <p class="mt-1 text-sm text-gray-600">
            You've been invited to join <span class="font-semibold text-gray-800">{{ $invitation->company->name }}</span> as <span class="font-semibold text-indigo-600">{{ ucfirst(str_replace('_', ' ', $invitation->role)) }}</span>.
        </p>
    </div>

    @auth
        <div class="mb-5 rounded-lg bg-amber-50 border border-amber-200 p-3 text-sm text-amber-800">
            You are currently signed in as <span class="font-semibold">{{ auth()->user()->email }}</span>. Submitting this form will accept the invitation and sign you in as <span class="font-semibold">{{ $invitation->email }}</span>.
        </div>
    @endauth

    <!-- Email Display (Read-only) -->
    <div class="mb-5 rounded-lg bg-gray-50 border border-gray-200 p-3">
        <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Account Email</span>
        <span class="block text-sm font-semibold text-gray-800">{{ $invitation->email }}</span>
    </div>

    <form method="POST" action="{{ route('invitations.accept', ['token' => $token]) }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Create Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-2.5">
                {{ __('Accept & Join') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>