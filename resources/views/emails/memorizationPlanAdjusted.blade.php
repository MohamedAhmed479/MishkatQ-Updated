<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحديث خطة الحفظ - مشكاة</title>
    <style>
        body {
            background: #f5fdf7;
            font-family: Arial, Helvetica, sans-serif;
            color: #2e7d32;
            margin: 0;
            padding: 0;
            direction: rtl;
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e0e0e0;
        }

        .header {
            background: #81c784;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #ffffff;
        }

        .content {
            padding: 20px;
            font-size: 16px;
            line-height: 1.6;
            text-align: right;
        }

        .content p {
            margin: 15px 0;
        }

        .footer {
            text-align: center;
            padding: 15px;
            background: #e8f5e9;
            font-size: 12px;
            color: #558b2f;
        }

        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100%;
            }
            .header h1 {
                font-size: 20px;
            }
            .content {
                padding: 15px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
<table class="email-container" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="header">
                        <h1>تحديث في خطة الحفظ</h1>
                    </td>
                </tr>
                <tr>
                    <td class="content">
                        <p>السلام عليكم ورحمة الله وبركاته، {{ $user->name }}،</p>
                        <p>{{ $messageText }}</p>
                        <p>نسأل الله أن يوفقك في رحلتك مع كتابه الكريم.</p>
                    </td>
                </tr>
                <tr>
                    <td class="footer">
                        &copy; {{ date('Y') }} مشكاة - جميع الحقوق محفوظة
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
