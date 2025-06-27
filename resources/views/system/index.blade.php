<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold h4 text-dark mb-0">
            {{ __('Quản lý Hệ thống') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message />

            <h3 class="h4 fw-bold mb-4">{{ __('Tổng quan hệ thống') }}</h3>

            <div class="row g-4">
                {{-- Card: Thông tin Symbolic Link --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="bg-light p-4 rounded-3 shadow-sm h-100">
                        <h4 class="h6 fw-bold mb-3">{{ __('Trạng thái Storage Link') }}</h4>
                        <p class="text-secondary mb-2">
                            {{ __('Đường dẫn công khai cho file tải lên.') }}
                        </p>
                        <div class="d-flex align-items-center mb-3">
                            <span class="fw-bold me-2">{{ __('Trạng thái:') }}</span>
                            @if ($storageLinkExists)
                                <span class="badge bg-success">{{ __('Đã tạo') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('Chưa tạo') }}</span>
                            @endif
                        </div>
                        @if (!$storageLinkExists)
                            <form action="{{ route('system.create_storage_link') }}" method="POST">
                                @csrf
                                <x-primary-button type="submit">
                                    {{ __('Tạo Symbolic Link ngay') }}
                                </x-primary-button>
                            </form>
                            <p class="small text-muted mt-2">
                                {{ __('Việc này cho phép các file được tải lên có thể truy cập công khai.') }}
                            </p>
                        @else
                            <p class="text-secondary">
                                {{ __('Symbolic link đã tồn tại và hoạt động.') }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Card: Thông tin Dung lượng Public Storage --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="bg-light p-4 rounded-3 shadow-sm h-100">
                        <h4 class="h6 fw-bold mb-3">{{ __('Dung lượng Public Storage') }}</h4>
                        <p class="text-secondary mb-2">
                            {{ __('Tổng dung lượng các file được tải lên công khai.') }}
                        </p>
                        <div class="d-flex align-items-center mb-3">
                            <span class="fw-bold me-2">{{ __('Dung lượng:') }}</span>
                            <span>{{ $publicStorageSize }}</span>
                        </div>
                        <p class="small text-muted mt-2">
                            {{ __('Bao gồm hình ảnh nhà tài trợ, ảnh bài viết, v.v.') }}
                        </p>
                    </div>
                </div>

                {{-- Card: Thông tin chung của hệ thống --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="bg-light p-4 rounded-3 shadow-sm h-100">
                        <h4 class="h6 fw-bold mb-3">{{ __('Thông tin chung') }}</h4>
                        <p class="mb-2">
                            <span class="fw-bold">{{ __('Phiên bản Laravel:') }}</span> {{ app()->version() }}
                        </p>
                        <p class="mb-2">
                            <span class="fw-bold">{{ __('Môi trường:') }}</span> {{ app()->environment() }}
                        </p>
                        <p class="text-secondary mb-0">
                            {{ __('Giúp kiểm tra cấu hình và môi trường hiện tại của ứng dụng.') }}
                        </p>
                    </div>
                </div>

                {{-- Card: Xóa Cache Toàn bộ Hệ thống --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="bg-light p-4 rounded-3 shadow-sm h-100">
                        <h4 class="h6 fw-bold mb-3">{{ __('Xóa Cache Hệ thống') }}</h4>
                        <p class="text-secondary mb-3">
                            {{ __('Xóa tất cả các cache (cấu hình, route, view, ứng dụng) để làm mới hệ thống.') }}
                        </p>
                        <form action="{{ route('system.clear_all_data') }}" method="POST">
                            @csrf
                            <x-primary-button type="submit" class="btn-warning">
                                {{ __('Xóa Toàn bộ Cache') }}
                            </x-primary-button>
                        </form>
                        <p class="small text-muted mt-2">
                            {{ __('Nên làm khi có thay đổi về cấu hình hoặc mã nguồn.') }}
                        </p>
                    </div>
                </div>

                {{-- Card: Trạng thái và Test Broadcasting --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="bg-light p-4 rounded-3 shadow-sm h-100">
                        <h4 class="h6 fw-bold mb-3">{{ __('Trạng thái Broadcasting') }}</h4>
                        <p class="text-secondary mb-2">
                            {{ __('Kiểm tra cấu hình Real-time Broadcasting.') }}
                        </p>
                        <div class="d-flex align-items-center mb-2">
                            <span class="fw-bold me-2">{{ __('Cấu hình:') }}</span>
                            @if ($broadcastConfigStatus['status'] === 'active')
                                <span class="badge bg-success">{{ __('Hoạt động') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('Không hoạt động') }}</span>
                            @endif
                        </div>
                        <p class="small text-muted mb-2">
                            {{ $broadcastConfigStatus['message'] }}
                        </p>
                        <p class="small text-muted mb-2">
                            <span class="fw-bold">{{ __('Driver:') }}</span>
                            {{ $broadcastConfigStatus['driver'] }}
                        </p>
                        @if (!empty($broadcastConfigStatus['config']))
                            <ul class="small text-muted mb-2 ps-3">
                                @foreach ($broadcastConfigStatus['config'] as $key => $value)
                                    <li><span class="fw-bold">{{ $key }}:</span> {{ $value }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="mt-3 pt-3 border-top">
                            <h5 class="fw-semibold mb-2">{{ __('Kiểm tra hoạt động:') }}</h5>
                            <form action="{{ route('system.test_broadcast') }}" method="POST">
                                @csrf
                                <x-primary-button type="submit" class="btn-primary">
                                    {{ __('Gửi sự kiện Test') }}
                                </x-primary-button>
                            </form>
                            @if (session('broadcast_test_result'))
                                @php $result = session('broadcast_test_result'); @endphp
                                <div
                                    class="mt-2 small {{ $result['status'] === 'success' ? 'text-success' : 'text-danger' }}">
                                    {{ $result['message'] }}
                                </div>
                            @endif
                            <p class="small text-muted mt-2">
                                {{ __('Kiểm tra log hoặc dashboard của dịch vụ broadcasting (Pusher, Redis, v.v.) để xác minh.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Card: Trạng thái và Test Email --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="bg-light p-4 rounded-3 shadow-sm h-100">
                        <h4 class="h6 fw-bold mb-3">{{ __('Trạng thái Email') }}</h4>
                        <p class="text-secondary mb-2">
                            {{ __('Kiểm tra cấu hình gửi Email của hệ thống.') }}
                        </p>
                        <div class="d-flex align-items-center mb-2">
                            <span class="fw-bold me-2">{{ __('Cấu hình:') }}</span>
                            @if ($emailConfigStatus['status'] === 'active')
                                <span class="badge bg-success">{{ __('Hoạt động') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('Không hoạt động') }}</span>
                            @endif
                        </div>
                        <p class="small text-muted mb-2">
                            {{ $emailConfigStatus['message'] }}
                        </p>
                        <p class="small text-muted mb-2">
                            <span class="fw-bold">{{ __('Driver:') }}</span> {{ $emailConfigStatus['driver'] }}
                        </p>
                        @if (!empty($emailConfigStatus['config']))
                            <ul class="small text-muted mb-2 ps-3">
                                @foreach ($emailConfigStatus['config'] as $key => $value)
                                    <li><span class="fw-bold">{{ $key }}:</span> {{ $value }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="mt-3 pt-3 border-top">
                            <h5 class="fw-semibold mb-2">{{ __('Kiểm tra hoạt động:') }}</h5>
                            <form action="{{ route('system.test_email') }}" method="POST">
                                @csrf
                                <x-input-label for="email_recipient" :value="__('Email nhận Test')" class="visually-hidden" />
                                <x-text-input id="email_recipient" name="email_recipient" type="email"
                                    class="form-control mb-2" placeholder="Nhập email nhận test" required />
                                <x-primary-button type="submit" class="btn-primary">
                                    {{ __('Gửi Email Test') }}
                                </x-primary-button>
                            </form>
                            @if (session('email_test_result'))
                                @php $result = session('email_test_result'); @endphp
                                <div
                                    class="mt-2 small {{ $result['status'] === 'success' ? 'text-success' : 'text-danger' }}">
                                    {{ $result['message'] }}
                                </div>
                            @endif
                            <p class="small text-muted mt-2">
                                {{ __('Một email sẽ được gửi đến địa chỉ bạn cung cấp.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Card: Trạng thái và Test Slack --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="bg-light p-4 rounded-3 shadow-sm h-100">
                        <h4 class="h6 fw-bold mb-3">{{ __('Trạng thái Slack') }}</h4>
                        <p class="text-secondary mb-2">
                            {{ __('Kiểm tra cấu hình tích hợp Slack.') }}
                        </p>
                        <div class="d-flex align-items-center mb-2">
                            <span class="fw-bold me-2">{{ __('Cấu hình:') }}</span>
                            @if ($slackConfigStatus['status'] === 'active')
                                <span class="badge bg-success">{{ __('Hoạt động') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('Không hoạt động') }}</span>
                            @endif
                        </div>
                        <p class="small text-muted mb-2">
                            {{ $slackConfigStatus['message'] }}
                        </p>
                        @if (!empty($slackConfigStatus['config']))
                            <ul class="small text-muted mb-2 ps-3">
                                @foreach ($slackConfigStatus['config'] as $key => $value)
                                    <li><span class="fw-bold">{{ $key }}:</span> {{ $value }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="mt-3 pt-3 border-top">
                            <h5 class="fw-semibold mb-2">{{ __('Kiểm tra hoạt động:') }}</h5>
                            <form action="{{ route('system.test_slack') }}" method="POST">
                                @csrf
                                <x-primary-button type="submit" class="btn-primary">
                                    {{ __('Gửi thông báo Test') }}
                                </x-primary-button>
                            </form>
                            @if (session('slack_test_result'))
                                @php $result = session('slack_test_result'); @endphp
                                <div
                                    class="mt-2 small {{ $result['status'] === 'success' ? 'text-success' : 'text-danger' }}">
                                    {{ $result['message'] }}
                                </div>
                            @endif
                            <p class="small text-muted mt-2">
                                {{ __('Một thông báo sẽ được gửi tới kênh Slack đã cấu hình.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
