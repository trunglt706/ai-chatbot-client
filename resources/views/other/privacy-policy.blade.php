<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/favico.png') }}" type="image/x-icon">
    <title>🔒 Chính Sách Quyền Riêng Tư</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            font-family: 'Quicksand', sans-serif;
            color: #343a40;
            line-height: 1.7;
            padding-top: 40px;
            padding-bottom: 40px;
        }

        .privacy-card {
            background-color: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
            border: none;
            border-radius: 20px;
            overflow: hidden;
            padding: 40px 50px;
        }

        .privacy-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .privacy-header h1 {
            font-weight: 700;
            color: #007bff;
            /* Màu xanh dương chủ đạo */
            font-size: 2.8rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .privacy-header h1 .icon {
            font-size: 2.5rem;
            color: #007bff;
        }

        .privacy-header p {
            font-size: 1.1rem;
            color: #6c757d;
            max-width: 700px;
            margin: 0 auto;
        }

        h3 {
            color: #007bff;
            font-weight: 700;
            margin-top: 35px;
            margin-bottom: 15px;
            position: relative;
            padding-left: 40px;
            display: flex;
            align-items: center;
        }

        h3 .heading-icon {
            position: absolute;
            left: 0;
            font-size: 1.8rem;
            color: #28a745;
            /* Màu xanh lá cây cho icon tiêu đề */
        }

        ul {
            padding-left: 2.5rem;
            list-style: none;
            margin-bottom: 20px;
        }

        ul li {
            position: relative;
            margin-bottom: 12px;
            padding-left: 30px;
            font-size: 1.05rem;
            color: #495057;
        }

        ul li::before {
            content: '👉';
            /* Icon đầu dòng danh sách */
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.1rem;
        }

        p {
            font-size: 1.05rem;
            color: #495057;
            margin-bottom: 15px;
        }

        .contact-info strong {
            color: #dc3545;
            /* Màu đỏ cho email liên hệ */
            font-size: 1.1rem;
        }

        .footer-note {
            text-align: right;
            margin-top: 40px;
            font-size: 0.9rem;
            color: #999;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        /* Icons for each section */
        #section1-icon::before {
            content: ' gathering_data ';
        }

        #section2-icon::before {
            content: ' assignment ';
        }

        #section3-icon::before {
            content: ' verified_user ';
        }

        #section4-icon::before {
            content: ' share_off ';
        }

        #section5-icon::before {
            content: ' security ';
        }

        #section6-icon::before {
            content: ' mail ';
        }
    </style>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>

<body>
    <div class="container mt-5 mb-5">
        <div class="privacy-card">
            <div class="privacy-header">
                <h1><span class="icon material-symbols-outlined">security</span> Chính Sách Quyền Riêng Tư</h1>
                <p>Tại đây, chúng tôi cam kết bảo vệ dữ liệu cá nhân của bạn. Chính sách này mô tả cách chúng tôi thu
                    thập, sử dụng và bảo vệ thông tin của bạn khi bạn sử dụng dịch vụ của chúng tôi.</p>
            </div>

            <h3 id="section1-icon"><span class="heading-icon material-symbols-outlined">person_search</span> 1. Thông
                Tin Chúng Tôi Thu Thập</h3>
            <p>Khi bạn lựa chọn đăng nhập thông qua tài khoản Facebook, chúng tôi có thể thu thập các thông tin sau đây
                từ hồ sơ Facebook của bạn (chỉ khi bạn cấp quyền cho chúng tôi):</p>
            <ul>
                <li>Họ tên đầy đủ</li>
                <li>Địa chỉ email liên kết với tài khoản Facebook của bạn</li>
                <li>Ảnh đại diện cá nhân của bạn</li>
                <li>ID người dùng Facebook duy nhất của bạn</li>
            </ul>

            <h3 id="section2-icon"><span class="heading-icon material-symbols-outlined">data_usage</span> 2. Mục Đích Sử
                Dụng Thông Tin</h3>
            <p>Thông tin cá nhân mà chúng tôi thu thập được sử dụng với các mục đích rõ ràng và cần thiết nhằm nâng cao
                trải nghiệm của bạn:</p>
            <ul>
                <li>Để tạo và quản lý tài khoản người dùng, giúp bạn dễ dàng đăng ký và đăng nhập vào dịch vụ.</li>
                <li>Để cá nhân hóa trải nghiệm của bạn trên nền tảng, cung cấp nội dung và tính năng phù hợp với sở
                    thích của bạn.</li>
                <li>Để cung cấp dịch vụ hỗ trợ khách hàng hiệu quả khi bạn cần giúp đỡ hoặc có bất kỳ thắc mắc nào.</li>
            </ul>

            <h3 id="section3-icon"><span class="heading-icon material-symbols-outlined">lock</span> 3. Bảo Mật Thông Tin
                Của Bạn</h3>
            <p>Chúng tôi luôn đặt sự an toàn dữ liệu của bạn lên hàng đầu. Chúng tôi triển khai các biện pháp kỹ thuật
                và tổ chức tiên tiến, phù hợp để bảo vệ dữ liệu cá nhân của bạn khỏi những rủi ro như mất mát, truy cập
                trái phép, tiết lộ, thay đổi hoặc sử dụng sai mục đích.</p>

            <h3 id="section4-icon"><span class="heading-icon material-symbols-outlined">group_off</span> 4. Chia Sẻ
                Thông Tin</h3>
            <p>Sự riêng tư của bạn là ưu tiên hàng đầu của chúng tôi. Chúng tôi cam kết không chia sẻ, không bán, và
                không trao đổi bất kỳ thông tin cá nhân nào của bạn với bất kỳ bên thứ ba nào, trừ những trường hợp sau:
            </p>
            <ul>
                <li>Khi chúng tôi nhận được sự đồng ý rõ ràng từ bạn.</li>
                <li>Khi pháp luật hiện hành yêu cầu chúng tôi phải tiết lộ thông tin.</li>
            </ul>

            <h3 id="section5-icon"><span class="heading-icon material-symbols-outlined">rule</span> 5. Quyền Của Bạn Đối
                Với Dữ Liệu</h3>
            <p>Bạn hoàn toàn có quyền kiểm soát dữ liệu cá nhân của mình. Bạn có thể:</p>
            <ul>
                <li>Yêu cầu truy cập vào dữ liệu cá nhân mà chúng tôi đang lưu giữ về bạn.</li>
                <li>Yêu cầu chỉnh sửa hoặc cập nhật bất kỳ thông tin nào không chính xác hoặc chưa đầy đủ.</li>
                <li>Yêu cầu xóa dữ liệu cá nhân của bạn khỏi hệ thống của chúng tôi.</li>
            </ul>
            <p>Để thực hiện các quyền này, vui lòng liên hệ với chúng tôi theo thông tin bên dưới.</p>

            <h3 id="section6-icon"><span class="heading-icon material-symbols-outlined">contact_mail</span> 6. Liên Hệ
                Với Chúng Tôi</h3>
            <p>Nếu bạn có bất kỳ câu hỏi, góp ý hoặc cần làm rõ thêm về Chính sách quyền riêng tư này, xin đừng ngần
                ngại liên hệ với chúng tôi qua địa chỉ email:</p>
            <p class="contact-info text-center"><strong>{{ env('APP_EMAIL') }}</strong></p>

            <p class="mt-5 footer-note">
                <small>Cập nhật lần cuối: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</small>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
