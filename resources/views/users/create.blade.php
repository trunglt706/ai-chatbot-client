<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => 'Users', 'url' => route('users.index')], ['name' => 'Add New User']]" />
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message />
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <x-input-label required for="name" :value="__('Name')" />
                                <x-text-input id="name" class="form-control mt-1" type="text" name="name"
                                    :value="old('name')" required autofocus autocomplete="name" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div class="col-12 col-md-6">
                                <x-input-label required for="email" :value="__('Email')" />
                                <x-text-input id="email" class="form-control mt-1" type="email" name="email"
                                    :value="old('email')" required autocomplete="username" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div class="col-12 col-md-6">
                                <x-input-label required for="password" :value="__('Password')" />
                                <x-text-input id="password" class="form-control" type="password" name="password"
                                    required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div class="col-12 col-md-6">
                                <x-input-label required for="password_confirmation" :value="__('Confirm Password')" />
                                <x-text-input id="password_confirmation" class="form-control" type="password"
                                    name="password_confirmation" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>

                            <div class="col-12">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" rows="3" class="form-control mt-1">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <div class="col-12">
                                <x-input-label class=" mt-2" :value="__('Roles')" />
                                <div class="row g-2">
                                    @foreach ($roles as $role)
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="form-check">
                                                <input class="form-check-input" id="role_{{ $role->id }}"
                                                    type="checkbox" name="roles[]" value="{{ $role->id }}"
                                                    {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
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
                                <i class="bi bi-floppy-fill"></i> {{ __('Create User') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
