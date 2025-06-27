<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success mb-3">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-2">
            <x-input-label required for="email" :value="__('Email')" />
            <x-text-input id="email" class="form-control" type="email" name="email" required autocomplete="email" />
        </div>

        <!-- Password -->
        <div class="mb-2">
            <x-input-label required for="password" :value="__('Password')" />
            <x-text-input id="password" class="form-control" type="password" name="password" required
                autocomplete="password" />
        </div>

        <!-- Captcha -->
        <div class="mb-2">
            <x-input-label required for="captcha" :value="__('Captcha')" />
            <div class="d-flex align-items-center mb-2">
                <div class="captcha-image me-3">{!! captcha_img('flat') !!}</div>
                <button type="button" class="btn btn-outline-secondary btn-sm refresh-captcha-btn">
                    {{ __('Refresh Captcha') }}
                </button>
            </div>
            <input type="text" id="captcha" name="captcha" required
                class="form-control @error('captcha') is-invalid @enderror">
            @error('captcha')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <!-- Remember Me -->
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                <label class="form-check-label small" for="remember_me">
                    {{ __('Remember me') }}
                </label>
            </div>
            @if (Route::has('password.request'))
                <a class="text-decoration-none small" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <!-- Buttons -->
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-box-arrow-in-right"></i> {{ __('Log in') }}
        </button>
    </form>

    <!-- Social login buttons (unchanged, depends on your custom component) -->
    <x-social-group-button />
</x-guest-layout>
