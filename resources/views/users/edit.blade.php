<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => 'Users', 'url' => route('users.index')], ['name' => 'Edit User']]" />
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-12 col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <form method="POST" action="{{ route('users.update', $user->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="row g-4">
                                    {{-- Row 1: Name and Email --}}
                                    <div class="col-12 col-md-6">
                                        <x-input-label required for="name" :value="__('Name')" />
                                        <x-text-input id="name" class="form-control mt-1" type="text"
                                            name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <x-input-label required for="email" :value="__('Email')" />
                                        <x-text-input id="email" class="form-control mt-1" type="email"
                                            name="email" :value="old('email', $user->email)" required autocomplete="username" />
                                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                    </div>

                                    {{-- Row 2: New Password and Confirm New Password --}}
                                    <div class="col-12 col-md-6">
                                        <x-input-label for="password" :value="__('New Password (leave blank to keep current)')" />
                                        <x-text-input id="password" class="form-control" type="password"
                                            name="password" autocomplete="new-password" />
                                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
                                        <x-text-input id="password_confirmation" class="form-control" type="password"
                                            name="password_confirmation" autocomplete="new-password" />
                                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                    </div>

                                    {{-- Row 3: Description (full width) --}}
                                    <div class="col-12">
                                        <x-input-label for="description" :value="__('Description')" />
                                        <textarea id="description" name="description" rows="3" class="form-control mt-1">{{ old('description', $user->description) }}</textarea>
                                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                    </div>

                                    {{-- Row 4: Block Account Checkbox (full width) --}}
                                    @if ($user->status !== 'blocked')
                                        <div class="col-12">
                                            <div class="form-check mt-1">
                                                <input id="blocked_account" type="checkbox" name="blocked_account"
                                                    value="1" class="form-check-input"
                                                    {{ old('blocked_account') == '1' ? 'checked' : '' }}>
                                                <label for="blocked_account" class="form-check-label">
                                                    {{ __('Blocked Account') }}
                                                </label>
                                            </div>
                                            <x-input-error :messages="$errors->get('blocked_account')" class="mt-2" />
                                        </div>
                                    @endif

                                    {{-- Row 5: Select Roles (allow multiple, full width) --}}
                                    <div class="col-12">
                                        <x-input-label :value="__('Roles')" />
                                        <div class="row g-2">
                                            @foreach ($roles as $role)
                                                <div class="col-6 col-md-4 col-lg-3">
                                                    <div class="form-check">
                                                        <input id="role_{{ $role->id }}" type="checkbox"
                                                            name="roles[]" value="{{ $role->name }}"
                                                            class="form-check-input"
                                                            {{ in_array($role->name, old('roles', $userRoles)) ? 'checked' : '' }} />
                                                        <label class="form-check-label" for="role_{{ $role->id }}">
                                                            {{ __($role->name) }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                                        <x-input-error :messages="$errors->get('roles.*')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <x-primary-button>
                                        <i class="bi bi-floppy-fill"></i> {{ __('Update User') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <x-user-info :user="$user" />

                    {{-- Activity Log Column --}}
                    <div class="card shadow-sm my-4">
                        <div class="card-body">
                            <h3 class="h6 fw-semibold mb-4">{{ __('Activity Log') }}</h3>
                            <x-activiti-timeline :activities="$activities" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
