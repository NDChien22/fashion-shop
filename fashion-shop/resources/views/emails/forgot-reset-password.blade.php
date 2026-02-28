<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận Đặt lại mật khẩu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f4f4f4;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: #fff;
            width: 400px;
            padding: 60px 50px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            min-height: 700px;
        }

        .reset-password .login-container {
            min-height: 450px;

        }

        .email-template {
            width: 500px !important;
            padding: 0 !important;
            min-height: auto !important;
            overflow: hidden;
            text-align: left;
        }

        .email-header {
            background: #b5966d;
            padding: 30px 20px;
            color: white;
            text-align: center;
        }


        .email-header h2 {
            display: inline-block;
            font-size: 22px;
            margin-bottom: 5px;
        }

        .email-header p {
            font-size: 13px;
            opacity: 0.9;
        }


        .email-body {
            padding: 30px 40px;
        }

        .email-body p {
            font-size: 14px;
            color: #444;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .center-btn {
            text-align: center;
            margin: 30px 0;
        }

        .btn-email-action {
            background-color: #b5966d;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-email-action:hover {
            background: #61605f;
        }

        .warning-box {
            background-color: #fffbe6;
            border: 1px solid #ffe58f;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .warning-box p {
            margin-bottom: 0;
            font-size: 13px;
            color: #856404;
        }


        .fallback-link {
            font-size: 12px;
            color: #888;
            margin-top: 20px;
        }

        .link-text {
            background: #f4f4f4;
            padding: 10px;
            word-break: break-all;
            border-radius: 4px;
            margin-top: 5px;
        }

        .safety-note {
            text-align: center;
            font-style: italic;
            color: #999 !important;
            margin-top: 25px;
        }


        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #eee;
        }

        .email-footer p {
            font-size: 11px;
            color: #999;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-container email-template">

            <div class="email-header">
                <i class="fa-solid fa-lock" style="color: #ffcc00; margin-right: 10px;"></i>
                <h2>Đặt lại mật khẩu</h2>
                <p>Yêu cầu đặt lại mật khẩu cho tài khoản FastFashion</p>
            </div>

            <div class="email-body">
                <p>Xin chào {{ $user->username }}</p>
                <p>Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn. Nhấp vào nút bên dưới để tạo
                    mật khẩu mới.</p>

                <div class="center-btn">
                    <a href="{{ $resetUrl }}" class="btn-email-action">Đặt lại mật khẩu</a>
                </div>

                <div class="warning-box">
                    <p><strong>Lưu ý:</strong></p>
                    <p>Liên kết đặt lại mật khẩu sẽ hết hạn sau <strong>{{ $expiresMinutes }} phút</strong> kể từ khi
                        email này được gửi. Nếu bạn không yêu cầu, vui lòng bỏ qua email này.</p>
                </div>

                <div class="fallback-link">
                    <p>Nếu nút trên không hoạt động, hãy sao chép và dán liên kết sau vào trình duyệt của bạn:</p>
                    <a href="{{ $resetUrl }}" class="plain-link">{{ $resetUrl }}</a>

                </div>

                <p class="safety-note">Vì sự an toàn của bạn, không chia sẻ liên kết này cho bất kỳ ai.</p>
            </div>

            <div class="email-footer">
                <p><strong>FastFashion - Hỗ trợ đặt lại mật khẩu</strong></p>
                <p>© 2025 FashFashion. Tất cả quyền lợi được bảo vệ.</p>
                <p>Đây là email tự động. Vui lòng không trả lời email này.</p>
            </div>

        </div>
    </div>
</body>

</html>
