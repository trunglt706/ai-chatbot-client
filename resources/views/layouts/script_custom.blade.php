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

            // Nếu form có thuộc tính onsubmit (inline handler), thì không xử lý spinner
            if ($form.attr('onsubmit')) {
                return;
            }

            const $submitButton = $form.find('button[type="submit"], input[type="submit"]');

            $submitButton.attr('disabled', 'true');
            $submitButton.addClass('opacity-75 cursor-not-allowed');

            // Lưu trữ nội dung gốc của nút để khôi phục sau (nếu cần)
            const originalButtonContent = $submitButton.html();

            $submitButton.html(`
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Loading...
            `);
        });

        // sự kiện cập nhật hình ảnh captcha
        $('.refresh-captcha-btn').on('click', function(e) {
            e.preventDefault();

            const $button = $(this);
            const $captchaImage = $button.prev('.captcha-image').find('img');

            // Lưu nội dung gốc của nút
            const originalButtonContent = $button.html();

            // Cập nhật trạng thái loading
            $button
                .attr('disabled', true)
                .addClass('opacity-75 cursor-not-allowed')
                .html(`<span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span role="status">Loading...</span>`);
            if ($captchaImage.length) {
                const newSrc = "{{ route('captcha.flat') }}?" + Math.random();
                $captchaImage.attr('src', newSrc);

                // Khi ảnh load xong thì khôi phục lại nút
                $captchaImage.on('load', function() {
                    $button
                        .removeAttr('disabled')
                        .removeClass('opacity-75 cursor-not-allowed')
                        .html(originalButtonContent);
                });
            }
        });


    });

    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>
