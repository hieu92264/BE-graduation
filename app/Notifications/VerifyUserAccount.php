<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Có yêu cầu liên hệ mới</title>
</head>
<body style="margin:0;padding:0;background:#f6f7fb;font-family:Arial,Helvetica,sans-serif;color:#111;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f7fb;padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                   style="max-width:600px;background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 6px 24px rgba(16,24,40,.08);">

                <!-- Header -->
                <tr>
                    <td style="padding:22px 24px;background:linear-gradient(135deg,#0ea5e9,#10b981);">
                        <div style="font-size:16px;font-weight:700;color:#fff;">
                            {{ $appName }}
                        </div>
                        <div
                            style="margin-top:6px;font-size:12px;color:rgba(255,255,255,.9);letter-spacing:.08em;text-transform:uppercase;">
                            New Contact Request
                        </div>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:26px 24px;">
                        <h1 style="margin:0 0 10px;font-size:20px;line-height:28px;">
                            Có yêu cầu liên hệ mới 📩
                        </h1>

                        <p style="margin:0 0 14px;font-size:14px;line-height:22px;color:#334155;">
                            Bạn nhận được một yêu cầu liên hệ từ khách xem phòng. Thông tin chi tiết bên dưới:
                        </p>

                        <!-- Contact panel -->
                        <div style="margin-top:14px;padding:14px 14px;background:#f1f5f9;border-radius:12px;">
                            <div style="font-size:12px;color:#475569;margin-bottom:8px;font-weight:700;">
                                Thông tin người liên hệ
                            </div>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                   style="font-size:14px;color:#0f172a;">
                                <tr>
                                    <td style="padding:6px 0;color:#64748b;width:120px;">Họ tên</td>
                                    <td style="padding:6px 0;font-weight:700;">{{ $contact->name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;color:#64748b;">SĐT</td>
                                    <td style="padding:6px 0;">
                                        <a href="tel:{{ preg_replace('/\s+/', '', (string) $contact->phone) }}"
                                           style="color:#0f172a;text-decoration:none;font-weight:700;">
                                            {{ $contact->phone }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;color:#64748b;">Email</td>
                                    <td style="padding:6px 0;">
                                        @if(!empty($contact->email))
                                        <a href="mailto:{{ $contact->email }}"
                                           style="color:#0ea5e9;text-decoration:none;font-weight:700;">
                                            {{ $contact->email }}
                                        </a>
                                        @else
                                        —
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        @if($room)
                        <!-- Room info -->
                        <div style="margin-top:16px;">
                            <div style="font-size:12px;color:#475569;margin-bottom:8px;font-weight:700;">
                                Thông tin phòng
                            </div>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                   style="border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
                                <tr>
                                    <td style="padding:10px 12px;background:#f8fafc;color:#64748b;font-size:12px;width:140px;">
                                        Mã phòng
                                    </td>
                                    <td style="padding:10px 12px;background:#ffffff;color:#0f172a;font-size:13px;font-weight:700;">
                                        #{{ $room->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 12px;background:#f8fafc;color:#64748b;font-size:12px;">
                                        Tiêu đề
                                    </td>
                                    <td style="padding:10px 12px;background:#ffffff;color:#0f172a;font-size:13px;">
                                        {{ $room->title }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 12px;background:#f8fafc;color:#64748b;font-size:12px;">
                                        Địa chỉ
                                    </td>
                                    <td style="padding:10px 12px;background:#ffffff;color:#0f172a;font-size:13px;">
                                        {{ $room->address ?? '—' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 12px;background:#f8fafc;color:#64748b;font-size:12px;">
                                        Giá
                                    </td>
                                    <td style="padding:10px 12px;background:#ffffff;color:#0f172a;font-size:13px;font-weight:700;">
                                        {{ number_format((float) $room->price, 0, ',', '.') }} ₫
                                    </td>
                                </tr>
                            </table>

                            @if(!empty($roomUrl))
                            <!-- Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:16px 0 6px;">
                                <tr>
                                    <td align="center" bgcolor="#111827" style="border-radius:10px;">
                                        <a href="{{ $roomUrl }}"
                                           style="display:inline-block;padding:12px 18px;color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;">
                                            Xem chi tiết phòng
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <div style="font-size:12px;color:#64748b;margin-top:8px;">
                                Nếu nút không bấm được, copy link sau:
                            </div>
                            <div style="font-size:12px;word-break:break-all;color:#0f172a;margin-top:4px;">
                                {{ $roomUrl }}
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Message -->
                        <div style="margin-top:18px;">
                            <div style="font-size:12px;color:#475569;margin-bottom:8px;font-weight:700;">
                                Nội dung
                            </div>

                            <div
                                style="padding:14px 14px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;">
                                <div style="font-size:14px;line-height:22px;color:#0f172a;white-space:pre-line;">
                                    {{ $contact->message }}
                                </div>
                            </div>
                        </div>

                        <p style="margin:18px 0 0;font-size:12px;line-height:18px;color:#64748b;">
                            Email này được gửi tự động từ hệ thống. Vui lòng liên hệ khách qua SĐT/Email bên trên.
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="padding:16px 24px;background:#f8fafc;border-top:1px solid #e2e8f0;">
                        <div style="font-size:12px;color:#64748b;">
                            © {{ date('Y') }} {{ $appName }}. All rights reserved.
                        </div>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
