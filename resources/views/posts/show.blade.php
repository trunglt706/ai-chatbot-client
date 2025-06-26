<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ $post->title }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Bởi <span class="font-semibold">{{ $post->user->name ?? 'Người dùng ẩn danh' }}</span> vào
                        {{ $post->published_at?->format('d/m/Y H:i') }}
                    </p>

                    @if ($post->thumbnail)
                        <img src="{{ $post->thumbnail->full_url }}" alt="{{ $post->title }}"
                            class="w-full h-auto rounded-lg mb-6">
                    @endif

                    <div class="prose dark:prose-invert max-w-none trix-content">
                        {{-- Trix content renders directly as HTML --}}
                        {!! $post->content !!}
                    </div>

                    <div class="flex justify-start mt-8">
                        <a href="{{ route('posts.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Quay lại danh sách bài viết') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
