<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận Đặt lại mật khẩu - FAST FASHION</title>
</head>

<body style="margin:0; padding:0; background-color:#f8f9fa; font-family:Arial, Helvetica, sans-serif; color:#4b5563;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
        style="background-color:#f8f9fa; margin:0; padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                    style="max-width:550px; background-color:#ffffff; border:1px solid #e5e7eb; border-radius:20px; overflow:hidden;">
                    <tr>
                        <td style="padding:28px 24px; text-align:center; border-bottom:1px solid #f1f5f9;">
                            <p style="margin:0; font-size:28px;">🔐</p>
                            <h2
                                style="margin:10px 0 6px; color:#111827; font-size:24px; line-height:1.2; font-weight:700; letter-spacing:0.4px; text-transform:uppercase;">
                                Đặt lại mật khẩu</h2>
                            <p
                                style="margin:0; color:#9ca3af; font-size:12px; font-weight:600; letter-spacing:1px; text-transform:uppercase;">
                                Yêu cầu bảo mật tài khoản</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:28px 24px;">
                            <p style="margin:0 0 14px; color:#111827; font-size:18px; font-weight:700;">Xin chào khách
                                hàng, {{ $user->name }}</p>
                            <p style="margin:0 0 18px; font-size:15px; line-height:1.7;">
                                Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn tại
                                <span style="font-weight:700; color:#111827;">FAST FASHION</span>.
                                Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email này.
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                style="margin:0 0 22px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetUrl }}" target="_blank"
                                            style="display:inline-block; background-color:#bc9c75; color:#ffffff; text-decoration:none; font-weight:700; font-size:13px; letter-spacing:0.6px; text-transform:uppercase; padding:14px 26px; border-radius:12px;">
                                            Xác nhận đặt lại mật khẩu
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                style="background-color:#fffbeb; border:1px solid #fde68a; border-left:4px solid #f59e0b; border-radius:10px; margin-bottom:16px;">
                                <tr>
                                    <td style="padding:14px 16px; font-size:14px; line-height:1.6; color:#92400e;">
                                        <p style="margin:0 0 6px; font-weight:700;">Lưu ý quan trọng:</p>
                                        <p style="margin:0;">Liên kết đặt lại mật khẩu này sẽ hết hạn sau <span
                                                style="font-weight:700;">{{ $expiresMinutes }} phút</span> kể từ khi
                                            được gửi đi.</p>
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="margin:0; text-align:center; color:#9ca3af; font-size:12px; font-style:italic; line-height:1.6;">
                                Vì sự an toàn của bạn, tuyệt đối không chia sẻ liên kết này cho bất kỳ ai khác.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td
                            style="padding:20px 24px; text-align:center; background-color:#f9fafb; border-top:1px solid #f1f5f9;">
                            <p style="margin:0; color:#111827; font-size:14px; font-weight:700;">FAST FASHION - Hỗ trợ
                                khách hàng</p>
                            <p
                                style="margin:6px 0 0; color:#9ca3af; font-size:11px; letter-spacing:0.5px; text-transform:uppercase;">
                                © 2026 FAST FASHION. Tất cả quyền lợi được bảo vệ.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
