<x-app-layout>
    <div class="py-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-12 col-lg-9">
                    {{-- Modules --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h3 class="h5 fw-semibold mb-4">{{ __('Published Modules') }}</h3>

                            @if ($publishedModules->isEmpty())
                                <p class="text-muted">{{ __('No modules have been published yet.') }}</p>
                            @else
                                <div class="row">
                                    @foreach ($publishedModules as $module)
                                        <div class="col-md-4 mb-4">
                                            <div class="card h-100 border">
                                                <div class="card-body d-flex flex-column">
                                                    <h4 class="h6 fw-bold text-primary mb-2">
                                                        {{ $module->name }}
                                                    </h4>
                                                    <p class="small text-muted mb-1">
                                                        <strong>{{ __('Version') }}:</strong>
                                                        {{ $module->version ?? __('N/A') }}
                                                    </p>
                                                    <p class="small text-muted mb-3">
                                                        <strong>{{ __('Release Date') }}:</strong>
                                                        {{ $module->created_at ? $module->created_at->format('d/m/Y') : __('N/A') }}
                                                    </p>

                                                    @if ($module->sponsors->isNotEmpty())
                                                        <div class="mt-auto small text-secondary">
                                                            {{ __('Sponsored by') }}:
                                                            @foreach ($module->sponsors as $sponsor)
                                                                <span
                                                                    class="badge bg-info text-dark me-1 mb-1 d-inline-flex align-items-center">
                                                                    @if ($sponsor->getFirstMediaUrl('sponsor_logo'))
                                                                        <img src="{{ $sponsor->getFirstMediaUrl('sponsor_logo') }}"
                                                                            alt="{{ $sponsor->name }}"
                                                                            class="rounded-circle me-1"
                                                                            style="height: 18px; width: 18px;">
                                                                    @endif
                                                                    {{ $sponsor->name }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    <div class="mt-auto d-flex gap-2 pt-3">
                                                        @can('show modules')
                                                            <a href="{{ $module->url }}" target="_blank"
                                                                class="btn btn-primary btn-sm flex-grow-1">
                                                                <i class="bi bi-play-circle-fill me-1"></i>
                                                                {{ __('Run Demo') }}
                                                            </a>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div> {{-- Kết thúc hàng Bootstrap --}}
                            @endif
                        </div>
                    </div>
                    {{-- Posts --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h3 class="h5 fw-semibold mb-4">{{ __('Latest Posts') }}</h3>

                            @if ($publishedPosts->isEmpty())
                                <p class="text-muted">
                                    {{ __('No data.') }}</p>
                            @else
                                @foreach ($publishedPosts as $post)
                                    <div class="mb-4 pb-3 border-bottom"> {{-- Mỗi bài viết là một khối riêng biệt --}}
                                        <div class="d-flex align-items-start"> {{-- Sử dụng flexbox để căn chỉnh ngang --}}
                                            {{-- Thumbnail ở bên trái --}}
                                            <div class="flex-shrink-0 me-3"> {{-- flex-shrink-0 ngăn thumbnail co lại, me-3 thêm khoảng cách bên phải --}}
                                                @if ($post->getFirstMedia('post_thumbnail'))
                                                    <img src="{{ $post->getFirstMediaUrl('post_thumbnail', 'thumb') }}"
                                                        alt="{{ $post->title }}" class="img-fluid rounded"
                                                        style="max-width: 150px; height: auto;"> {{-- Sử dụng thumbnail nhỏ --}}
                                                @else
                                                    {{-- Placeholder nếu không có thumbnail --}}
                                                    <img src="https://via.placeholder.com/150x100?text=No+Image"
                                                        alt="No Image" class="img-fluid rounded"
                                                        style="max-width: 150px; height: auto;">
                                                @endif
                                            </div>

                                            {{-- Nội dung bài viết ở bên phải --}}
                                            <div class="flex-grow-1"> {{-- flex-grow-1 để nội dung chiếm phần còn lại của không gian --}}
                                                <h4 class="h6 fw-bold mb-1">
                                                    <a href="{{ route('posts.show', $post->slug) }}"
                                                        class="link-primary text-decoration-none">
                                                        {{ $post->title }}
                                                    </a>
                                                </h4>
                                                <p class="small text-secondary mb-2">
                                                    <i class="bi bi-clock"></i> {{ __('Published at') }}
                                                    {{ $post->published_at?->format('d/m/Y H:i') }}
                                                    {{-- Thêm giờ phút --}}
                                                    {{ __('by') }} {{ $post->user->name ?? __('N/A') }}
                                                </p>
                                                <p class="text-muted mb-2">
                                                    {{ Str::limit($post->description, 150) }}
                                                    {{-- Sử dụng short_description và giới hạn độ dài --}}
                                                </p>

                                                {{-- Hiển thị Tags --}}
                                                @if (!empty($post->tags))
                                                    <div class="mt-2">
                                                        @foreach ($post->tags as $tag)
                                                            <span
                                                                class="badge bg-secondary me-1 mb-1">{{ $tag }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    {{-- Activity Log --}}
                    {{-- <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h3 class="h6 fw-semibold mb-4">{{ __('Your Activity Log') }}</h3>
                            <x-activiti-timeline :activities="$userActivities" />
                        </div>
                    </div> --}}
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
                            <h3 class="h6 fw-semibold mb-3">{{ __('Support the project') }}</h3>
                            <p class="small text-muted mb-3">
                                {{ __('If you find this project useful, please consider supporting us to help maintain and develop it!') }}
                            </p>
                            <img src="{{ asset('images/donate_qr.png') }}" alt="Support QR Code"
                                class="img-fluid rounded shadow mb-2" style="max-width: 200px;">
                            <div class="small text-muted mt-2">{{ __('Scan the QR code to support.') }}</div>
                            <p class="small text-muted">
                                {{ __('Every contribution is greatly appreciated!') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
