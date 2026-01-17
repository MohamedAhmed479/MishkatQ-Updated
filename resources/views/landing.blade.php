<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>مشكاة - منصة الحفظ والمراجعة الذكية</title>
    <meta name="description" content="مشكاة: خطط حفظ القرآن الذكية، المراجعة بالتكرار المتباعد، تتبُّع التقدّم، التحفيز والشارات">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Cairo:wght@300;400;500;600;700;800&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    
    <style>
        :root {
            --primary-50: #ecfdf5;
            --primary-100: #d1fae5;
            --primary-200: #a7f3d0;
            --primary-300: #6ee7b7;
            --primary-400: #34d399;
            --primary-500: #10b981;
            --primary-600: #059669;
            --primary-700: #047857;
            --primary-800: #065f46;
            --primary-900: #064e3b;
            --primary-950: #022c22;
            --accent-500: #D4AF37;
            --surface-50: #FDFCF0;
            --dark-500: #121212;
        }

        body {
            font-family: 'Tajawal', 'Cairo', sans-serif;
        }

        .font-amiri {
            font-family: 'Amiri', serif;
        }

        /* Animated gradient background */
        .hero-gradient {
            background: linear-gradient(135deg, var(--primary-950) 0%, var(--primary-900) 25%, var(--primary-800) 50%, var(--primary-900) 75%, var(--primary-950) 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Floating animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-float-delayed {
            animation: float 6s ease-in-out infinite;
            animation-delay: -3s;
        }

        /* Glow effect */
        .glow-primary {
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.4), 0 0 60px rgba(16, 185, 129, 0.2);
        }

        .glow-accent {
            box-shadow: 0 0 30px rgba(212, 175, 55, 0.4), 0 0 60px rgba(212, 175, 55, 0.2);
        }

        /* Glass morphism */
        .glass {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .glass-dark {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Card hover effect */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* Gradient text */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary-400), var(--primary-300), var(--accent-500));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Particles */
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.8) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* Responsive nav */
        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-400);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Feature icon animation */
        .feature-icon {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        }

        /* Step connector */
        .step-connector {
            position: absolute;
            top: 50%;
            left: 100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-500), transparent);
        }

        /* Dark mode toggle */
        .dark body {
            background-color: var(--dark-500);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--primary-950);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-600);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-500);
        }
    </style>
</head>

<body class="antialiased text-white overflow-x-hidden">
    <!-- Particles Background -->
    <div id="particles" class="fixed inset-0 pointer-events-none z-0"></div>

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 py-4 px-4 md:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="glass rounded-2xl px-6 py-3 flex items-center justify-between">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.svg') }}" alt="مشكاة" class="w-10 h-10 animate-float">
                    <span class="text-xl font-bold text-gradient">مشكاة</span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="nav-link text-white/80 hover:text-white transition-colors">المزايا</a>
                    <a href="#how-it-works" class="nav-link text-white/80 hover:text-white transition-colors">كيف يعمل</a>
                    <a href="#testimonials" class="nav-link text-white/80 hover:text-white transition-colors">آراء المستخدمين</a>
                    <a href="#faq" class="nav-link text-white/80 hover:text-white transition-colors">الأسئلة الشائعة</a>
                </div>

                <!-- CTA Buttons -->
                <div class="flex items-center gap-3">
                    @guest
                        <a href="/login" class="hidden sm:block px-4 py-2 text-white/80 hover:text-white transition-colors">
                            دخول
                        </a>
                        <a href="/register" class="px-5 py-2.5 bg-gradient-to-r from-[#059669] to-[#10b981] rounded-xl font-medium hover:shadow-lg hover:shadow-[#059669]/30 transition-all">
                            ابدأ مجاناً
                        </a>
                    @else
                        <a href="/app/dashboard" class="px-5 py-2.5 bg-gradient-to-r from-[#059669] to-[#10b981] rounded-xl font-medium hover:shadow-lg hover:shadow-[#059669]/30 transition-all">
                            لوحة التحكم
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient min-h-screen flex items-center justify-center pt-24 pb-16 px-4 relative overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute top-20 right-10 w-72 h-72 bg-[#10b981]/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-20 left-10 w-96 h-96 bg-[#D4AF37]/10 rounded-full blur-3xl animate-float-delayed"></div>

        <div class="max-w-6xl mx-auto text-center relative z-10">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-2 glass rounded-full mb-8">
                <span class="w-2 h-2 bg-[#10b981] rounded-full animate-pulse"></span>
                <span class="text-sm text-white/80">منصة الحفظ الذكية الأولى عربياً</span>
            </div>

            <!-- Headline -->
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-black leading-tight mb-6">
                احفظ القرآن الكريم
                <br>
                <span class="text-gradient">بطريقة علمية ذكية</span>
            </h1>

            <!-- Subheadline -->
            <p class="text-lg md:text-xl text-white/70 max-w-3xl mx-auto mb-10 leading-relaxed">
                خطط حفظ مخصصة، مراجعة بالتكرار المتباعد، تتبع تقدمك، واكسب الشارات والنقاط.
                <br class="hidden md:block">
                رحلتك نحو إتمام حفظ كتاب الله تبدأ من هنا.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
                @guest
                    <a href="/register" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-[#059669] to-[#10b981] rounded-2xl font-bold text-lg glow-primary hover:scale-105 transition-all">
                        🚀 ابدأ رحلتك مجاناً
                    </a>
                @else
                    <a href="/app/dashboard" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-[#059669] to-[#10b981] rounded-2xl font-bold text-lg glow-primary hover:scale-105 transition-all">
                        📊 انتقل للوحة التحكم
                    </a>
                @endguest
                <a href="#how-it-works" class="w-full sm:w-auto px-8 py-4 glass rounded-2xl font-semibold hover:bg-white/10 transition-all">
                    شاهد كيف يعمل
                </a>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto">
                <div class="glass rounded-2xl p-6 card-hover">
                    <div class="text-3xl md:text-4xl font-black text-gradient mb-1">خطط</div>
                    <div class="text-sm text-white/60">مخصصة لك</div>
                </div>
                <div class="glass rounded-2xl p-6 card-hover">
                    <div class="text-3xl md:text-4xl font-black text-gradient mb-1">تكرار</div>
                    <div class="text-sm text-white/60">متباعد علمي</div>
                </div>
                <div class="glass rounded-2xl p-6 card-hover">
                    <div class="text-3xl md:text-4xl font-black text-gradient mb-1">شارات</div>
                    <div class="text-sm text-white/60">وتحفيز مستمر</div>
                </div>
                <div class="glass rounded-2xl p-6 card-hover">
                    <div class="text-3xl md:text-4xl font-black text-gradient mb-1">تحليلات</div>
                    <div class="text-sm text-white/60">لأدائك</div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2">
            <div class="w-6 h-10 border-2 border-white/30 rounded-full flex items-start justify-center p-2">
                <div class="w-1.5 h-3 bg-white/50 rounded-full animate-bounce"></div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 px-4 bg-gradient-to-b from-[#022c22] to-[#064e3b]">
        <div class="max-w-7xl mx-auto">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 glass rounded-full text-sm text-[#10b981] mb-4">المزايا</span>
                <h2 class="text-3xl md:text-5xl font-black mb-4">
                    كل ما تحتاجه <span class="text-gradient">لحفظ القرآن</span>
                </h2>
                <p class="text-lg text-white/60 max-w-2xl mx-auto">
                    تقنيات متقدمة مصممة خصيصاً لتسريع الحفظ وضمان الثبات
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="feature-card glass rounded-3xl p-8 card-hover">
                    <div class="feature-icon w-16 h-16 bg-gradient-to-br from-[#10b981] to-[#059669] rounded-2xl flex items-center justify-center text-3xl mb-6">
                        🎯
                    </div>
                    <h3 class="text-xl font-bold text-[#6ee7b7] mb-3">خطط حفظ مخصصة</h3>
                    <p class="text-white/70 leading-relaxed">
                        أنشئ خطة تناسب وقتك ومستواك مع توزيع تلقائي للآيات على الأيام
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card glass rounded-3xl p-8 card-hover">
                    <div class="feature-icon w-16 h-16 bg-gradient-to-br from-[#D4AF37] to-[#ca8a04] rounded-2xl flex items-center justify-center text-3xl mb-6">
                        🔄
                    </div>
                    <h3 class="text-xl font-bold text-[#6ee7b7] mb-3">التكرار المتباعد</h3>
                    <p class="text-white/70 leading-relaxed">
                        نظام مراجعة علمي بفترات متدرجة (1، 3، 7، 14، 30، 60، 90 يوم)
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card glass rounded-3xl p-8 card-hover">
                    <div class="feature-icon w-16 h-16 bg-gradient-to-br from-[#10b981] to-[#059669] rounded-2xl flex items-center justify-center text-3xl mb-6">
                        📊
                    </div>
                    <h3 class="text-xl font-bold text-[#6ee7b7] mb-3">تتبع التقدم</h3>
                    <p class="text-white/70 leading-relaxed">
                        إحصائيات مفصلة لكل سورة ومعدلات النجاح وتحليلات الأداء
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card glass rounded-3xl p-8 card-hover">
                    <div class="feature-icon w-16 h-16 bg-gradient-to-br from-[#D4AF37] to-[#ca8a04] rounded-2xl flex items-center justify-center text-3xl mb-6">
                        🏆
                    </div>
                    <h3 class="text-xl font-bold text-[#6ee7b7] mb-3">نظام التحفيز</h3>
                    <p class="text-white/70 leading-relaxed">
                        شارات ونقاط ولوحة صدارة للتنافس مع الآخرين
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card glass rounded-3xl p-8 card-hover">
                    <div class="feature-icon w-16 h-16 bg-gradient-to-br from-[#10b981] to-[#059669] rounded-2xl flex items-center justify-center text-3xl mb-6">
                        ⚙️
                    </div>
                    <h3 class="text-xl font-bold text-[#6ee7b7] mb-3">تعديل تلقائي</h3>
                    <p class="text-white/70 leading-relaxed">
                        النظام يراقب أداءك ويعدل خطتك تلقائياً حسب مستواك
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card glass rounded-3xl p-8 card-hover">
                    <div class="feature-icon w-16 h-16 bg-gradient-to-br from-[#D4AF37] to-[#ca8a04] rounded-2xl flex items-center justify-center text-3xl mb-6">
                        🔔
                    </div>
                    <h3 class="text-xl font-bold text-[#6ee7b7] mb-3">تذكيرات ذكية</h3>
                    <p class="text-white/70 leading-relaxed">
                        إشعارات مخصصة لمواعيد الحفظ والمراجعة
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-24 px-4 bg-[#064e3b]">
        <div class="max-w-6xl mx-auto">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 glass rounded-full text-sm text-[#D4AF37] mb-4">كيف يعمل</span>
                <h2 class="text-3xl md:text-5xl font-black mb-4">
                    أربع خطوات <span class="text-gradient">نحو الحفظ</span>
                </h2>
                <p class="text-lg text-white/60 max-w-2xl mx-auto">
                    رحلة بسيطة ومنظمة تبدأ معك من الصفر
                </p>
            </div>

            <!-- Steps -->
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="relative text-center">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-[#10b981] to-[#059669] rounded-full flex items-center justify-center text-3xl font-black text-white glow-primary">
                        1
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">حدد مستواك</h3>
                    <p class="text-white/60">
                        اختر بين مبتدئ أو متوسط أو متقدم مع الوقت اليومي المتاح
                    </p>
                    <div class="hidden lg:block step-connector"></div>
                </div>

                <!-- Step 2 -->
                <div class="relative text-center">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-[#D4AF37] to-[#ca8a04] rounded-full flex items-center justify-center text-3xl font-black text-white glow-accent">
                        2
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">أنشئ خطتك</h3>
                    <p class="text-white/60">
                        اختر السور والنظام يحسب ويوزع الآيات تلقائياً
                    </p>
                    <div class="hidden lg:block step-connector"></div>
                </div>

                <!-- Step 3 -->
                <div class="relative text-center">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-[#10b981] to-[#059669] rounded-full flex items-center justify-center text-3xl font-black text-white glow-primary">
                        3
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">احفظ يومياً</h3>
                    <p class="text-white/60">
                        اتبع خطتك مع تقييم أدائك بعد كل جلسة
                    </p>
                    <div class="hidden lg:block step-connector"></div>
                </div>

                <!-- Step 4 -->
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-[#D4AF37] to-[#ca8a04] rounded-full flex items-center justify-center text-3xl font-black text-white glow-accent">
                        4
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">راجع بانتظام</h3>
                    <p class="text-white/60">
                        النظام يجدول مراجعاتك بفترات متدرجة لثبات الحفظ
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-24 px-4 bg-gradient-to-b from-[#064e3b] to-[#022c22]">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 glass rounded-full text-sm text-[#10b981] mb-4">آراء المستخدمين</span>
                <h2 class="text-3xl md:text-5xl font-black mb-4">
                    ماذا يقولون <span class="text-gradient">عن مشكاة</span>
                </h2>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="glass rounded-3xl p-8 card-hover">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-[#10b981] to-[#059669] rounded-full flex items-center justify-center text-xl font-bold">أ</div>
                        <div>
                            <div class="font-bold text-white">أحمد محمد</div>
                            <div class="text-sm text-white/60">حافظ 5 أجزاء</div>
                        </div>
                    </div>
                    <p class="text-white/80 leading-relaxed">
                        "نظام المراجعة المتباعد غيّر طريقة حفظي تماماً. لم أعد أنسى ما حفظته بفضل الجدولة الذكية."
                    </p>
                    <div class="flex gap-1 mt-4">
                        @for($i = 0; $i < 5; $i++)
                            <span class="text-[#D4AF37]">★</span>
                        @endfor
                    </div>
                </div>

                <div class="glass rounded-3xl p-8 card-hover">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-[#D4AF37] to-[#ca8a04] rounded-full flex items-center justify-center text-xl font-bold">س</div>
                        <div>
                            <div class="font-bold text-white">سارة علي</div>
                            <div class="text-sm text-white/60">طالبة جامعية</div>
                        </div>
                    </div>
                    <p class="text-white/80 leading-relaxed">
                        "التطبيق يناسب جدولي المشغول. 30 دقيقة يومياً كافية لتحقيق تقدم ملموس."
                    </p>
                    <div class="flex gap-1 mt-4">
                        @for($i = 0; $i < 5; $i++)
                            <span class="text-[#D4AF37]">★</span>
                        @endfor
                    </div>
                </div>

                <div class="glass rounded-3xl p-8 card-hover">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-[#10b981] to-[#059669] rounded-full flex items-center justify-center text-xl font-bold">م</div>
                        <div>
                            <div class="font-bold text-white">محمد عبدالله</div>
                            <div class="text-sm text-white/60">معلم قرآن</div>
                        </div>
                    </div>
                    <p class="text-white/80 leading-relaxed">
                        "أنصح طلابي باستخدام مشكاة. نظام الشارات يحفزهم على الاستمرار والتنافس."
                    </p>
                    <div class="flex gap-1 mt-4">
                        @for($i = 0; $i < 5; $i++)
                            <span class="text-[#D4AF37]">★</span>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-24 px-4 bg-[#022c22]">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 glass rounded-full text-sm text-[#D4AF37] mb-4">الأسئلة الشائعة</span>
                <h2 class="text-3xl md:text-5xl font-black text-gradient">أسئلة متكررة</h2>
            </div>

            <div class="space-y-4" x-data="{ openFaq: null }">
                <!-- FAQ Items -->
                @php
                    $faqs = [
                        ['q' => 'هل التطبيق مجاني؟', 'a' => 'نعم، التطبيق مجاني بالكامل مع جميع المزايا الأساسية. نخطط لإضافة مزايا متقدمة مستقبلاً.'],
                        ['q' => 'كيف يعمل نظام التكرار المتباعد؟', 'a' => 'النظام يستخدم فترات مراجعة متدرجة: 1، 3، 7، 14، 30، 60، 90 يوم. كلما كان أداؤك أفضل، زادت الفترة بين المراجعات لضمان ثبات الحفظ.'],
                        ['q' => 'هل يمكنني تعديل الخطة يدوياً؟', 'a' => 'نعم، يمكنك إيقاف الخطة مؤقتاً أو تعديل التفضيلات. النظام سيعيد حساب الخطة تلقائياً حسب التغييرات.'],
                        ['q' => 'ما أنواع الشارات المتاحة؟', 'a' => 'لدينا شارات متنوعة: أولى الخطوات، المتعلم المتفاني، عالم القرآن، المتعلم المنتظم، المراجعة المثالية، وغيرها الكثير.'],
                        ['q' => 'كيف يتم حساب الوقت المطلوب للحفظ؟', 'a' => 'النظام يحسب الوقت حسب مستواك: المبتدئ (1.5 كلمة/دقيقة)، المتوسط (2.5 كلمة/دقيقة)، المتقدم (6 كلمات/دقيقة). 60% للحفظ و40% للمراجعة.'],
                    ];
                @endphp

                @foreach($faqs as $index => $faq)
                    <div class="glass rounded-2xl overflow-hidden">
                        <button 
                            onclick="toggleFaq({{ $index }})" 
                            class="w-full p-6 text-right flex items-center justify-between gap-4 hover:bg-white/5 transition-colors"
                        >
                            <span class="text-lg font-bold text-white">{{ $faq['q'] }}</span>
                            <span id="faq-icon-{{ $index }}" class="text-2xl text-[#10b981] transition-transform">+</span>
                        </button>
                        <div id="faq-answer-{{ $index }}" class="hidden px-6 pb-6 text-white/70 leading-relaxed">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 px-4 hero-gradient relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-[#022c22]/80 to-[#064e3b]/80"></div>
        <div class="max-w-4xl mx-auto text-center relative z-10">
            @guest
                <h2 class="text-3xl md:text-5xl font-black mb-6">
                    ابدأ رحلتك اليوم
                </h2>
                <p class="text-xl text-white/70 mb-10 max-w-2xl mx-auto">
                    انضم لآلاف المستخدمين الذين يحفظون القرآن بطريقة ذكية ومنظمة
                </p>
                <a href="/register" class="inline-block px-10 py-5 bg-gradient-to-r from-[#D4AF37] to-[#ca8a04] rounded-2xl font-bold text-xl text-white glow-accent hover:scale-105 transition-all">
                    🚀 أنشئ حسابك المجاني الآن
                </a>
            @else
                <h2 class="text-3xl md:text-5xl font-black mb-6">
                    أكمل رحلتك معنا
                </h2>
                <p class="text-xl text-white/70 mb-10 max-w-2xl mx-auto">
                    تابع تقدمك في حفظ القرآن الكريم واستمر في رحلتك
                </p>
                <a href="/app/dashboard" class="inline-block px-10 py-5 bg-gradient-to-r from-[#D4AF37] to-[#ca8a04] rounded-2xl font-bold text-xl text-white glow-accent hover:scale-105 transition-all">
                    📊 انتقل للوحة التحكم
                </a>
            @endguest
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 px-4 bg-[#022c22] border-t border-white/10">
        <div class="max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.svg') }}" alt="مشكاة" class="w-12 h-12 animate-float">
                    <div>
                        <span class="text-xl font-bold text-gradient">مشكاة</span>
                        <span class="block text-sm text-white/60">منصة الحفظ الذكية</span>
                    </div>
                </div>

                <div class="flex gap-8 text-white/60">
                    <a href="#features" class="hover:text-[#10b981] transition-colors">المزايا</a>
                    <a href="#how-it-works" class="hover:text-[#10b981] transition-colors">كيف يعمل</a>
                    <a href="#faq" class="hover:text-[#10b981] transition-colors">الأسئلة</a>
                </div>
            </div>

            <div class="text-center mt-8 pt-8 border-t border-white/10 text-white/40">
                © {{ date('Y') }} مشكاة. جميع الحقوق محفوظة.
            </div>
        </div>
    </footer>

    <script>
        // Create floating particles
        function createParticles() {
            const container = document.getElementById('particles');
            const particleCount = 50;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.width = (Math.random() * 4 + 2) + 'px';
                particle.style.height = particle.style.width;
                particle.style.animation = `float ${8 + Math.random() * 4}s ease-in-out infinite`;
                particle.style.animationDelay = Math.random() * 4 + 's';
                container.appendChild(particle);
            }
        }

        // Toggle FAQ
        function toggleFaq(index) {
            const answer = document.getElementById(`faq-answer-${index}`);
            const icon = document.getElementById(`faq-icon-${index}`);
            
            if (answer.classList.contains('hidden')) {
                answer.classList.remove('hidden');
                icon.textContent = '−';
                icon.style.transform = 'rotate(180deg)';
            } else {
                answer.classList.add('hidden');
                icon.textContent = '+';
                icon.style.transform = 'rotate(0deg)';
            }
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
        });
    </script>
</body>
</html>
