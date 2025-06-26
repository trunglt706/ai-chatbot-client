<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Quản lý Hệ thống') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold mb-6">{{ __('Tổng quan hệ thống') }}</h3>

                    {{-- Thông báo chung (success/error) --}}
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

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {{-- Card: Thông tin Symbolic Link --}}
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-md">
                            <h4 class="text-xl font-semibold mb-3">{{ __('Trạng thái Storage Link') }}</h4>
                            <p class="text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Đường dẫn công khai cho file tải lên.') }}
                            </p>
                            <div class="flex items-center mb-4">
                                <span class="font-bold mr-2">{{ __('Trạng thái:') }}</span>
                                @if ($storageLinkExists)
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                        {{ __('Đã tạo') }}
                                    </span>
                                @else
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                        {{ __('Chưa tạo') }}
                                    </span>
                                @endif
                            </div>

                            @if (!$storageLinkExists)
                                <form action="{{ route('admin.system.create_storage_link') }}" method="POST">
                                    @csrf
                                    <x-primary-button type="submit">
                                        {{ __('Tạo Symbolic Link ngay') }}
                                    </x-primary-button>
                                </form>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                    {{ __('Việc này cho phép các file được tải lên có thể truy cập công khai.') }}
                                </p>
                            @else
                                <p class="text-gray-700 dark:text-gray-300">
                                    {{ __('Symbolic link đã tồn tại và hoạt động.') }}
                                </p>
                            @endif
                        </div>

                        {{-- Card: Thông tin Dung lượng Public Storage --}}
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-md">
                            <h4 class="text-xl font-semibold mb-3">{{ __('Dung lượng Public Storage') }}</h4>
                            <p class="text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Tổng dung lượng các file được tải lên công khai.') }}
                            </p>
                            <div class="flex items-center mb-4">
                                <span class="font-bold mr-2">{{ __('Dung lượng:') }}</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $publicStorageSize }}</span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                {{ __('Bao gồm hình ảnh nhà tài trợ, ảnh bài viết, v.v.') }}
                            </p>
                        </div>

                        {{-- Card: Thông tin chung của hệ thống (ví dụ: phiên bản Laravel) --}}
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-md">
                            <h4 class="text-xl font-semibold mb-3">{{ __('Thông tin chung') }}</h4>
                            <p class="text-gray-700 dark:text-gray-300 mb-2">
                                <span class="font-bold">{{ __('Phiên bản Laravel:') }}</span> {{ app()->version() }}
                            </p>
                            <p class="text-gray-700 dark:text-gray-300 mb-2">
                                <span class="font-bold">{{ __('Môi trường:') }}</span> {{ app()->environment() }}
                            </p>
                            <p class="text-gray-700 dark:text-gray-300 mt-2">
                                {{ __('Giúp kiểm tra cấu hình và môi trường hiện tại của ứng dụng.') }}
                            </p>
                        </div>

                        {{-- NEW Card: Xóa Cache Toàn bộ Hệ thống --}}
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-md">
                            <h4 class="text-xl font-semibold mb-3">{{ __('Xóa Cache Hệ thống') }}</h4>
                            <p class="text-gray-700 dark:text-gray-300 mb-4">
                                {{ __('Xóa tất cả các cache (cấu hình, route, view, ứng dụng) để làm mới hệ thống.') }}
                            </p>
                            <form action="{{ route('admin.system.clear_cache') }}" method="POST">
                                @csrf
                                <x-primary-button type="submit"
                                    class="bg-yellow-600 hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900">
                                    {{ __('Xóa Toàn bộ Cache') }}
                                </x-primary-button>
                            </form>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                {{ __('Nên làm khi có thay đổi về cấu hình hoặc mã nguồn.') }}
                            </p>
                        </div>

                        {{-- NEW Card: Trạng thái và Test Broadcasting --}}
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-md">
                            <h4 class="text-xl font-semibold mb-3">{{ __('Trạng thái Broadcasting') }}</h4>
                            <p class="text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Kiểm tra cấu hình Real-time Broadcasting.') }}
                            </p>
                            <div class="flex items-center mb-2">
                                <span class="font-bold mr-2">{{ __('Cấu hình:') }}</span>
                                @if ($broadcastConfigStatus['status'] === 'active')
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                        {{ __('Hoạt động') }}
                                    </span>
                                @else
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                        {{ __('Không hoạt động') }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                {{ $broadcastConfigStatus['message'] }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-bold">{{ __('Driver:') }}</span>
                                {{ $broadcastConfigStatus['driver'] }}
                            </p>
                            @if (!empty($broadcastConfigStatus['config']))
                                <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    @foreach ($broadcastConfigStatus['config'] as $key => $value)
                                        <li><span class="font-bold">{{ $key }}:</span> {{ $value }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <div class="mt-4 pt-4 border-t border-gray-300 dark:border-gray-600">
                                <h5 class="text-md font-semibold mb-2">{{ __('Kiểm tra hoạt động:') }}</h5>
                                <form action="{{ route('admin.system.test_broadcast') }}" method="POST">
                                    @csrf
                                    <x-primary-button type="submit"
                                        class="bg-indigo-600 hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900">
                                        {{ __('Gửi sự kiện Test') }}
                                    </x-primary-button>
                                </form>
                                @if (session('broadcast_test_result'))
                                    @php $result = session('broadcast_test_result'); @endphp
                                    <div
                                        class="mt-2 text-sm {{ $result['status'] === 'success' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $result['message'] }}
                                    </div>
                                @endif
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                    {{ __('Kiểm tra log hoặc dashboard của dịch vụ broadcasting (Pusher, Redis, v.v.) để xác minh.') }}
                                </p>
                            </div>
                        </div>

                        {{-- NEW Card: Trạng thái và Test Email --}}
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-md">
                            <h4 class="text-xl font-semibold mb-3">{{ __('Trạng thái Email') }}</h4>
                            <p class="text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Kiểm tra cấu hình gửi Email của hệ thống.') }}
                            </p>
                            <div class="flex items-center mb-2">
                                <span class="font-bold mr-2">{{ __('Cấu hình:') }}</span>
                                @if ($emailConfigStatus['status'] === 'active')
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                        {{ __('Hoạt động') }}
                                    </span>
                                @else
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                        {{ __('Không hoạt động') }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                {{ $emailConfigStatus['message'] }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-bold">{{ __('Driver:') }}</span> {{ $emailConfigStatus['driver'] }}
                            </p>
                            @if (!empty($emailConfigStatus['config']))
                                <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    @foreach ($emailConfigStatus['config'] as $key => $value)
                                        <li><span class="font-bold">{{ $key }}:</span> {{ $value }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <div class="mt-4 pt-4 border-t border-gray-300 dark:border-gray-600">
                                <h5 class="text-md font-semibold mb-2">{{ __('Kiểm tra hoạt động:') }}</h5>
                                <form action="{{ route('admin.system.test_email') }}" method="POST">
                                    @csrf
                                    <x-input-label for="email_recipient" :value="__('Email nhận Test')" class="sr-only" />
                                    <x-text-input id="email_recipient" name="email_recipient" type="email"
                                        class="mt-1 block w-full mb-2" placeholder="Nhập email nhận test" required />
                                    <x-primary-button type="submit"
                                        class="bg-indigo-600 hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900">
                                        {{ __('Gửi Email Test') }}
                                    </x-primary-button>
                                </form>
                                @if (session('email_test_result'))
                                    @php $result = session('email_test_result'); @endphp
                                    <div
                                        class="mt-2 text-sm {{ $result['status'] === 'success' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $result['message'] }}
                                    </div>
                                @endif
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                    {{ __('Một email sẽ được gửi đến địa chỉ bạn cung cấp.') }}
                                </p>
                            </div>
                        </div>

                        {{-- NEW Card: Trạng thái và Test Slack --}}
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-md">
                            <h4 class="text-xl font-semibold mb-3">{{ __('Trạng thái Slack') }}</h4>
                            <p class="text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Kiểm tra cấu hình tích hợp Slack.') }}
                            </p>
                            <div class="flex items-center mb-2">
                                <span class="font-bold mr-2">{{ __('Cấu hình:') }}</span>
                                @if ($slackConfigStatus['status'] === 'active')
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                        {{ __('Hoạt động') }}
                                    </span>
                                @else
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                        {{ __('Không hoạt động') }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                {{ $slackConfigStatus['message'] }}
                            </p>
                            @if (!empty($slackConfigStatus['config']))
                                <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    @foreach ($slackConfigStatus['config'] as $key => $value)
                                        <li><span class="font-bold">{{ $key }}:</span> {{ $value }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <div class="mt-4 pt-4 border-t border-gray-300 dark:border-gray-600">
                                <h5 class="text-md font-semibold mb-2">{{ __('Kiểm tra hoạt động:') }}</h5>
                                <form action="{{ route('admin.system.test_slack') }}" method="POST">
                                    @csrf
                                    <x-primary-button type="submit"
                                        class="bg-indigo-600 hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900">
                                        {{ __('Gửi thông báo Test') }}
                                    </x-primary-button>
                                </form>
                                @if (session('slack_test_result'))
                                    @php $result = session('slack_test_result'); @endphp
                                    <div
                                        class="mt-2 text-sm {{ $result['status'] === 'success' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $result['message'] }}
                                    </div>
                                @endif
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                    {{ __('Một thông báo sẽ được gửi tới kênh Slack đã cấu hình.') }}
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
