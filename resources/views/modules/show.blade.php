<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chi tiết Module') }}: {{ $module->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ __('Tên Module') }}</h3>
                        <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $module->name }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ __('Mô tả') }}</h3>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $module->description }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ __('Trạng thái') }}</h3>
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if ($module->status == \App\Models\Module::STATUS_PUBLISHED) bg-green-100 text-green-800
                            @elseif($module->status == \App\Models\Module::STATUS_DEVELOPING) bg-blue-100 text-blue-800
                            @elseif($module->status == \App\Models\Module::STATUS_PAUSED) bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ \App\Models\Module::getStatuses()[$module->status] ?? 'Không xác định' }}
                        </span>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ __('Ngày cập nhật cuối') }}
                        </h3>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">
                            {{ $module->updated_at->format('Y-m-d H:i:s') }}</p>
                    </div>

                    <div class="flex justify-start mt-6">
                        <a href="{{ url()->previous() }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Quay lại') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
