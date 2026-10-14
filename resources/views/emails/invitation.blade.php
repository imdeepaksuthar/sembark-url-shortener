<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>Invitation to join {{ $companyName }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            background-color: #f8fafc;
        }

        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        div[style*="margin: 16px 0"] {
            margin: 0 !important;
        }

        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }

        a {
            text-decoration: none;
        }

        .btn-primary:hover {
            background-color: #4338ca !important;
        }
    </style>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; -webkit-font-smoothing: antialiased;">
    <div style="background-color: #f8fafc; padding: 48px 16px;">
        <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0"
            style="max-width: 520px; width: 100%; margin: 0 auto;">

            <!-- Logo / Brand Header -->
            <tr>
                <td style="padding: 0 0 24px 4px; text-align: left;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td style="vertical-align: middle; padding-right: 10px;">
                                <div
                                    style="width: 32px; height: 32px; background: linear-gradient(135deg, #4f46e5 0%, #2563eb 100%); background-color: #4f46e5; border-radius: 8px; text-align: center; line-height: 32px; color: #ffffff; font-weight: 800; font-size: 17px; display: inline-block;">
                                    S
                                </div>
                            </td>
                            <td style="vertical-align: middle;">
                                <span
                                    style="font-size: 16px; font-weight: 700; color: #0f172a; letter-spacing: -0.01em;">
                                    {{ config('app.name', 'Sembark URL Shortener') }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Main Card -->
            <tr>
                <td
                    style="background-color: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 36px 32px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);">

                    <h1
                        style="margin: 0 0 20px 0; font-size: 20px; font-weight: 700; color: #0f172a; line-height: 1.4; letter-spacing: -0.01em;">
                        You've been invited to join {{ $companyName }}
                    </h1>

                    <p style="margin: 0 0 16px 0; font-size: 15px; line-height: 1.6; color: #334155;">
                        Hello,
                    </p>

                    <p style="margin: 0 0 16px 0; font-size: 15px; line-height: 1.6; color: #334155;">
                        You have been invited to join the <strong>{{ $companyName }}</strong> team on
                        <strong>{{ config('app.name', 'Sembark URL Shortener') }}</strong>.
                    </p>

                    <p style="margin: 0 0 28px 0; font-size: 15px; line-height: 1.6; color: #475569;">
                        Accepting this invitation gives you access to create short URLs, track real-time click
                        analytics, and collaborate within your workspace.
                    </p>

                    <!-- CTA Button -->
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 0 0 28px 0;">
                        <tr>
                            <td style="border-radius: 8px; background-color: #4f46e5;">
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="btn-primary"
                                    style="display: inline-block; background-color: #4f46e5; color: #ffffff; font-size: 14px; font-weight: 600; text-decoration: none; padding: 12px 28px; border-radius: 8px;">
                                    Accept Invitation &rarr;
                                </a>
                            </td>
                        </tr>
                    </table>

                    <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #334155;">
                        Best regards,<br>
                        <strong>The {{ config('app.name', 'Sembark URL Shortener') }} Team</strong>
                    </p>

                    <!-- Subtle Divider -->
                    <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 28px 0 20px 0;">

                    <!-- Fallback Link -->
                    <p style="margin: 0 0 6px 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                        Button not working? Copy and paste this link into your browser:
                    </p>
                    <p style="margin: 0; font-size: 12px; line-height: 1.5; word-break: break-all;">
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                            style="color: #4f46e5; text-decoration: underline;">
                            {{ $url }}
                        </a>
                    </p>
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td
                    style="padding: 24px 8px 0 8px; text-align: left; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Sembark URL Shortener') }}. All rights reserved.
                </td>
            </tr>
        </table>
    </div>
</body>

</html>