<div>
    <div class="px-4 py-1 bg-white shadow rounded h-100">
        <div class="mx-auto" style="max-width: 36rem;">
            <section>
                <div class="mt-4">
                    <div class="row mb-3">
                        <div class="col-6">
                            <x-input-label for="code" :value="__('User Code')" />
                        </div>
                        <div class="col-6 text-end">
                            <p id="code" class="mb-0 small text-dark">
                                {{ $user->code ?? __('N/A') }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <x-input-label for="registration_date" :value="__('Created At')" />
                        </div>
                        <div class="col-6 text-end">
                            <p id="registration_date" class="mb-0 small text-dark">
                                {{ $user->created_at->format('d/m/Y H:i:s') }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <x-input-label for="registration_date" :value="__('Verify Account At')" />
                        </div>
                        <div class="col-6 text-end">
                            <p id="registration_date" class="mb-0 small text-dark">
                                {{ $user->email_verified_at ? $user->email_verified_at->format('d/m/Y H:i:s') : __('N/A') }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <x-input-label for="last_updated" :value="__('Last Updated At')" />
                        </div>
                        <div class="col-6 text-end">
                            <p id="last_updated" class="mb-0 small text-dark">
                                {{ $user->updated_at->format('d/m/Y H:i:s') }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <x-input-label for="last_updated" :value="__('Roles')" />
                        </div>
                        <div class="col-6 text-end">
                            <p id="last_updated" class="mb-0 small text-dark">
                                @forelse ($user->getRoleNames() as $role)
                                    <span class="badge bg-primary me-2 mb-2">
                                        {{ __($role) }}
                                    </span>
                                @empty
                                    @lang('N/A')
                                @endforelse
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <x-input-label for="last_updated" :value="__('Social Accounts')" />
                        </div>
                        <div class="col-6 text-end">
                            <ul class="list-unstyled mb-0">
                                @forelse ($user->socialAccounts as $socialAccount)
                                    <li class="d-flex align-items-center text-secondary mb-1 text-nowrap">
                                        @if ($socialAccount->provider_name === 'google')
                                            <img src="https://www.google.com/favicon.ico" alt="Google" class="me-2"
                                                style="width: 20px; height: 20px;">
                                        @elseif ($socialAccount->provider_name === 'facebook')
                                            <img src="https://www.facebook.com/favicon.ico" alt="Facebook"
                                                class="me-2" style="width: 20px; height: 20px;">
                                        @elseif ($socialAccount->provider_name === 'github')
                                            <img src="https://github.githubassets.com/favicons/favicon.png"
                                                alt="GitHub" class="me-2" style="width: 20px; height: 20px;">
                                        @endif
                                        <span>{{ ucfirst($socialAccount->provider_name) }} (@lang('ID')
                                            {{ $socialAccount->provider_id }})</span>
                                    </li>
                                @empty
                                    @lang('N/A')
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <x-input-label for="status" :value="__('Status')" />
                        </div>
                        <div class="col-6 text-end">
                            <p id="status" class="mb-0 small text-dark">
                                <span
                                    class="badge
                                    @if ($user->status === 'active') bg-success
                                    @elseif($user->status === 'blocked') bg-danger
                                    @else bg-warning text-dark @endif">
                                    {{ $user->status ? __(ucfirst($user->status)) : __('N/A') }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
