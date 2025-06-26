<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/favico.png') }}" type="image/x-icon">
    <title>🗑️ Hướng Dẫn Xóa Tài Khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        body {
            background: linear-gradient(to right, #f0f2f5, #e0e5ec);
            /* Nền gradient nhẹ nhàng */
            font-family: 'Quicksand', sans-serif;
            color: #34495e;
            line-height: 1.7;
            padding-top: 40px;
            padding-bottom: 40px;
        }

        .delete-guide-card {
            background-color: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            /* Bóng đổ mềm mại */
            border: none;
            border-radius: 20px;
            overflow: hidden;
            padding: 40px 50px;
        }

        .guide-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
        }

        .guide-header h1 {
            font-weight: 700;
            color: #dc3545;
            /* Màu đỏ chủ đạo cho sự chú ý */
            font-size: 2.8rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .guide-header h1 .icon {
            font-size: 2.5rem;
            color: #dc3545;
        }

        .guide-header p {
            font-size: 1.1rem;
            color: #6c757d;
            max-width: 700px;
            margin: 0 auto;
        }

        .step-heading {
            color: #28a745;
            /* Màu xanh lá cho các bước */
            font-weight: 700;
            margin-top: 35px;
            margin-bottom: 15px;
            position: relative;
            padding-left: 50px;
            /* Khoảng cách cho icon và số bước */
            display: flex;
            align-items: center;
        }

        .step-number {
            font-size: 2rem;
            font-weight: 900;
            color: #007bff;
            /* Màu xanh dương cho số bước */
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            text-align: center;
        }

        .step-description {
            font-size: 1.05rem;
            color: #495057;
            margin-bottom: 20px;
        }

        .important-note {
            background-color: #fff3cd;
            /* Nền vàng nhạt cho lưu ý */
            color: #856404;
            /* Chữ màu vàng đậm */
            border-left: 5px solid #ffc107;
            /* Viền trái màu vàng */
            padding: 20px 25px;
            border-radius: 8px;
            margin-top: 30px;
            font-size: 1.1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .important-note .note-icon {
            font-size: 2rem;
            color: #ffc107;
        }

        .contact-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
        }

        .contact-section h3 {
            color: #17a2b8;
            /* Màu xanh ngọc cho liên hệ */
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .contact-section h3 .contact-icon {
            font-size: 2rem;
            color: #17a2b8;
        }

        .contact-section p {
            font-size: 1.05rem;
            color: #495057;
        }

        .contact-email strong {
            color: #007bff;
            /* Email màu xanh dương */
            font-size: 1.15rem;
        }

        .footer-note {
            text-align: right;
            margin-top: 40px;
            font-size: 0.9rem;
            color: #999;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>

<body>
    <div class="container mt-5 mb-5">
        <div class="delete-guide-card">
            <div class="guide-header">
                <h1><span class="icon material-symbols-outlined">delete_forever</span> Hướng Dẫn Xóa Tài Khoản</h1>
                <p>Chúng tôi tôn trọng quyền kiểm soát dữ liệu cá nhân của bạn. Nếu bạn không còn muốn sử dụng dịch vụ
                    và muốn xóa tài khoản của mình khỏi hệ thống, vui lòng thực hiện theo các bước hướng dẫn chi tiết
                    dưới đây.</p>
            </div>

            <h3 class="step-heading"><span class="step-number">1</span> Đăng Nhập Vào Hệ Thống</h3>
            <p class="step-description">Đầu tiên, bạn cần truy cập vào hệ thống bằng cách sử dụng tài khoản đã đăng ký
                hoặc đăng nhập thông qua tài khoản Facebook của mình.</p>
            <p class="text-center"><span class="material-symbols-outlined"
                    style="font-size: 3rem; color: #007bff;">login</span></p>

            <h3 class="step-heading"><span class="step-number">2</span> Truy Cập Trang Cá Nhân (Hồ Sơ)</h3>
            <p class="step-description">Sau khi đăng nhập thành công, hãy tìm và nhấn vào biểu tượng tài khoản của bạn
                (thường ở góc trên bên phải màn hình) hoặc menu người dùng. Trong menu thả xuống, chọn mục **"Hồ sơ"**
                hoặc **"Profile"** để vào trang quản lý thông tin cá nhân của bạn.</p>
            <p class="text-center"><span class="material-symbols-outlined"
                    style="font-size: 3rem; color: #28a745;">account_circle</span></p>

            <h3 class="step-heading"><span class="step-number">3</span> Chọn "Xóa Tài Khoản"</h3>
            <p class="step-description">Trên trang Hồ sơ cá nhân của bạn, hãy cuộn xuống hoặc tìm kiếm tùy chọn **"Xóa
                tài khoản"** (Delete Account). Đây có thể là một nút hoặc một liên kết riêng biệt. Nhấn vào tùy chọn này
                để bắt đầu quá trình xóa.</p>
            <p class="text-center"><span class="material-symbols-outlined"
                    style="font-size: 3rem; color: #dc3545;">delete</span></p>

            <h3 class="step-heading"><span class="step-number">4</span> Xác Nhận Hành Động</h3>
            <p class="step-description">Để đảm bảo rằng đây là hành động có chủ đích, hệ thống sẽ hiển thị một cửa sổ
                cảnh báo hoặc một trang xác nhận, yêu cầu bạn xác nhận lại quyết định xóa tài khoản. Vui lòng đọc kỹ
                thông báo và sau khi xác nhận, toàn bộ thông tin và dữ liệu liên quan đến tài khoản của bạn sẽ bị xóa
                vĩnh viễn khỏi hệ thống của chúng tôi.</p>
            <p class="text-center"><span class="material-symbols-outlined"
                    style="font-size: 3rem; color: #ffc107;">warning</span></p>

            <hr class="my-5">

            <div class="important-note">
                <span class="note-icon material-symbols-outlined">info</span>
                <strong>LƯU Ý QUAN TRỌNG:</strong> Hành động xóa tài khoản là **không thể khôi phục**. Tất cả dữ liệu,
                thông tin cá nhân và lịch sử sử dụng liên quan đến tài khoản của bạn sẽ bị xóa vĩnh viễn và không thể
                lấy lại được. Hãy cân nhắc kỹ trước khi thực hiện!
            </div>

            <div class="contact-section">
                <h3><span class="contact-icon material-symbols-outlined">help</span> Cần Hỗ Trợ?</h3>
                <p>Nếu bạn gặp bất kỳ khó khăn nào trong quá trình xóa tài khoản hoặc có bất kỳ câu hỏi nào khác, đừng
                    ngần ngại liên hệ với đội ngũ hỗ trợ của chúng tôi qua địa chỉ email:</p>
                <p class="contact-email"><strong>{{ env('APP_EMAIL') }}</strong></p>
            </div>

            <p class="mt-5 footer-note">
                <small>Cập nhật lần cuối: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</small>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
