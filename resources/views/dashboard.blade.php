<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold h4 text-dark mb-0">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="py-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-12 col-lg-8">
                    {{-- Modules --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h3 class="h5 fw-semibold mb-4">{{ __('Các Module đã Phát hành') }}</h3>
                            @forelse ($publishedModules as $module)
                                <div class="mb-4 pb-2 border-bottom">
                                    <h4 class="h6 fw-bold text-primary mb-1">
                                        {{ $module->name }}
                                    </h4>
                                    <p class="small text-secondary mb-2">
                                        {{ Str::limit($module->description, 100) }}
                                    </p>
                                    @if ($module->sponsors->isNotEmpty())
                                        <div class="mt-2 small text-secondary">
                                            {{ __('Được tài trợ bởi:') }}
                                            @foreach ($module->sponsors as $sponsor)
                                                <span
                                                    class="badge bg-info text-dark me-1 mb-1 d-inline-flex align-items-center">
                                                    @if ($sponsor->image)
                                                        <img src="{{ Storage::url($sponsor->image) }}"
                                                            alt="{{ $sponsor->name }}" class="rounded-circle me-1"
                                                            style="height: 18px; width: 18px;">
                                                    @endif
                                                    {{ $sponsor->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="d-flex gap-2 mt-2">
                                        @can('show modules')
                                            <a href="{{ route('modules.show', $module->id) }}" target="_blank"
                                                class="btn btn-primary btn-sm">
                                                {{ __('Chạy Demo') }}
                                            </a>
                                        @endcan
                                        @can('view module details')
                                            <a href="{{ route('modules.show', $module->slug) }}"
                                                class="btn btn-outline-secondary btn-sm">
                                                {{ __('Xem Module') }}
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">
                                    {{ __('Hiện chưa có module nào được phát hành.') }}</p>
                            @endforelse
                            <div class="mt-4 text-end">
                                <a href="{{ route('modules.index') }}" class="link-primary text-decoration-underline">
                                    {{ __('Quản lý tất cả Modules') }} &raquo;
                                </a>
                            </div>
                        </div>
                    </div>
                    {{-- Posts --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h3 class="h5 fw-semibold mb-4">{{ __('Bài viết Mới nhất') }}</h3>
                            @forelse ($publishedPosts as $post)
                                <div class="mb-4 pb-2 border-bottom">
                                    <h4 class="h6 fw-bold mb-1">
                                        <a href="{{ route('posts.show', $post->slug) }}"
                                            class="link-primary text-decoration-underline">
                                            {{ $post->title }}
                                        </a>
                                    </h4>
                                    <p class="small text-secondary">
                                        {{ __('Xuất bản vào') }} {{ $post->published_at?->format('d/m/Y') }}
                                        {{ __('bởi') }} {{ $post->user->name ?? 'N/A' }}
                                    </p>
                                </div>
                            @empty
                                <p class="text-muted">
                                    {{ __('Hiện chưa có bài viết nào được phát hành.') }}</p>
                            @endforelse
                            <div class="mt-4 text-end">
                                <a href="{{ route('posts.index') }}" class="link-primary text-decoration-underline">
                                    {{ __('Xem tất cả bài viết') }} &raquo;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    {{-- Activity Log --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h3 class="h6 fw-semibold mb-4">{{ __('Nhật ký Hành động của bạn') }}</h3>
                            <x-activiti-timeline :activities="$userActivities" />
                        </div>
                    </div>
                    {{-- Banner --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-body text-center">
                            <a target="_blank" href="https://my.bkns.net/?affid=286">
                                <img width="100%" height="200"
                                    src="https://www.bkns.vn/wp-content/uploads/2024/06/cloud-0d.jpg.webp"
                                    alt="banner_250x250" class="img-fluid rounded mb-2">
                            </a>
                            <p class="small text-muted mt-2">
                                {{ __('By Server At Here') }}</p>
                        </div>
                    </div>
                    {{-- Donate QR Code Banner --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-body text-center">
                            <h3 class="h6 fw-semibold mb-3">{{ __('Ủng hộ dự án') }}</h3>
                            <p class="text-muted mb-3">
                                {{ __('Nếu bạn thấy dự án này hữu ích, hãy cân nhắc ủng hộ để chúng tôi có động lực duy trì và phát triển!') }}
                            </p>
                            <img src="{{ asset('images/donate_qr.png') }}" alt="QR Code ủng hộ"
                                class="img-fluid rounded shadow mb-2" style="max-width: 200px;">
                            <p class="small text-muted mt-2">{{ __('Quét mã QR để ủng hộ.') }}</p>
                            <p class="small text-muted mt-1">
                                {{ __('Mọi sự đóng góp đều rất quý báu!') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
