<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Quản lý Dung lượng Hệ thống') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-3 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600 bg-red-100 p-3 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-700 rounded-md shadow-sm">
                        <h3 class="text-lg font-semibold mb-3">Thông số Tổng quan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Tổng số tập tin:</p>
                                <p class="text-2xl font-bold">{{ $totalFiles }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Tổng dung lượng sử dụng:</p>
                                <p class="text-2xl font-bold">{{ $totalSizeReadable }}</p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <form action="{{ route('admin.storage.clear-all') }}" method="POST"
                                onsubmit="return confirm('Bạn CÓ CHẮC CHẮN muốn xóa TẤT CẢ dữ liệu media? Hành động này không thể hoàn tác!');">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Xóa tất cả dữ liệu Media') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold mb-3">Danh sách Tập tin Media</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Ảnh / Tên file
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Loại
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Kích thước
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Liên kết với
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Hành động
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($allMediaFiles as $media)
                                    <tr>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $media->id }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 flex items-center">
                                            @if (Str::startsWith($media->mime_type, 'image'))
                                                <img src="{{ $media->full_url }}" alt="{{ $media->file_name }}"
                                                    class="h-10 w-10 object-cover rounded mr-2">
                                            @else
                                                <svg class="h-10 w-10 text-gray-400 mr-2" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0113 3.414V6a2 2 0 002 2h2.586A2 2 0 0120 11.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                            <span>{{ Str::limit($media->file_name, 30) }}</span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $media->mime_type }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ \App\Http\Controllers\Admin\StorageController::formatBytes($media->file_size) }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            @if ($media->mediable)
                                                {{ class_basename($media->mediable_type) }} #{{ $media->mediable_id }}
                                                @if ($media->mediable_type === 'App\\Models\\Post')
                                                    - <a href="{{ route('posts.show', $media->mediable->slug) }}"
                                                        class="text-blue-500 hover:underline">{{ Str::limit($media->mediable->title, 20) }}</a>
                                                @endif
                                            @else
                                                <span class="text-gray-400">Không liên kết</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <form action="{{ route('admin.storage.destroy', $media->id) }}"
                                                method="POST" class="inline-block"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa tập tin này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600">Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6"
                                            class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            Không có tập tin nào trong hệ thống.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
