<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Xác minh tài khoản</title>
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
                            Email Verification
                        </div>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:26px 24px;">
                        <h1 style="margin:0 0 10px;font-size:20px;line-height:28px;">
                            Xin chào {{ $user->username ?? 'bạn' }} 👋
                        </h1>

                        <p style="margin:0 0 14px;font-size:14px;line-height:22px;color:#334155;">
                            Cảm ơn bạn đã đăng ký <b>{{ $appName }}</b>.
                            Vui lòng nhấn nút bên dưới để <b>kích hoạt tài khoản</b>.
                        </p>

                        <!-- Button -->
                        <table role="presentation" cellpadding="0" cellspacing="0" style="margin:18px 0 10px;">
                            <tr>
                                <td align="center" bgcolor="#111827" style="border-radius:10px;">
                                    <a href="{{ $url }}"
                                       style="display:inline-block;padding:12px 18px;color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;">
                                        Kích hoạt tài khoản
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:0 0 14px;font-size:12px;line-height:18px;color:#64748b;">
                            Liên kết sẽ hết hạn sau {{ $expire }} phút.
                        </p>

                        <div style="margin-top:18px;padding:12px 14px;background:#f1f5f9;border-radius:10px;">
                            <div style="font-size:12px;color:#475569;margin-bottom:6px;">
                                Nếu nút không bấm được, copy link sau và dán vào trình duyệt:
                            </div>
                            <div style="font-size:12px;word-break:break-all;color:#0f172a;">
                                {{ $url }}
                            </div>
                        </div>

                        <p style="margin:18px 0 0;font-size:12px;line-height:18px;color:#64748b;">
                            Nếu bạn không đăng ký tài khoản này, bạn có thể bỏ qua email.
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
