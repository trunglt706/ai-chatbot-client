<x-guest-layout>
    <div class="mb-3 text-muted small">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div class="mb-3">
            <x-input-label required for="password" :value="__('Password')" />
            <x-text-input id="password" class="form-control" type="password" name="password" required
                autocomplete="current-password" />
        </div>

        <button type="submit" class="btn btn-primary w-100">
            {{ __('Confirm') }}
        </button>
    </form>
</x-guest-layout>
