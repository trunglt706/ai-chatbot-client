@if (env('APP_ENV') !== 'production')
    {{-- NEW: Socialite Login Buttons --}}
    <div class="mt-4">
        <div class="position-relative mb-3">
            <hr class="m-0">
            <div class="position-absolute top-50 start-50 translate-middle bg-white px-2 text-secondary small">
                {{ __('Or') }}
            </div>
        </div>

        <div class="row mt-4 text-center">
            <div class="col-md-4 mb-2">
                <a href="{{ route('socialite.redirect', ['provider' => 'google']) }}"
                    class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center">
                    <img src="https://www.google.com/favicon.ico" alt="Google" class="me-2"
                        style="width: 20px; height: 20px;">
                    {{ __('Google') }}
                </a>
            </div>
            <div class="col-md-4 mb-2">
                <a href="{{ route('socialite.redirect', ['provider' => 'facebook']) }}"
                    class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center">
                    <img src="https://www.facebook.com/favicon.ico" alt="Facebook" class="me-2"
                        style="width: 20px; height: 20px;">
                    {{ __('Facebook') }}
                </a>
            </div>
            <div class="col-md-4 mb-2">
                <a href="{{ route('socialite.redirect', ['provider' => 'github']) }}"
                    class="btn btn-outline-dark w-100 d-flex align-items-center justify-content-center">
                    <img src="https://github.githubassets.com/favicons/favicon.png" alt="GitHub" class="me-2"
                        style="width: 20px; height: 20px;">
                    {{ __('GitHub') }}
                </a>
            </div>
        </div>
    </div>
@endif
