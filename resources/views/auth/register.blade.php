<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-2">
            <x-input-label for="name" :value="__('Name')" class="form-label" />
            <x-text-input id="name" class="form-control" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="form-text text-danger" />
        </div>

        <!-- Email Address -->
        <div class="mb-2">
            <x-input-label for="email" :value="__('Email')" class="form-label" />
            <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required
                autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="form-text text-danger" />
        </div>

        <!-- Password -->
        <div class="mb-2">
            <x-input-label for="password" :value="__('Password')" class="form-label" />
            <x-text-input id="password" class="form-control" type="password" name="password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="form-text text-danger" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-2">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="form-label" />
            <x-text-input id="password_confirmation" class="form-control" type="password" name="password_confirmation"
                required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="form-text text-danger" />
        </div>

        <!-- Captcha -->
        <div class="mb-2">
            <x-input-label for="captcha" :value="__('Captcha')" class="form-label" />
            <div class="d-flex align-items-center mb-2">
                <div class="captcha-image me-3">{!! captcha_img('flat') !!}</div>
                <button type="button" class="btn btn-outline-secondary btn-sm refresh-captcha-btn">
                    <i class="bi bi-arrow-clockwise"></i> {{ __('Refresh Captcha') }}
                </button>
            </div>
            <x-text-input id="captcha" class="form-control" type="text" name="captcha" required />
            <x-input-error :messages="$errors->get('captcha')" class="form-text text-danger" />
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <a class="text-decoration-none small" href="{{ route('login') }}">
                <i class="bi bi-sign-turn-left"></i> {{ __('Already registered?') }}
            </a>
            <x-primary-button class="btn btn-primary">
                <i class="bi bi-pencil"></i> {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
