<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/favico.png') }}" type="image/x-icon">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @auth
        <script>
            window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token(),
                'user' => Auth::user(),
            ]) !!};
        </script>
    @endauth

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Lắng nghe sự kiện click trên tất cả các nút có lớp .toggle-password
            $('.toggle-password').on('click', function() {
                console.log('ok');

                var targetId = $(this).data('target'); // Lấy ID của input được điều khiển
                var passwordInput = $('#' + targetId); // Tìm input bằng ID

                // Kiểm tra kiểu hiện tại của input
                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text'); // Chuyển sang text
                    $(this).find('i').removeClass('bi-eye').addClass(
                        'bi-eye-slash'); // Đổi biểu tượng sang mắt gạch chéo
                } else {
                    passwordInput.attr('type', 'password'); // Chuyển lại sang password
                    $(this).find('i').removeClass('bi-eye-slash').addClass(
                        'bi-eye'); // Đổi biểu tượng sang mắt
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const refreshButtons = document.querySelectorAll('.refresh-captcha-btn');

            refreshButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const captchaImageDiv = this.previousElementSibling; // div.captcha-image
                    const captchaImage = captchaImageDiv.querySelector('img');

                    if (captchaImage) {
                        // Tải lại hình ảnh Captcha bằng cách thay đổi src
                        captchaImage.src = '{{ route('captcha.flat') }}?' + Math.random();
                    }
                });
            });
        });
    </script>
    <script src="{{ asset('js/chatbot-widget.js') }}"></script>
    @stack('scripts')
</body>

</html>
