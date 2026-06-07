<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $eventLabel }}</title>
</head>

<body style="margin:0;padding:24px;background:#f7f7f7;font-family:Arial,sans-serif;color:#222;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
        style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #eee;border-radius:12px;overflow:hidden;">
        <tr>
            <td style="padding:20px 24px;background:#bc9c75;color:#fff;">
                <h1 style="margin:0;font-size:20px;">{{ $eventLabel }}</h1>
            </td>
        </tr>
        <tr>
            <td style="padding:24px;">
                <p style="margin:0 0 12px;">Xin chào
                    {{ $order->user?->full_name ?? ($order->guest_name ?? 'Quý khách') }},</p>
                <p style="margin:0 0 16px;">Đơn hàng <strong>{{ $order->order_code }}</strong> vừa được cập nhật trạng
                    thái: <strong>{{ $eventLabel }}</strong>.</p>

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                    style="border-collapse:collapse;border:1px solid #eee;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="padding:10px 12px;border-bottom:1px solid #eee;background:#fafafa;width:40%;">Mã đơn
                            hàng</td>
                        <td style="padding:10px 12px;border-bottom:1px solid #eee;">{{ $order->order_code }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 12px;border-bottom:1px solid #eee;background:#fafafa;">Trạng thái đơn
                        </td>
                        <td style="padding:10px 12px;border-bottom:1px solid #eee;">{{ $order->status }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 12px;border-bottom:1px solid #eee;background:#fafafa;">Trạng thái vận
                            chuyển</td>
                        <td style="padding:10px 12px;border-bottom:1px solid #eee;">{{ $order->shipping_status }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 12px;background:#fafafa;">Tổng thanh toán</td>
                        <td style="padding:10px 12px;">{{ number_format((float) $order->final_amount, 0, ',', '.') }}đ
                        </td>
                    </tr>
                </table>

                @if (!empty($trackingUrl))
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:16px;">
                        <tr>
                            <td align="center">
                                <a href="{{ $trackingUrl }}" target="_blank"
                                    style="display:inline-block;background:#bc9c75;color:#fff;text-decoration:none;font-size:13px;font-weight:700;padding:12px 20px;border-radius:10px;">
                                    Theo dõi đơn hàng
                                </a>
                            </td>
                        </tr>
                    </table>
                @endif

                <p style="margin:16px 0 0;color:#666;font-size:13px;">Nếu bạn cần hỗ trợ, vui lòng liên hệ bộ phận CSKH
                    của FashionShop.</p>
            </td>
        </tr>
    </table>
</body>

</html>
