<section>
    <header>
        <h2 class="h5 fw-semibold text-dark mb-2">
            {{ __('Update Password') }}
        </h2>
        <p class="mb-4 small text-secondary">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="form-control mt-1" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class="mb-3">
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" class="form-control mt-1"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div class="mb-3">
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="form-control mt-1" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label required for="captcha" :value="__('Captcha')" />
            <div class="d-flex align-items-center mb-2">
                {{-- Hiển thị hình ảnh Captcha --}}
                <div class="captcha-image me-3">{!! captcha_img('flat') !!}</div>
                {{-- Nút refresh Captcha --}}
                <button type="button" class="btn btn-link p-0 text-decoration-none refresh-captcha-btn">
                    {{ __('Refresh Captcha') }}
                </button>
            </div>
            <x-text-input id="captcha" class="form-control mt-1" type="text" name="captcha" required />
            <x-input-error :messages="$errors->get('captcha')" class="mt-2" />
        </div>

        <div class="d-flex align-items-center gap-3">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="small text-success mb-0">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
