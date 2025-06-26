<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gửi Yêu Cầu Liên Hệ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-3 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 font-medium text-red-600 bg-red-100 p-3 rounded-md">
                            Có lỗi xảy ra:
                            <ul class="mt-2 list-disc list-inside">
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
                            <select name="type" id="type" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm"
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
                            <label for="module_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Chọn Module bạn quan tâm') }}
                            </label>
                            <select name="module_id" id="module_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm">
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
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Nội dung liên hệ <span class="text-red-500">*</span>
                            </label>
                            {{-- Input ẩn để Trix lưu trữ nội dung --}}
                            <input id="x" type="hidden" name="content"
                                value="{{ old('content', $contact->content ?? '') }}">
                            <trix-editor input="x"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm"></trix-editor>
                            @error('content')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
