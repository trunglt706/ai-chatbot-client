<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => 'Sponsors', 'url' => route('sponsors.index')], ['name' => 'Add New Sponsor']]" />
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('sponsors.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <x-input-label required for="name" :value="__('Tên nhà tài trợ')" />
                            <x-text-input id="name" class="form-control mt-1" type="text" name="name"
                                :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="image" :value="__('Hình ảnh (Logo)')" />
                            <input id="image" type="file" name="image" class="form-control mt-1" />
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Trạng thái')" />
                            <select id="status" name="status" class="form-select mt-1" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                    {{ __('Active') }}</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                    {{ __('Inactive') }}</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>
                                    {{ __('Pending') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Mô tả')" />
                            <x-textarea id="description" name="description"
                                class="form-control mt-1">{{ old('description') }}</x-textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <x-primary-button>
                                {{ __('Thêm') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
