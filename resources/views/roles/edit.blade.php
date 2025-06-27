<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => 'Roles', 'url' => route('roles.index')], ['name' => 'Edit Role']]" />
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('roles.update', $role->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label required for="name" :value="__('Role Name')" />
                            <x-text-input id="name" class="form-control mt-1" type="text" name="name"
                                :value="old('name', $role->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label :value="__('Permissions')" />
                            <div class="row g-3">
                                @foreach ($permissions as $permission)
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                        <div class="form-check">
                                            <input id="permission_{{ $permission->id }}" type="checkbox"
                                                name="permissions[]" value="{{ $permission->name }}"
                                                class="form-check-input"
                                                {{ in_array($permission->name, old('permissions', $rolePermissions ?? [])) ? 'checked' : '' }} />
                                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                {{ ucfirst(__($permission->name)) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('permissions')" class="mt-2" />
                            <x-input-error :messages="$errors->get('permissions.*')" class="mt-2" />
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <x-primary-button>
                                <i class="bi bi-floppy-fill"></i> {{ __('Update Role') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
