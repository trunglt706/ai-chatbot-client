<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => 'Roles', 'url' => route('roles.index')], ['name' => 'Add New Role']]" />
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message />
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('roles.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-7 mb-4">
                                <x-input-label required for="name" :value="__('Role Name')" />
                                <x-text-input id="name" class="form-control mt-1" type="text" name="name"
                                    :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div class="col-md-5 mb-4">
                                <x-input-label for="parent_id" :value="__('Parent Role')" />
                                <select class="form-select" id="parent_id" name="parent_id">
                                    <option value="">-- {{ __('None') }} --</option>
                                    @foreach ($roles as $parentRole)
                                        <option value="{{ $parentRole->id }}"
                                            {{ old('parent_id') == $parentRole->id ? 'selected' : '' }}>
                                            {{ $parentRole->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="can_view_child_data"
                                name="can_view_child_data" value="1"
                                {{ old('can_view_child_data', true) ? 'checked' : '' }}>

                            <x-input-label for="can_view_child_data" :value="__('Can view child data')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label :value="__('Permissions')" />
                            <div class="row g-3">
                                @foreach ($permissions as $permission)
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                        <div class="form-check">
                                            <input id="permission_{{ $permission->id }}" type="checkbox"
                                                name="permissions[]" value="{{ $permission->id }}"
                                                class="form-check-input"
                                                {{ in_array($permission->id, old('permissions', $userPermissions ?? [])) ? 'checked' : '' }} />
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
                                <i class="bi bi-floppy-fill"></i> {{ __('Create Role') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
