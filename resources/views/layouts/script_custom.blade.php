<script>
    $(document).ready(function() {
        // sự kiện hiển thị nội dung trong thể password
        $('.toggle-password').on('click', function() {
            var targetId = $(this).data('target');
            var passwordInput = $('#' + targetId);

            // Kiểm tra kiểu hiện tại của input
            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                $(this).find('i').removeClass('bi-eye').addClass(
                    'bi-eye-slash');
            } else {
                passwordInput.attr('type', 'password');
                $(this).find('i').removeClass('bi-eye-slash').addClass(
                    'bi-eye');
            }
        });

        $('form').on('submit', function() {
            const $form = $(this);
            // Tìm nút submit bên trong form đang được submit
            const $submitButton = $form.find('button[type="submit"], input[type="submit"]');

            $submitButton.attr('disabled', 'true');
            $submitButton.addClass('opacity-75 cursor-not-allowed');

            // Lưu trữ nội dung gốc của nút để khôi phục sau (tùy chọn)
            const originalButtonContent = $submitButton.html();

            $submitButton.html(`
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                `);
        });

        // sự kiện cập nhật hình ảnh captcha
        document.addEventListener('DOMContentLoaded', function() {
            const refreshButtons = document.querySelectorAll('.refresh-captcha-btn');

            refreshButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const captchaImageDiv = this
                        .previousElementSibling; // div.captcha-image
                    const captchaImage = captchaImageDiv.querySelector('img');

                    if (captchaImage) {
                        // Tải lại hình ảnh Captcha bằng cách thay đổi src
                        captchaImage.src = '{{ route('captcha.flat') }}?' + Math
                            .random();
                    }
                });
            });
        });
    });
</script>
