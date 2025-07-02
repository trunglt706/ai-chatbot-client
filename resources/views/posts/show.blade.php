<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold h4 text-dark mb-0">
            {{ $post->title }}
        </h2>
        <p class="text-muted small mt-1">
            <i class="bi bi-clock"></i> {{ __('Published by') }} {{ $post->user->name ?? __('N/A') }}
            {{ __('on') }} {{ $post->published_at?->format('d/m/Y H:i') }}
        </p>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">

                            {{-- Thumbnail bài viết --}}
                            @if ($thumbnailUrl)
                                <div class="text-center mb-4">
                                    <img src="{{ $thumbnailUrl }}" alt="{{ $post->title }}" class="img-fluid rounded"
                                        style="max-height: 400px; width: auto;">
                                </div>
                            @endif

                            {{-- Mô tả ngắn --}}
                            <p class="lead text-secondary mb-4">
                                {{ $post->short_description }}
                            </p>

                            <hr>

                            {{-- Nội dung bài viết (Quill content) --}}
                            <div class="post-content">
                                {!! $post->content !!} {{-- Hiển thị nội dung HTML từ Quill --}}
                            </div>

                            <hr class="my-4">

                            {{-- Tags --}}
                            @if (!empty($post->tags))
                                <div class="mb-4">
                                    <h5 class="fw-semibold">{{ __('Tags') }}:</h5>
                                    @foreach ($post->tags as $tag)
                                        <span class="badge bg-primary me-1">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- Tùy chỉnh CSS cho nội dung bài viết từ Quill nếu cần --}}
<style>
    .post-content h1,
    .post-content h2,
    .post-content h3,
    .post-content h4,
    .post-content h5,
    .post-content h6 {
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }

    .post-content p {
        margin-bottom: 1rem;
        line-height: 1.6;
    }

    .post-content img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 1rem auto;
        border-radius: 0.25rem;
    }

    .post-content ul,
    .post-content ol {
        margin-bottom: 1rem;
        padding-left: 1.5rem;
    }

    .post-content a {
        color: var(--bs-link-color);
        text-decoration: underline;
    }

    /* Thêm các style khác cho Quill output nếu cần */
</style>
