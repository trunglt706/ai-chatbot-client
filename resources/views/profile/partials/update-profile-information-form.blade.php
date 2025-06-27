<section class="mb-5">
    <header class="mb-3">
        <h2 class="h5 text-dark">
            {{ __('Profile Information') }}
        </h2>

        <p class="text-muted small">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label">
                {{ __('Name') }} <span class="text-danger">*</span>
            </label>
            <input id="name" name="name" type="text" class="form-control"
                value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @if ($errors->has('name'))
                <div class="text-danger mt-1 small">
                    {{ $errors->first('name') }}
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">
                {{ __('Email') }} <span class="text-danger">*</span>
            </label>
            <input id="email" name="email" type="email" class="form-control"
                value="{{ old('email', $user->email) }}" required autocomplete="username" disabled>
            @if ($errors->has('email'))
                <div class="text-danger mt-1 small">
                    {{ $errors->first('email') }}
                </div>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="small text-warning">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" type="submit"
                            class="btn btn-link p-0 small text-decoration-underline">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-success small fw-semibold">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p class="text-success small mb-0" id="saved-message">
                    {{ __('Saved.') }}
                </p>
                <script>
                    setTimeout(() => {
                        const msg = document.getElementById('saved-message');
                        if (msg) msg.style.display = 'none';
                    }, 2000);
                </script>
            @endif
        </div>
    </form>
</section>
