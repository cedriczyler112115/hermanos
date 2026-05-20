<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailSubject }}</title>
    <style>
        /* Base styles for email clients */
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            background-color: #f4f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #1a202c;
        }
        table {
            border-spacing: 0;
            border-collapse: collapse;
            width: 100%;
        }
        img {
            border: 0;
            -ms-interpolation-mode: bicubic;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .header {
            padding: 30px;
            background-color: #003399; /* Professional deep blue */
            color: #ffffff;
            text-align: left;
        }
        .logo {
            height: 60px;
            width: 60px;
            border-radius: 30px; /* Force circular */
            vertical-align: middle;
            margin-right: 15px;
            border: 2px solid #ffffff;
            object-fit: cover;
        }
        .header-title {
            display: inline-block;
            vertical-align: middle;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #ffffff;
        }
        .content {
            padding: 40px 30px;
            text-align: left; /* Explicit left alignment */
        }
        h1 {
            font-size: 24px; /* 18pt+ equivalent */
            color: #111827;
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: 700;
            text-align: left;
        }
        .body-text {
            font-size: 18px; /* 14pt equivalent */
            color: #374151;
            margin-bottom: 25px;
            line-height: 1.8;
            text-align: left;
        }
        .message-box {
            background-color: #f9fafb;
            border-left: 4px solid #003399;
            padding: 20px;
            margin: 25px 0;
            color: #4b5563;
            text-align: left; /* Explicit left alignment */
            word-wrap: break-word;
        }
        .footer {
            padding: 30px;
            background-color: #f9fafb;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
        }
        .unsubscribe-link {
            color: #003399;
            text-decoration: underline;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #003399;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
        }
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                border-radius: 0 !important;
            }
            .content {
                padding: 30px 20px !important;
            }
        }
    </style>
</head>
<body>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <div class="container">
                    <!-- Header with Logo -->
                    <div class="header">
                        @php
                            // For email clients, absolute URLs are required.
                            // We check if the logo exists in the public storage.
                            $logoPath = storage_path('app/public/logo/logo.jpg');
                            $logoUrl = file_exists($logoPath) ? url('/storage/logo/logo.jpg') : url('/favicon.ico');
                        @endphp
                        <img src="{{ $logoUrl }}" alt="Cantores Hermanos Logo" class="logo" width="60" height="60">
                        <div class="header-title">Cantores Hermanos Del Sr Sto. Niño Choir</div>
                    </div>

                    <!-- Main Content -->
                    <div class="content">
                        
                        <div class="body-text">
                            Hello, You have received a new  inquiry from <strong>{{ $senderName }}</strong> via the Cantores Hermanos Del Senyor Sto, Nino official portal. 
                        </div>

                        <div class="message-box">
                            {!! $messageContent !!}
                        </div>

                        <div class="body-text">
                            This message was sent to you as part of our mission to foster community engagement and musical ministry. We encourage you to review the contents and respond as appropriate.
                        </div>

                        <div style="margin-top: 40px; border-top: 1px solid #eee; padding-top: 20px;">
                            <p class="body-text" style="font-size: 16px; margin-bottom: 0;">
                                Sincerely,<br>
                                <strong>The Cantores Hermanos Administration</strong>
                            </p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="footer">
                        <p>&copy; {{ date('Y') }} Cantores Hermanos Del Sr. Sto. Niño Choir. All rights reserved.</p>
                        <p>Libertad, Butuan City, Agusan Del Norte, Philippines</p>
                        <p style="margin-top: 15px;">
                            You are receiving this because you are a registered member of our choir community.
                            <br>
                            <a href="{{ url('/unsubscribe') }}" class="unsubscribe-link">Unsubscribe from these communications</a>
                        </p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
