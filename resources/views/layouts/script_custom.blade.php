<script>
    $(document).ready(function() {
        // Lắng nghe sự kiện 'input' trên các input có class 'currency'
        $('.currency').on('input', function() {
            let value = $(this).val();

            // Xóa tất cả các ký tự không phải số
            value = value.replace(/[^0-9]/g, '');

            // Chuyển đổi sang số để định dạng
            let number = parseInt(value, 10);

            if (isNaN(number)) {
                $(this).val(''); // Nếu không phải số, xóa giá trị
                return;
            }

            // Định dạng số với dấu phân cách hàng nghìn (ví dụ: 1.000.000)
            // locale 'vi-VN' sẽ dùng dấu chấm '.' làm dấu phân cách hàng nghìn
            $(this).val(number.toLocaleString('vi-VN'));
        });

        // Đảm bảo định dạng khi trang tải nếu input đã có giá trị cũ
        $('.currency').each(function() {
            if ($(this).val()) {
                let value = $(this).val().replace(/[^0-9]/g, '');
                let number = parseInt(value, 10);
                if (!isNaN(number)) {
                    $(this).val(number.toLocaleString('vi-VN'));
                }
            }
        });

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

        $('.btn-loading').on('click', function() {
            const $submitButton = $(this);

            $submitButton.attr('disabled', 'true');
            $submitButton.addClass('opacity-75 cursor-not-allowed');
            $submitButton.html(`
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Loading...
            `);
        })

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

    // Hàm Toastify tùy chỉnh
    window.showToast = function(message, type = 'info', duration = 3000, position = 'right') {
        let backgroundColor;

        switch (type) {
            case 'success':
                backgroundColor = "linear-gradient(to right, #00b09b, #96c93d)";
                break;
            case 'error':
                backgroundColor = "linear-gradient(to right, #e74c3c, #c0392b)";
                break;
            case 'warning':
                backgroundColor = "linear-gradient(to right, #f39c12, #f1c40f)";
                break;
            case 'info':
                backgroundColor = "linear-gradient(to right, #3498db, #2980b9)";
                break;
            default:
                backgroundColor = "linear-gradient(to right, #3498db, #2980b9)";
        }

        Toastify({
            text: message,
            duration: duration,
            newWindow: true,
            close: true,
            gravity: "top",
            position: position,
            stopOnFocus: true,
            style: {
                background: backgroundColor,
            },
            onClick: function() {}
        }).showToast();
    };
</script>
