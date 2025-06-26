<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => 'Users', 'url' => route('users.index')], ['name' => 'Edit User']]" />
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form method="POST" action="{{ route('users.update', $user->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Row 1: Name and Email --}}
                                <div>
                                    <x-input-label required for="name" :value="__('Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                        :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label required for="email" :value="__('Email')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                        :value="old('email', $user->email)" required autocomplete="username" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                {{-- Row 2: New Password and Confirm New Password --}}
                                <div>
                                    <x-input-label for="password" :value="__('New Password (leave blank to keep current)')" />
                                    <x-text-input id="password" class="block w-full" type="password" name="password"
                                        autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
                                    <x-text-input id="password_confirmation" class="block w-full" type="password"
                                        name="password_confirmation" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>

                                {{-- Row 3: Description (full width) --}}
                                <div class="col-span-full">
                                    <x-input-label for="description" :value="__('Description')" />
                                    <textarea id="description" name="description" rows="3"
                                        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">{{ old('description', $user->description) }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                {{-- Row 4: Block Account Checkbox (full width) --}}
                                @if ($user->status !== 'blocked')
                                    <div class="col-span-full">
                                        <label for="blocked_account" class="flex items-center mt-1">
                                            <input id="blocked_account" type="checkbox" name="blocked_account"
                                                value="1"
                                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                                {{ old('blocked_account') == '1' ? 'checked' : '' }}>
                                            <span
                                                class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Blocked Account') }}</span>
                                        </label>
                                        <x-input-error :messages="$errors->get('blocked_account')" class="mt-2" />
                                    </div>
                                @endif

                                {{-- Row 5: Select Roles (allow multiple, full width) --}}
                                <div class="col-span-full">
                                    <x-input-label :value="__('Roles')" />
                                    <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                        @foreach ($roles as $role)
                                            <label for="role_{{ $role->id }}" class="flex items-center">
                                                <input id="role_{{ $role->id }}" type="checkbox" name="roles[]"
                                                    value="{{ $role->name }}"
                                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                                    {{ in_array($role->name, old('roles', $userRoles)) ? 'checked' : '' }} />
                                                <span
                                                    class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __($role->name) }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                                    <x-input-error :messages="$errors->get('roles.*')" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button class="ms-4">
                                    {{ __('Update User') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <x-user-info :user="$user" />

                {{-- Activity Log Column (1/3 width on large screens) --}}
                <div class="lg:col-span-1 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-6">{{ __('Activity Log') }}</h3>

                        <x-activiti-timeline :activities="$activities" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
