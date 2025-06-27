<section class="mb-5">
    <header>
        <h2 class="h5 text-dark">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-2 text-muted">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button type="button" class="btn btn-danger mt-3" data-bs-toggle="modal" data-bs-target="#confirm-user-deletion">
        {{ __('Delete Account') }}
    </button>

    <!-- Modal -->
    <div class="modal fade @if ($errors->userDeletion->isNotEmpty()) show d-block @endif" id="confirm-user-deletion" tabindex="-1"
        role="dialog" aria-labelledby="confirmUserDeletionLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" action="{{ route('profile.destroy') }}" class="modal-content">
                @csrf
                @method('delete')

                <div class="modal-header">
                    <h5 class="modal-title" id="confirmUserDeletionLabel">
                        {{ __('Are you sure you want to delete your account?') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('Close') }}"></button>
                </div>

                <div class="modal-body">
                    <p class="text-muted">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>

                    <div class="form-group mt-3">
                        <label for="password" class="form-label visually-hidden">{{ __('Password') }}</label>
                        <input id="password" name="password" type="password" class="form-control w-75"
                            placeholder="{{ __('Password') }}">
                        @if ($errors->userDeletion->get('password'))
                            <div class="text-danger mt-2">
                                {{ $errors->userDeletion->first('password') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Delete Account') }}</button>
                </div>
            </form>
        </div>
    </div>
</section>
