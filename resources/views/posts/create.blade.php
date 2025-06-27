<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold h4 text-dark mb-0">
            {{ __('Tạo Bài viết Mới') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="card shadow-sm">
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <div class="fw-semibold mb-2">Có lỗi xảy ra:</div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <x-input-label required for="title" :value="__('Post Title')" />
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                class="form-control mt-1">
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <label for="content" class="form-label">
                                {{ __('Nội dung Bài viết') }} <span class="text-danger">*</span>
                            </label>
                            <input id="trix-content" type="hidden" name="content" value="{{ old('content') }}">
                            <trix-editor input="trix-content" class="form-control mt-1 min-vh-25"></trix-editor>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <label for="thumbnail" class="form-label">
                                {{ __('Ảnh đại diện') }}
                            </label>
                            <input type="file" name="thumbnail" id="thumbnail" class="form-control mt-1">
                            <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" name="is_published" id="is_published" value="1"
                                {{ old('is_published') ? 'checked' : '' }} class="form-check-input">
                            <label for="is_published" class="form-check-label">
                                {{ __('Xuất bản ngay?') }}
                            </label>
                            <x-input-error :messages="$errors->get('is_published')" class="mt-2" />
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary text-uppercase fw-semibold text-xs">
                                {{ __('Lưu Bài viết') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
