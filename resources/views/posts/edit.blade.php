<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chỉnh sửa Bài viết') }}: {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if ($errors->any())
                        <div class="mb-4 font-medium text-red-600 bg-red-100 p-3 rounded-md">
                            Có lỗi xảy ra:
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.posts.update', $post->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Tiêu đề Bài viết') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm">
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Nội dung Bài viết') }} <span class="text-red-500">*</span>
                            </label>
                            <input id="trix-content" type="hidden" name="content"
                                value="{{ old('content', $post->content) }}">
                            <trix-editor input="trix-content"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm min-h-[200px]"></trix-editor>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <label for="thumbnail" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Ảnh đại diện') }}
                            </label>
                            @if ($post->thumbnail)
                                <div class="mt-2 mb-4">
                                    <img src="{{ $post->thumbnail->full_url }}" alt="Thumbnail"
                                        class="max-w-xs h-auto rounded-md">
                                </div>
                            @endif
                            <input type="file" name="thumbnail" id="thumbnail"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                            <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                        </div>

                        <div class="mb-4 flex items-center">
                            <input type="checkbox" name="is_published" id="is_published" value="1"
                                {{ old('is_published', $post->is_published) ? 'checked' : '' }}
                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                            <label for="is_published" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Xuất bản ngay?') }}
                            </label>
                            <x-input-error :messages="$errors->get('is_published')" class="mt-2" />
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Cập nhật Bài viết') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
