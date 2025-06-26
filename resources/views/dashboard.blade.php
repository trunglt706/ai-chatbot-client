<x-app-layout>
    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-xl font-semibold mb-4">{{ __('Các Module đã Phát hành') }}</h3>
                            @forelse ($publishedModules as $module)
                                <div class="mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                                    <h4 class="text-lg font-medium text-indigo-600 dark:text-indigo-400">
                                        {{ $module->name }}
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        {{ Str::limit($module->description, 100) }}</p>

                                    {{-- Hiển thị nhà tài trợ cho module này --}}
                                    @if ($module->sponsors->isNotEmpty())
                                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                            {{ __('Được tài trợ bởi:') }}
                                            @foreach ($module->sponsors as $sponsor)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-700 dark:text-indigo-100 mr-1">
                                                    @if ($sponsor->image)
                                                        <img src="{{ Storage::url($sponsor->image) }}"
                                                            alt="{{ $sponsor->name }}"
                                                            class="h-4 w-4 rounded-full mr-1">
                                                    @endif
                                                    {{ $sponsor->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="flex space-x-2 mt-2">
                                        @can('show modules')
                                            <a href="{{ route('modules.show', $module->id) }}" target="_blank"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                {{ __('Chạy Demo') }}
                                            </a>
                                        @endcan

                                        @can('view module details')
                                            <a href="{{ route('modules.show', $module->slug) }}"
                                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                {{ __('Xem Module') }}
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400">
                                    {{ __('Hiện chưa có module nào được phát hành.') }}</p>
                            @endforelse
                            <div class="mt-4 text-right">
                                <a href="{{ route('modules.index') }}"
                                    class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ __('Quản lý tất cả Modules') }} &raquo;
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-xl font-semibold mb-4">{{ __('Bài viết Mới nhất') }}</h3>
                            @forelse ($publishedPosts as $post)
                                <div class="mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                                    <h4 class="text-lg font-medium">
                                        <a href="{{ route('posts.show', $post->slug) }}"
                                            class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                            {{ $post->title }}
                                        </a>
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Xuất bản vào') }} {{ $post->published_at?->format('d/m/Y') }}
                                        {{ __('bởi') }} {{ $post->user->name ?? 'N/A' }}
                                    </p>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400">
                                    {{ __('Hiện chưa có bài viết nào được phát hành.') }}</p>
                            @endforelse
                            <div class="mt-4 text-right">
                                <a href="{{ route('posts.index') }}"
                                    class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ __('Xem tất cả bài viết') }} &raquo;
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="lg:col-span-1 space-y-6">

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-xl font-semibold mb-4">{{ __('Nhật ký Hành động của bạn') }}</h3>
                            <x-activiti-timeline :activities="$userActivities" />
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="pb-2 text-gray-900 dark:text-gray-100 text-center">
                            <a target="_blank" href="https://my.bkns.net/?affid=286">
                                <img width='100%' height='505'
                                    src='https://www.bkns.vn/wp-content/uploads/2024/06/cloud-0d.jpg.webp'
                                    alt='banner_250x250'>
                            </a>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                {{ __('By Server At Here') }}</p>
                        </div>
                    </div>

                    {{-- NEW: Donate QR Code Banner --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100 text-center">
                            <h3 class="text-xl font-semibold mb-4">{{ __('Ủng hộ dự án') }}</h3>
                            <p class="text-gray-700 dark:text-gray-300 mb-4">
                                {{ __('Nếu bạn thấy dự án này hữu ích, hãy cân nhắc ủng hộ để chúng tôi có động lực duy trì và phát triển!') }}
                            </p>
                            <img src="{{ asset('images/donate_qr.png') }}" alt="QR Code ủng hộ"
                                class="w-48 h-48 rounded-lg shadow-md mx-auto object-contain">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Quét mã QR để ủng hộ.') }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('Mọi sự đóng góp đều rất quý báu!') }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
