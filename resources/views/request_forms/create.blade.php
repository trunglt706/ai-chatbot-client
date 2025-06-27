<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold h4 text-dark mb-0">
            {{ __('Gửi Yêu Cầu Liên Hệ') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="card shadow-sm mb-4">
                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger mb-4" role="alert">
                            <div class="fw-semibold mb-2">Có lỗi xảy ra:</div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contacts.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <x-input-label required for="type" :value="__('Request Form Type')" />
                            <select name="type" id="type" required class="form-select mt-1"
                                onchange="toggleModuleSelection()">
                                <option value="">{{ __('Chọn phân loại') }}</option>
                                @foreach ($formTypes as $key => $value)
                                    <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        {{-- Danh sách module (hiển thị có điều kiện) --}}
                        <div id="module-selection-container" class="mb-4"
                            style="display: {{ in_array(old('type'), [2, 3]) ? 'block' : 'none' }};">
                            <label for="module_id" class="form-label fw-semibold">
                                {{ __('Chọn Module bạn quan tâm') }}
                            </label>
                            <select name="module_id" id="module_id" class="form-select mt-1">
                                <option value="">{{ __('Không chọn module cụ thể') }}</option>
                                @foreach ($modules as $module)
                                    <option value="{{ $module->id }}"
                                        {{ old('module_id') == $module->id ? 'selected' : '' }}>
                                        {{ $module->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('module_id')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <label for="content" class="form-label fw-semibold">
                                Nội dung liên hệ <span class="text-danger">*</span>
                            </label>
                            {{-- Input ẩn để Trix lưu trữ nội dung --}}
                            <input id="x" type="hidden" name="content"
                                value="{{ old('content', $contact->content ?? '') }}">
                            <trix-editor input="x" class="form-control mt-1 min-vh-25"></trix-editor>
                            @error('content')
                                <p class="text-danger text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary text-uppercase fw-semibold text-xs">
                                {{ __('Gửi Yêu Cầu') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript để hiển thị/ẩn danh sách module --}}
    <script>
        function toggleModuleSelection() {
            const selectElement = document.getElementById('type');
            const moduleContainer = document.getElementById('module-selection-container');
            const selectedType = parseInt(selectElement.value);

            if (selectedType === 2 || selectedType === 3) {
                moduleContainer.style.display = 'block';
            } else {
                moduleContainer.style.display = 'none';
                // Đặt giá trị module_id về rỗng khi ẩn
                document.getElementById('module_id').value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', toggleModuleSelection);
    </script>
</x-app-layout>
