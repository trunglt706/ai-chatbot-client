<x-app-layout>

    {{-- Quill --}}
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    {{-- Tagify --}}
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />

    <x-slot name="header">
        <x-breadcrumb :items="[['name' => 'Posts', 'url' => route('posts.index')], ['name' => 'Create New Post']]" />
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message />
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('posts.store') }}" id="form-post" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <x-input-label required for="title" :value="__('Post Title')" />
                                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                    class="form-control mt-1">
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>
                            <div class="col-md-5 mb-3">
                                <x-input-label for="thumbnail" :value="__('Thumbnail Image') . __(' (JPG, PNG, JPEG, GIF, SVG)')" />
                                <input type="file" accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml"
                                    name="thumbnail" id="thumbnail" class="form-control mt-1">
                                <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <x-input-label required for="description" :value="__('Description')" />
                            <textarea name="description" id="description" class="form-control mt-1" rows="3" required>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="content" :value="__('Post Content')" />
                            <input type="hidden" name="content" id="content">
                            <div id="content_editor" style="min-height: 200px;">{!! old('content') !!}</div>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="tags" :value="__('Tags')" />
                            <input name="tags" id="tags_input" value="{{ old('tags') }}" class="form-control mt-1">
                            <small class="form-text text-muted">{{ __('Enter tags separated by commas.') }}</small>
                            <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" name="is_published" id="is_published" value="1"
                                {{ old('is_published') ? 'checked' : '' }} class="form-check-input">
                            <label for="is_published" class="form-check-label">
                                {{ __('Publish now?') }}
                            </label>
                            <x-input-error :messages="$errors->get('is_published')" class="mt-2" />
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary text-uppercase fw-semibold text-xs">
                                <i class="bi bi-floppy-fill"></i> {{ __('Save Post') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <script>
        var quill = new Quill('#content_editor', {
            theme: 'snow'
        });

        var form = document.querySelector('#form-post');
        var contentInput = document.querySelector('#content');

        if (form && contentInput) {
            form.addEventListener('submit', function() {
                contentInput.value = quill.root.innerHTML;
            });
        }

        // Tagify
        var input = document.querySelector('#tags_input');
        var tagify = new Tagify(input, {
            maxTags: 10,
            dropdown: {
                maxItems: 20,
                classname: "tags-look",
                enabled: 0,
                closeOnSelect: false
            }
        });
    </script>
</x-app-layout>
