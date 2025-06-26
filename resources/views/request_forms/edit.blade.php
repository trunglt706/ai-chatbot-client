<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Cập nhật Yêu Cầu Liên Hệ') }} #{{ $contact->id }}
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

                    <form action="{{ route('contacts.update', $contact->id) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- Sử dụng phương thức PUT cho cập nhật --}}

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Người gửi') }}:
                            </label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $contact->user->name }}
                                ({{ $contact->user->email }})</p>
                        </div>

                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Phân loại yêu cầu') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="type" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm"
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
                            <label for="module_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Chọn Module bạn quan tâm') }}
                            </label>
                            <select name="module_id" id="module_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm">
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
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Nội dung yêu cầu chi tiết') }} <span class="text-red-500">*</span>
                            </label>
                            <textarea name="content" id="content" rows="6" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm resize-y">{{ old('content', $contact->content) }}</textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        @if (Auth::user()->hasRole('admin')) {{-- Chỉ hiển thị trạng thái cho admin --}}
                            <div class="mb-4">
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Trạng thái') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="status" id="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm">
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

                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Cập nhật Yêu Cầu') }}
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
