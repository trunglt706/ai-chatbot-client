<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => 'Roles', 'url' => route('roles.index')], ['name' => 'Add New Role']]" />
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('roles.store') }}">
                        @csrf

                        <div>
                            <x-input-label required for="name" :value="__('Role Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label :value="__('Permissions')" />
                            <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @foreach ($permissions as $permission)
                                    <label for="permission_{{ $permission->id }}" class="flex items-center">
                                        <input id="permission_{{ $permission->id }}" type="checkbox"
                                            name="permissions[]" value="{{ $permission->id }}"
                                            class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                            {{ in_array($permission->id, old('permissions', $userPermissions ?? [])) ? 'checked' : '' }} />
                                        <span
                                            class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __($permission->name) }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('permissions')" class="mt-2" />
                            <x-input-error :messages="$errors->get('permissions.*')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Create Role') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#permissions').select2();
        });
    </script>
@endpush
