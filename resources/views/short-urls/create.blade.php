<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Generate Short URL</h2>
    </x-slot>

    <div class="p-6">
        @if (session('status'))
            <p>{{ session('status') }}</p>
        @endif

        <form method="POST" action="{{ route('short-urls.store') }}">
            @csrf

            <label for="original_url">Original URL</label>

            <input id="original_url" name="original_url" type="url" required value="{{ old('original_url') }}">

            @error('original_url')
                <p>{{ $message }}</p>
            @enderror

            <button type="submit">Generate</button>
        </form>
    </div>
</x-app-layout>