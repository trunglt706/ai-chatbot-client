<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold h4 text-dark mb-0">
            {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">

                            <h1 class="h2 fw-bold text-dark mb-4">{{ $post->title }}</h1>
                            <p class="small text-secondary mb-4">
                                Bởi <span class="fw-semibold">{{ $post->user->name ?? 'Người dùng ẩn danh' }}</span> vào
                                {{ $post->published_at?->format('d/m/Y H:i') }}
                            </p>

                            @if ($post->thumbnail)
                                <img src="{{ $post->thumbnail->full_url }}" alt="{{ $post->title }}"
                                    class="img-fluid rounded mb-4">
                            @endif

                            <div class="mb-5">
                                {{-- Trix content renders directly as HTML --}}
                                {!! $post->content !!}
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('posts.index') }}"
                                    class="btn btn-secondary text-uppercase fw-semibold text-xs">
                                    {{ __('Quay lại danh sách bài viết') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
