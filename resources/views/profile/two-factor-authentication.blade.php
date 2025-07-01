<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 font-weight-bold">
            {{ __('Two-Factor Authentication') }}
        </h2>
    </x-slot>

    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Two-Factor Authentication') }}</h5>
            </div>
            <div class="card-body">
                <h3 class="text-lg font-medium text-gray-900">
                    @if (Auth::user()->two_factor_secret && !Auth::user()->two_factor_confirmed_at)
                        {{ __('Finish enabling two factor authentication.') }}
                    @else
                        {{ __('Two Factor Authentication') }}
                    @endif
                </h3>

                <p class="mt-3 text-sm text-gray-600">
                    {{ __('Add additional security to your account using two factor authentication.') }}
                </p>

                @if (session('status') == 'two-factor-authentication-enabled')
                    <div class="mb-4 font-medium text-sm text-success">
                        {{ __('Two factor authentication has been enabled.') }}
                    </div>
                @endif

                @if (session('status') == 'two-factor-authentication-disabled')
                    <div class="mb-4 font-medium text-sm text-success">
                        {{ __('Two factor authentication has been disabled.') }}
                    </div>
                @endif

                <div class="mt-5">
                    @if (!Auth::user()->two_factor_secret)
                        {{-- Enable 2FA --}}
                        <form method="POST" action="{{ url('user/two-factor-authentication') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                {{ __('Enable') }}
                            </button>
                        </form>
                    @else
                        {{-- QR Code (chỉ hiển thị khi 2FA được bật và chưa xác nhận) --}}
                        @if (Auth::user()->two_factor_secret && !Auth::user()->two_factor_confirmed_at)
                            <p class="font-semibold">
                                {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
                            </p>
                            <div class="mt-4">
                                {!! Auth::user()->twoFactorQrCodeSvg() !!}
                            </div>
                            <p class="mt-4 text-sm font-semibold">
                                {{ __('Setup Key') }}: {{ decrypt(Auth::user()->two_factor_secret) }}
                            </p>

                            {{-- Confirm 2FA (khi bật lần đầu) --}}
                            <div class="mt-4">
                                <form method="POST" action="{{ url('user/confirmed-two-factor-authentication') }}">
                                    @csrf
                                    <input type="text" name="code" class="form-control"
                                        placeholder="{{ __('Code') }}" required autofocus>
                                    <x-input-error for="code" class="mt-2" />
                                    <button type="submit" class="btn btn-success mt-2">{{ __('Confirm') }}</button>
                                </form>
                            </div>
                        @endif

                        {{-- Recovery Codes --}}
                        <div class="mt-4">
                            <p class="font-semibold">
                                {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                            </p>
                            <div class="bg-gray-100 rounded-lg p-3 mt-2 text-sm">
                                @foreach (json_decode(decrypt(Auth::user()->two_factor_recovery_codes), true) as $code)
                                    <div>{{ $code }}</div>
                                @endforeach
                            </div>

                            <div class="mt-4 d-flex">
                                {{-- Regenerate Recovery Codes --}}
                                <form method="POST" action="{{ url('user/two-factor-recovery-codes') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary me-2">
                                        {{ __('Regenerate Recovery Codes') }}
                                    </button>
                                </form>

                                {{-- Disable 2FA --}}
                                <form method="POST" action="{{ url('user/two-factor-authentication') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('{{ __('Are you sure you want to disable two factor authentication?') }}')">
                                        {{ __('Disable') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
