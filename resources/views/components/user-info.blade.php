<div>
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg h-full">
        <div class="max-w-xl">
            <section>
                <div class="mt-6 space-y-4">
                    <div class="flex justify-between">
                        <x-input-label for="code" :value="__('User Code')" />
                        <p id="code" class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $user->code ?? 'N/A' }}</p>
                    </div>

                    <div class="flex justify-between">
                        <x-input-label for="registration_date" :value="__('Created At')" />
                        <p id="registration_date" class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $user->created_at->format('d/m/Y H:i:s') }}
                        </p>
                    </div>

                    <div class="flex justify-between">
                        <x-input-label for="registration_date" :value="__('Verify Account At')" />
                        <p id="registration_date" class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $user->email_verified_at ? $user->email_verified_at->format('d/m/Y H:i:s') : 'N/A' }}
                        </p>
                    </div>

                    <div class="flex justify-between">
                        <x-input-label for="last_updated" :value="__('Last Updated At')" />
                        <p id="last_updated" class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $user->updated_at->format('d/m/Y H:i:s') }}</p>
                    </div>

                    <div class="flex justify-between">
                        <x-input-label for="last_updated" :value="__('Roles')" />
                        <p id="last_updated" class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            @forelse ($user->getRoleNames() as $role)
                                <span
                                    class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mr-2 mt-2">
                                    {{ __($role) }}
                                </span>
                            @empty
                                N/A
                            @endforelse
                        </p>
                    </div>

                    <div class="flex justify-between">
                        <x-input-label for="last_updated" :value="__('Social Accounts')" />
                        @forelse ($user->socialAccounts as $socialAccount)
                            <li class="flex items-center text-gray-700 dark:text-gray-300">
                                @if ($socialAccount->provider_name === 'google')
                                    <img src="https://www.google.com/favicon.ico" alt="Google" class="w-5 h-5 mr-2">
                                @elseif ($socialAccount->provider_name === 'facebook')
                                    <img src="https://www.facebook.com/favicon.ico" alt="Facebook" class="w-5 h-5 mr-2">
                                @elseif ($socialAccount->provider_name === 'github')
                                    <img src="https://github.githubassets.com/favicons/favicon.png" alt="GitHub"
                                        class="w-5 h-5 mr-2">
                                @endif
                                <span>{{ ucfirst($socialAccount->provider_name) }} (ID:
                                    {{ $socialAccount->provider_id }})</span>
                            </li>
                        @empty
                            N/A
                        @endforelse
                    </div>

                    <div class="flex justify-between">
                        <x-input-label for="status" :value="__('Status')" />
                        <p id="status" class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if ($user->status === 'active') bg-green-100 text-green-800
                                                @elseif($user->status === 'blocked') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ $user->status ? __(ucfirst($user->status)) : 'N/A' }}
                            </span>
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
