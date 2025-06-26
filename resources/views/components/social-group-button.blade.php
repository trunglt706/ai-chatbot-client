@if (env('APP_ENV') == 'production')
    {{-- NEW: Socialite Login Buttons --}}
    <div class="mt-6">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    {{ __('Or') }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-1 mt-6">
            <a href="{{ route('socialite.redirect', ['provider' => 'google']) }}"
                class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 dark:border-gray-600">
                <img src="https://www.google.com/favicon.ico" alt="Google" class="w-5 h-5 mr-2">
                {{ __('Google') }}
            </a>
            <a href="{{ route('socialite.redirect', ['provider' => 'facebook']) }}"
                class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 dark:border-gray-600">
                <img src="https://www.facebook.com/favicon.ico" alt="Facebook" class="w-5 h-5 mr-2">
                {{ __('Facebook') }}
            </a>
            <a href="{{ route('socialite.redirect', ['provider' => 'github']) }}"
                class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 dark:border-gray-600">
                <img src="https://github.githubassets.com/favicons/favicon.png" alt="GitHub" class="w-5 h-5 mr-2">
                {{ __('GitHub') }}
            </a>
        </div>
    </div>
@endif
