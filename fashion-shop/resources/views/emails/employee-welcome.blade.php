<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thong tin tai khoan nhan vien</title>
</head>

<body style="margin:0; padding:0; background-color:#f8f9fa; font-family:Arial, Helvetica, sans-serif; color:#374151;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
        style="background-color:#f8f9fa; margin:0; padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                    style="max-width:560px; background-color:#ffffff; border:1px solid #e5e7eb; border-radius:18px; overflow:hidden;">
                    <tr>
                        <td style="padding:26px 24px; border-bottom:1px solid #f3f4f6; text-align:center;">
                            <h2
                                style="margin:0; color:#111827; font-size:22px; font-weight:700; text-transform:uppercase;">
                                Thong tin tai khoan nhan vien
                            </h2>
                            <p
                                style="margin:10px 0 0; color:#9ca3af; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:1px;">
                                FashionShop
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 14px; font-size:15px; line-height:1.7;">
                                Xin chao <strong>{{ $employeeName }}</strong>,
                            </p>
                            <p style="margin:0 0 16px; font-size:15px; line-height:1.7;">
                                Ban da duoc tao tai khoan nhan vien tren he thong FashionShop voi thong tin nhu sau:
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                style="border:1px solid #e5e7eb; border-radius:10px; overflow:hidden; margin-bottom:18px;">
                                <tr>
                                    <td
                                        style="padding:10px 12px; border-bottom:1px solid #f3f4f6; width:40%; color:#6b7280; font-size:13px;">
                                        Ma nhan vien</td>
                                    <td
                                        style="padding:10px 12px; border-bottom:1px solid #f3f4f6; color:#111827; font-size:13px; font-weight:700;">
                                        {{ $employeeCode }}</td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding:10px 12px; border-bottom:1px solid #f3f4f6; color:#6b7280; font-size:13px;">
                                        Chuc vu</td>
                                    <td
                                        style="padding:10px 12px; border-bottom:1px solid #f3f4f6; color:#111827; font-size:13px; font-weight:700;">
                                        {{ $roleLabel }}</td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding:10px 12px; border-bottom:1px solid #f3f4f6; color:#6b7280; font-size:13px;">
                                        Username</td>
                                    <td
                                        style="padding:10px 12px; border-bottom:1px solid #f3f4f6; color:#111827; font-size:13px; font-weight:700;">
                                        {{ $username }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 12px; color:#6b7280; font-size:13px;">Mat khau tam thoi</td>
                                    <td style="padding:10px 12px; color:#111827; font-size:13px; font-weight:700;">
                                        {{ $password }}</td>
                                </tr>
                            </table>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                style="margin:0 0 18px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $loginUrl }}" target="_blank"
                                            style="display:inline-block; background-color:#bc9c75; color:#ffffff; text-decoration:none; font-weight:700; font-size:13px; text-transform:uppercase; letter-spacing:0.5px; padding:12px 24px; border-radius:10px;">
                                            Dang nhap he thong
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0; font-size:12px; color:#9ca3af; line-height:1.7;">
                                Vui long doi mat khau ngay sau lan dang nhap dau tien de dam bao an toan tai khoan.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
