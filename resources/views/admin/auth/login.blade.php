<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="لوحة التحكم الإدارية لمركز مشكاة - نظام إدارة حفظ القرآن الكريم" />
    <title>تسجيل دخول المشرف — مركز مشكاة</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" media="print" onload="this.media='all'">
    <style>
        /* ============ إعدادات أساسية محسنة ============ */
        :root {
            --primary-color: #059669;
            --primary-dark: #047857;
            --primary-light: #10b981;
            --secondary-color: #065f46;
            --accent-color: #d1fae5;
            --background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #064e3b 100%);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-primary: #f8fafc;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --shadow-primary: 0 8px 32px rgba(5, 150, 105, 0.12);
            --shadow-elevated: 0 16px 48px rgba(5, 150, 105, 0.18);
            --border-radius: 12px;
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--background);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            color: var(--text-primary);
            margin: 0;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ============ خلفية بسيطة ومحسنة ============ */
        .background-decoration {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            opacity: 0.6;
        }

        .background-decoration::before {
            content: '';
            position: absolute;
            top: 20%;
            left: 10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(5, 150, 105, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulse 8s ease-in-out infinite;
        }

        .background-decoration::after {
            content: '';
            position: absolute;
            bottom: 20%;
            right: 10%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.06) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulse 8s ease-in-out infinite 4s;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 0.6;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        /* ============ كنتينر الصفحة الرئيسي ============ */
        .login-container {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            max-width: 1200px;
            width: 100%;
            gap: 3rem;
        }

        /* ============ قسم المعلومات الجانبي محسن ============ */
        .info-section {
            display: none;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 3rem 2.5rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-elevated);
            text-align: center;
            flex: 1;
            max-width: 480px;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: var(--shadow-primary);
        }

        .brand-logo i {
            font-size: 2rem;
            color: white;
        }

        .brand-title {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }

        .brand-subtitle {
            font-size: 1rem;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .features-list {
            text-align: right;
            display: grid;
            gap: 1rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .feature-item:hover {
            background: rgba(255, 255, 255, 0.06);
            transform: translateX(-2px);
        }

        .feature-item i {
            color: var(--primary-light);
            margin-left: 1rem;
            font-size: 1rem;
            width: 20px;
        }

        /* ============ بطاقة تسجيل الدخول محسنة ============ */
        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 3rem 2.5rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-elevated);
            position: relative;
            width: 100%;
            max-width: 420px;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        /* العنوان والوصف محسن */
        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .welcome-text {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .welcome-subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 400;
        }

        /* ============ النموذج ============ */
        .login-form {
            width: 100%;
        }

        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-input {
            width: 100%;
            padding: 1rem 3rem 1rem 1.25rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.03);
            color: var(--text-primary);
            font-size: 1rem;
            font-family: inherit;
            transition: var(--transition);
            outline: none;
        }

        .form-input:focus {
            border-color: var(--primary-color);
            background: rgba(255, 255, 255, 0.06);
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
        }

        .form-input::placeholder {
            color: var(--text-muted);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            color: var(--text-muted);
            font-size: 1rem;
            transition: var(--transition);
            z-index: 2;
        }

        .form-input:focus + .input-icon {
            color: var(--primary-color);
        }

        /* تذكرني */
        .form-options {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-bottom: 2rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .custom-checkbox {
            width: 18px;
            height: 18px;
            border: 1px solid var(--text-muted);
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            background: transparent;
        }

        .remember-me input[type="checkbox"] {
            display: none;
        }

        .remember-me input[type="checkbox"]:checked + .custom-checkbox {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .remember-me input[type="checkbox"]:checked + .custom-checkbox i {
            color: white;
            font-size: 0.75rem;
        }

        .remember-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
            cursor: pointer;
            user-select: none;
        }

        /* ============ زر تسجيل الدخول محسن ============ */
        .submit-button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .submit-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.25);
        }

        .submit-button:active {
            transform: translateY(0);
        }

        .submit-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* ============ تذييل محسن ============ */
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .footer-text {
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .social-link {
            width: 36px;
            height: 36px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .social-link:hover {
            background: rgba(255, 255, 255, 0.06);
            color: var(--primary-light);
            border-color: var(--primary-color);
        }

        /* ============ الاستجابة للشاشات محسنة ============ */
        @media (min-width: 1024px) {
            .info-section {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 100%;
            }

            .login-card {
                padding: 2rem 1.5rem;
                max-width: 100%;
            }

            .welcome-text {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 1rem;
            }

            .login-card {
                padding: 2rem 1.25rem;
            }

            .welcome-text {
                font-size: 1.375rem;
            }

            .form-input {
                padding: 0.875rem 2.5rem 0.875rem 1rem;
            }

            .input-icon {
                left: 0.75rem;
            }
        }

        /* ============ تحسينات الأداء والوصول ============ */
        .loading-spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* رسائل الأخطاء */
        .error-message {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 0.5rem;
            display: block;
        }

        .form-input.error {
            border-color: #ef4444;
        }

        /* تحسين إمكانية الوصول */
        .form-input:focus,
        .submit-button:focus,
        .custom-checkbox:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        /* حالة التحميل للنموذج */
        .login-form.submitting {
            opacity: 0.9;
            pointer-events: none;
        }

        .submit-button:disabled {
            opacity: 0.8;
            cursor: not-allowed;
            transform: none !important;
        }

        /* تحسين الأداء */
        .login-card {
            contain: layout style;
        }

        .submit-button {
            contain: layout;
        }

        /* تحسين الخطوط */
        .form-input,
        .form-label,
        .welcome-text {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* تقليل الحركة للمستخدمين الذين يفضلون ذلك */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    <!-- خلفية بسيطة -->
    <div class="background-decoration"></div>

    <!-- الحاوي الرئيسي -->
    <div class="login-container">
        <!-- قسم المعلومات (يظهر في الشاشات الكبيرة) -->
        <div class="info-section">
            <div class="brand-logo">
                <i class="fas fa-book-quran"></i>
            </div>
            <h1 class="brand-title">مركز مشكاة</h1>
            <p class="brand-subtitle">نظام متكامل لحفظ القرآن الكريم وتعلم علومه، مع أدوات تفاعلية تساعد على تحقيق أهدافك في رحلة الحفظ</p>

            <div class="features-list">
                <div class="feature-item">
                    <i class="fas fa-graduation-cap"></i>
                    <span>متابعة تقدم الطلاب</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-chart-line"></i>
                    <span>تقارير مفصلة</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-users"></i>
                    <span>إدارة شاملة</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-mobile-alt"></i>
                    <span>واجهة متجاوبة</span>
                </div>
            </div>
        </div>

        <!-- بطاقة تسجيل الدخول -->
        <div class="login-card">
            <div class="login-header">
                <h2 class="welcome-text">مرحباً بعودتك</h2>
                <p class="welcome-subtitle">قم بتسجيل الدخول للوصول إلى لوحة التحكم الإدارية</p>
            </div>

            <form class="login-form" id="loginForm" action="{{ route('admin.login.submit') }}" method="POST">
                @csrf
                <!-- حقل البريد الإلكتروني -->
                <div class="form-group">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" class="form-input"
                               placeholder="admin@mishkah.com" required autocomplete="email"
                               value="{{ old('email') }}">
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- حقل كلمة المرور -->
                <div class="form-group">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" class="form-input"
                               placeholder="••••••••••••" required autocomplete="current-password">
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- خيارات إضافية -->
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <div class="custom-checkbox">
                            <i class="fas fa-check"></i>
                        </div>
                        <label for="remember" class="remember-label">تذكرني لمدة 30 يوماً</label>
                    </div>
                </div>

                <!-- زر تسجيل الدخول -->
                <button type="submit" class="submit-button">
                    <span>تسجيل الدخول</span>
                    <div class="loading-spinner"></div>
                </button>
            </form>

            <!-- تذييل البطاقة -->
            <div class="login-footer">
                <p class="footer-text">© 2025 مركز مشكاة — جميع الحقوق محفوظة</p>
                <div class="social-links">
                    <a href="#" class="social-link" title="فيسبوك">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link" title="تويتر">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link" title="انستغرام">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link" title="واتساب">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript محسن للأداء
        document.addEventListener('DOMContentLoaded', function() {
            // معالجة النموذج
            const loginForm = document.getElementById('loginForm');
            const submitButton = loginForm.querySelector('.submit-button');
            const spinner = submitButton.querySelector('.loading-spinner');
            const buttonText = submitButton.querySelector('span');

            // معالجة إرسال النموذج
            loginForm.addEventListener('submit', function(e) {
                // السماح للنموذج بالإرسال الطبيعي، لكن مع تأثيرات بصرية

                // تفعيل حالة التحميل
                spinner.style.display = 'block';
                buttonText.textContent = 'جاري التحقق...';
                submitButton.disabled = true;

                // إضافة فئة تحميل للنموذج
                loginForm.classList.add('submitting');
            });

            // تحسين checkbox المخصص
            const rememberCheckbox = document.getElementById('remember');
            const customCheckbox = document.querySelector('.custom-checkbox');

            if (rememberCheckbox && customCheckbox) {
                rememberCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        customCheckbox.style.transform = 'scale(1.05)';
                        requestAnimationFrame(() => {
                            customCheckbox.style.transform = 'scale(1)';
                        });
                    }
                });
            }

            // تحسين التنقل بالكيبورد
            const formInputs = document.querySelectorAll('.form-input');
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.matches('.form-input')) {
                    e.preventDefault();
                    const inputs = Array.from(formInputs);
                    const currentIndex = inputs.indexOf(e.target);
                    if (currentIndex < inputs.length - 1) {
                        inputs[currentIndex + 1].focus();
                    } else {
                        submitButton.click(); // إرسال النموذج عند الضغط على Enter في الحقل الأخير
                    }
                }
            });
        });
    </script>
</body>
</html>
