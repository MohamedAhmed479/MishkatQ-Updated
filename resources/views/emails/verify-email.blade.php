<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تفعيل البريد الإلكتروني - مشكاة</title>
    <style>
        body {
            background: #f5fdf7;
            font-family: 'Tajawal', Arial, sans-serif;
            color: #2e7d32;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(46, 125, 50, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #a8e6cf, #81c784);
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            color: #ffffff;
        }

        .content {
            padding: 30px;
            line-height: 1.8;
            font-size: 16px;
        }

        .content p {
            margin: 20px 0;
        }

        .verify-button {
            display: inline-block;
            background-color: #4caf50;
            color: white;
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 16px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .verify-button:hover {
            background-color: #388e3c;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background-color: #e8f5e9;
            font-size: 14px;
            color: #558b2f;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <h1>مرحبًا بك في مشكاة</h1>
    </div>

    <div class="content">
        <p>السلام عليكم ورحمة الله وبركاته،</p>
        <p>نشكرك على انضمامك إلى منصة <strong>مشكاة</strong>، التي تسعى لمساعدتك على حفظ كتاب الله تعالى بطريقة منظمة ومشوقة.</p>
        <p>لتفعيل حسابك والبدء في رحلتك المباركة، يرجى الضغط على الزر أدناه:</p>

        <p style="text-align: center;">
            <a href="{{ $url }}" class="verify-button">تفعيل الحساب</a>
        </p>

        <p>إذا لم تقم بإنشاء هذا الحساب، يمكنك تجاهل هذه الرسالة.</p>
        <p>نسأل الله أن يبارك فيك ويعينك على حفظ كتابه الكريم.</p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} مشكاة - جميع الحقوق محفوظة
    </div>
</div>
</body>
</html>
