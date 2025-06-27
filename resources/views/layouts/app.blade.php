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
    <script type="text/javascript" src="{{ asset('js/toastify-js.js') }}"></script>

    @auth
        <script>
            window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token(),
                'user' => Auth::user(),
            ]) !!};
        </script>
    @endauth
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
    </div>
    <script src="{{ asset('js/jquery-3.3.1.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" crossorigin="anonymous"></script>
    </script>
    @include('layouts.script_custom')
    <script src="{{ asset('js/chatbot-widget.js') }}"></script>
    @stack('scripts')
</body>

</html>
