<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold h4 text-dark mb-0">
            {{ __('Cập nhật Yêu Cầu Liên Hệ') }} #{{ $contact->id }}
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

                    <form action="{{ route('contacts.update', $contact->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                {{ __('Người gửi') }}:
                            </label>
                            <p class="mb-0 text-dark">{{ $contact->user->name }} ({{ $contact->user->email }})</p>
                        </div>

                        <div class="mb-4">
                            <x-input-label required for="type" :value="__('Request Form Type')" />
                            <select name="type" id="type" required class="form-select mt-1"
                                onchange="toggleModuleSelection()">
                                @foreach ($formTypes as $key => $value)
                                    <option value="{{ $key }}"
                                        {{ $contact->type == $key || old('type') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        {{-- Danh sách module (hiển thị có điều kiện) --}}
                        <div id="module-selection-container" class="mb-4"
                            style="display: {{ in_array(old('type', $contact->type), [2, 3]) ? 'block' : 'none' }};">
                            <label for="module_id" class="form-label fw-semibold">
                                {{ __('Chọn Module bạn quan tâm') }}
                            </label>
                            <select name="module_id" id="module_id" class="form-select mt-1">
                                <option value="">{{ __('Không chọn module cụ thể') }}</option>
                                @foreach ($modules as $module)
                                    <option value="{{ $module->id }}"
                                        {{ old('module_id', $contact->module_id) == $module->id ? 'selected' : '' }}>
                                        {{ $module->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('module_id')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label required for="content" :value="__('Request Form Content')" />
                            <textarea name="content" id="content" rows="6" required class="form-control mt-1 resize-vertical">{{ old('content', $contact->content) }}</textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        @if (Auth::user()->hasRole('admin'))
                            <div class="mb-4">
                                <x-input-label required for="status" :value="__('Request Form Status')" />
                                <select name="status" id="status" required class="form-select mt-1">
                                    @foreach ($statuses as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ old('status', $contact->status) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        @endif

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary text-uppercase fw-semibold text-xs">
                                {{ __('Cập nhật Yêu Cầu') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleModuleSelection() {
            const selectElement = document.getElementById('type');
            const moduleContainer = document.getElementById('module-selection-container');
            const selectedType = parseInt(selectElement.value);

            if (selectedType === 2 || selectedType === 3) {
                moduleContainer.style.display = 'block';
            } else {
                moduleContainer.style.display = 'none';
                document.getElementById('module_id').value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', toggleModuleSelection);
    </script>
</x-app-layout>
