<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold fs-4 text-dark">
            {{ __('Chỉnh sửa Bài viết') }}: {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="container">
            <div class="card shadow-sm">
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Có lỗi xảy ra:</strong>
                            <ul class="mt-2 mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <x-input-label required for="title" :value="__('Post Title')" />
                            <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}"
                                required class="form-control">
                            <x-input-error :messages="$errors->get('title')" class="mt-2 text-danger" />
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">
                                {{ __('Nội dung Bài viết') }} <span class="text-danger">*</span>
                            </label>
                            <input id="trix-content" type="hidden" name="content"
                                value="{{ old('content', $post->content) }}">
                            <trix-editor input="trix-content" class="form-control"
                                style="min-height: 200px;"></trix-editor>
                            <x-input-error :messages="$errors->get('content')" class="mt-2 text-danger" />
                        </div>

                        <div class="mb-3">
                            <label for="thumbnail" class="form-label">
                                {{ __('Ảnh đại diện') }}
                            </label>
                            @if ($post->thumbnail)
                                <div class="mb-3">
                                    <img src="{{ $post->thumbnail->full_url }}" alt="Thumbnail"
                                        class="img-fluid rounded" style="max-width: 200px;">
                                </div>
                            @endif
                            <input type="file" name="thumbnail" id="thumbnail" class="form-control">
                            <x-input-error :messages="$errors->get('thumbnail')" class="mt-2 text-danger" />
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_published" id="is_published" value="1"
                                {{ old('is_published', $post->is_published) ? 'checked' : '' }}
                                class="form-check-input">
                            <label for="is_published" class="form-check-label">
                                {{ __('Xuất bản ngay?') }}
                            </label>
                            <x-input-error :messages="$errors->get('is_published')" class="mt-2 text-danger" />
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Cập nhật Bài viết') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
