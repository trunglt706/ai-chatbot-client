<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => __('Sponsors'), 'url' => route('sponsors.index')], ['name' => __('Add New Sponsor')]]" />
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message />
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('sponsors.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <x-input-label required for="name" :value="__('Name')" />
                                <x-text-input id="name" class="form-control mt-1" type="text" name="name"
                                    :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="col-md-4 mb-3">
                                <x-input-label for="image" :value="__('Image') . __(' (JPG, PNG, JPEG, GIF, SVG)')" />
                                <input id="image" type="file" name="image" class="form-control mt-1"
                                    accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml" />
                                <x-input-error :messages="$errors->get('image')" class="mt-2" />
                            </div>

                            <div class="col-md-4 mb-3">
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="form-select mt-1" required>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                        {{ __('Active') }}</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                        {{ __('Inactive') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <x-input-label for="description" :value="__('Description')" />
                            <x-textarea id="description" name="description"
                                class="form-control mt-1">{{ old('description') }}</x-textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <x-primary-button>
                                <i class="bi bi-floppy-fill"></i> {{ __('Add New') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
