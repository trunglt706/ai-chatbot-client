<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sửa Nhà tài trợ') }}: {{ $sponsor->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.sponsors.update', $sponsor) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="code" :value="__('Mã nhà tài trợ')" />
                            <p id="code" class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $sponsor->code }}
                            </p>
                        </div>

                        <div>
                            <x-input-label for="name" :value="__('Tên nhà tài trợ')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="old('name', $sponsor->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="image" :value="__('Hình ảnh (Logo)')" />
                            @if ($sponsor->image)
                                <div class="mt-2 mb-2">
                                    <img src="{{ Storage::url($sponsor->image) }}" alt="{{ $sponsor->name }}"
                                        class="h-20 w-20 object-contain rounded-full">
                                    <div class="flex items-center mt-2">
                                        <input type="checkbox" name="remove_image" id="remove_image"
                                            class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <label for="remove_image"
                                            class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Xóa hình ảnh hiện tại') }}</label>
                                    </div>
                                </div>
                            @endif
                            <input id="image" type="file" name="image"
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" />
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="status" :value="__('Trạng thái')" />
                            <select id="status" name="status"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                required>
                                <option value="active"
                                    {{ old('status', $sponsor->status) == 'active' ? 'selected' : '' }}>
                                    {{ __('Active') }}</option>
                                <option value="inactive"
                                    {{ old('status', $sponsor->status) == 'inactive' ? 'selected' : '' }}>
                                    {{ __('Inactive') }}</option>
                                <option value="pending"
                                    {{ old('status', $sponsor->status) == 'pending' ? 'selected' : '' }}>
                                    {{ __('Pending') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Mô tả')" />
                            <x-textarea id="description" name="description"
                                class="block mt-1 w-full">{{ old('description', $sponsor->description) }}</x-textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                {{ __('Tài trợ Modules') }}</h3>
                            <div class="space-y-4">
                                @forelse ($modules as $module)
                                    @php
                                        $isSponsored = $sponsoredModules->has($module->id);
                                        $pivot = $isSponsored ? $sponsoredModules[$module->id]->pivot : null;
                                    @endphp
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <label for="module_{{ $module->id }}"
                                                class="flex items-center text-gray-900 dark:text-gray-100">
                                                <input type="checkbox" id="module_{{ $module->id }}"
                                                    name="modules[{{ $module->id }}][is_sponsored]" value="1"
                                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                                    {{ $isSponsored ? 'checked' : '' }}
                                                    onchange="toggleModuleSponsorDetails(this, {{ $module->id }})">
                                                <span class="ml-2 font-semibold">{{ $module->name }}</span>
                                            </label>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Slug:') }}
                                                {{ $module->slug }}</span>
                                        </div>

                                        <div id="module_details_{{ $module->id }}"
                                            class="mt-4 space-y-3 {{ $isSponsored ? '' : 'hidden' }}">
                                            <div>
                                                <x-input-label for="total_amount_{{ $module->id }}"
                                                    :value="__('Tổng tiền tài trợ')" />
                                                <x-text-input id="total_amount_{{ $module->id }}" type="number"
                                                    step="0.01" name="modules[{{ $module->id }}][total_amount]"
                                                    class="block mt-1 w-full" :value="old(
                                                        'modules.' . $module->id . '.total_amount',
                                                        $pivot->total_amount ?? 0,
                                                    )" />
                                                <x-input-error :messages="$errors->get('modules.' . $module->id . '.total_amount')" class="mt-2" />
                                            </div>
                                            <div>
                                                <x-input-label for="start_date_{{ $module->id }}"
                                                    :value="__('Ngày bắt đầu tài trợ')" />
                                                <x-text-input id="start_date_{{ $module->id }}" type="date"
                                                    name="modules[{{ $module->id }}][start_date]"
                                                    class="block mt-1 w-full" :value="old(
                                                        'modules.' . $module->id . '.start_date',
                                                        $pivot->start_date ?? '',
                                                    )" />
                                                <x-input-error :messages="$errors->get('modules.' . $module->id . '.start_date')" class="mt-2" />
                                            </div>
                                            <div>
                                                <x-input-label for="end_date_{{ $module->id }}" :value="__('Ngày kết thúc tài trợ')" />
                                                <x-text-input id="end_date_{{ $module->id }}" type="date"
                                                    name="modules[{{ $module->id }}][end_date]"
                                                    class="block mt-1 w-full" :value="old(
                                                        'modules.' . $module->id . '.end_date',
                                                        $pivot->end_date ?? '',
                                                    )" />
                                                <x-input-error :messages="$errors->get('modules.' . $module->id . '.end_date')" class="mt-2" />
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400">
                                        {{ __('Không có module nào để tài trợ.') }}</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Cập nhật') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleModuleSponsorDetails(checkbox, moduleId) {
                const detailsDiv = document.getElementById('module_details_' + moduleId);
                if (checkbox.checked) {
                    detailsDiv.classList.remove('hidden');
                    // Đặt lại giá trị mặc định nếu trước đó bị ẩn
                    detailsDiv.querySelectorAll('input').forEach(input => {
                        if (input.type === 'number') input.value = input.value || 0;
                        if (input.type === 'date') input.value = input.value || '';
                    });
                } else {
                    detailsDiv.classList.add('hidden');
                    // Xóa giá trị khi không tài trợ để không gửi dữ liệu không cần thiết
                    detailsDiv.querySelectorAll('input').forEach(input => {
                        input.value = '';
                    });
                }
            }

            // Khởi tạo trạng thái ban đầu khi tải trang
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('input[type="checkbox"][name^="modules["][name$="[is_sponsored]"]').forEach(
                    checkbox => {
                        const moduleId = checkbox.id.split('_')[1];
                        toggleModuleSponsorDetails(checkbox, moduleId);
                    });
            });
        </script>
    @endpush
</x-app-layout>
