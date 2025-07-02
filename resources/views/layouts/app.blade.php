<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/favico.png') }}" type="image/x-icon">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" crossorigin="anonymous">
    {{-- Script cho Toastify --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('css/toastify.min.css') }}">

    @stack('styles')
</head>

<body class="bg-light">
    <div class="min-vh-100 d-flex flex-column">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow-sm border-bottom">
                <div class="container p-3">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-grow-1">
            {{ $slot }}
        </main>

        <footer class="bg-dark text-white py-3 mt-auto">
            <div class="container d-flex justify-content-between">
                <div class="mb-0">
                    &copy; {{ date('Y') }} <a href="https://trunglt706.xyz/profile" target="_blank"
                        rel="noopener noreferrer" class="text-decoration-none text-white">trunglt706</a>
                </div>
                <div class="mb-0">
                    <a class="text-decoration-none text-white" href="/other/privacy-policy" target="_blank"
                        rel="noopener noreferrer">
                        {{ __('Privacy Policy') }}
                    </a>
                    |
                    <a class="text-decoration-none text-white" href="/other/terms-of-service" target="_blank"
                        rel="noopener noreferrer">
                        {{ __('Terms of Service') }}
                    </a>
                </div>
            </div>
        </footer>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="{{ asset('js/jquery-3.3.1.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" crossorigin="anonymous"></script>
    <script type="text/javascript" src="{{ asset('js/toastify-js.js') }}"></script>
    @stack('scripts')
    @include('layouts.script_custom')
    <script src="{{ asset('js/chatbot-widget.js') }}"></script>

    {{-- Vite JS --}}
    @viteReactRefresh

    @vite(['resources/js/app.jsx'])
</body>

</html>
