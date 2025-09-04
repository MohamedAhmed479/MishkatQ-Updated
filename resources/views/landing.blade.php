<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>مشكاة - منصة الحفظ والمراجعة الذكية</title>
    <meta name="description" content="مشكاة: خطط حفظ القرآن الذكية، المراجعة بالتكرار المتباعد، تتبُّع التقدّم، التحفيز والشارات">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22'
                        }
                    },
                    fontFamily: {
                        arabic: ['Tajawal', 'Cairo', 'system-ui', 'sans-serif']
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                        'slide-up': 'slideUp 0.8s ease-out',
                        'gradient': 'gradient 15s ease infinite',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&family=Tajawal:wght@200;300;400;500;700;800;900&display=swap');
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(3deg); }
            66% { transform: translateY(-10px) rotate(-2deg); }
        }

        @keyframes glow {
            0% { box-shadow: 0 0 5px rgba(16, 185, 129, 0.5), 0 0 10px rgba(16, 185, 129, 0.3); }
            100% { box-shadow: 0 0 20px rgba(16, 185, 129, 0.8), 0 0 30px rgba(16, 185, 129, 0.4); }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .gradient-bg {
            background: linear-gradient(-45deg, #064e3b, #047857, #065f46, #022c22);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .text-gradient {
            background: linear-gradient(135deg, #10b981, #34d399, #6ee7b7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-lift:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .card-shine {
            position: relative;
            overflow: hidden;
        }

        .card-shine::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            transition: all 0.5s;
            opacity: 0;
        }

        .card-shine:hover::before {
            opacity: 1;
            animation: shine 0.8s ease-in-out;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        .floating-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .particle {
            position: absolute;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.8) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }

        .nav-blur {
            backdrop-filter: blur(20px);
            background: rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
</head>

<body class="font-arabic gradient-bg text-white overflow-x-hidden">
    <!-- خلفية الجزيئات المتحركة -->
    <div class="floating-particles" id="particles"></div>

    <!-- شريط التنقل -->
    <nav class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 px-4">
        <div class="nav-blur rounded-full px-8 py-4 flex items-center gap-8">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.svg') }}" alt="شعار مشكاة" class="w-10 h-10 rounded-xl animate-float" />
                <span class="text-xl font-bold text-primary-300">مشكاة</span>
            </div>
            <div class="hidden md:flex gap-6">
                <a href="#features" class="text-white/80 hover:text-primary-300 transition-colors px-4 py-2 rounded-full hover:bg-white/10">المزايا</a>
                <a href="#how" class="text-white/80 hover:text-primary-300 transition-colors px-4 py-2 rounded-full hover:bg-white/10">كيف يعمل</a>
                <a href="#faq" class="text-white/80 hover:text-primary-300 transition-colors px-4 py-2 rounded-full hover:bg-white/10">الأسئلة</a>
            </div>
        </div>
    </nav>

    <!-- القسم الرئيسي -->
    <section class="min-h-screen flex items-center justify-center relative pt-24 pb-16 px-4">
        <div class="max-w-6xl mx-auto text-center relative z-10">
            <div class="animate-slide-up">
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-black mb-8 leading-tight">
                    <span class="text-gradient">منصة ذكية</span>
                    <br>
                    <span class="text-white">لحفظ القرآن</span>
                </h1>
                
                <p class="text-xl md:text-2xl text-white/80 mb-12 max-w-4xl mx-auto leading-relaxed">
                    تجربة عملية للحفظ والمراجعة مع خطط مخصصة وتكرار متباعد علمي لضمان ثبات الحفظ، بالإضافة إلى تتبُّع التقدّم والتحفيز بالشارات والنقاط والتعديل التلقائي للخطط
                </p>

                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-16">
                    <button class="bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 px-8 py-4 rounded-full text-white font-bold text-lg transition-all duration-300 transform hover:scale-105 animate-glow">
                        🚀 ابدأ رحلتك الآن
                    </button>
                    <button class="glass-effect px-8 py-4 rounded-full text-white/70 font-semibold cursor-not-allowed">
                        📱 تحميل التطبيق (قريباً)
                    </button>
                </div>
            </div>

            <!-- إحصائيات -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
                <div class="glass-effect p-6 rounded-2xl text-center hover-lift card-shine">
                    <div class="text-3xl font-black text-gradient mb-2">خطط</div>
                    <div class="text-sm text-white/60">خطط حفظ مخصّصة</div>
                </div>
                <div class="glass-effect p-6 rounded-2xl text-center hover-lift card-shine">
                    <div class="text-3xl font-black text-gradient mb-2">مراجعات</div>
                    <div class="text-sm text-white/60">جداول تكرار متباعد</div>
                </div>
                <div class="glass-effect p-6 rounded-2xl text-center hover-lift card-shine">
                    <div class="text-3xl font-black text-gradient mb-2">شارات</div>
                    <div class="text-sm text-white/60">نقاط وتحفيز</div>
                </div>
                <div class="glass-effect p-6 rounded-2xl text-center hover-lift card-shine">
                    <div class="text-3xl font-black text-gradient mb-2">تحليلات</div>
                    <div class="text-sm text-white/60">مؤشرات التقدّم</div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم المزايا -->
    <section id="features" class="py-20 px-4 relative">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-6xl font-black mb-6">
                    <span class="text-gradient">مزايا متقدمة</span> للحفظ الذكي
                </h2>
                <p class="text-xl text-white/70 max-w-3xl mx-auto">
                    تقنيات متقدمة مصممة خصيصاً لتسريع الحفظ وضمان الثبات مع تجربة تفاعلية ممتعة
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="glass-effect p-8 rounded-3xl hover-lift card-shine">
                    <div class="text-5xl mb-6">🧩</div>
                    <h3 class="text-2xl font-bold text-primary-300 mb-4">خطط حفظ مخصّصة</h3>
                    <p class="text-white/80 leading-relaxed">
                        أنشئ خطة حفظ تناسب وقتك اليومي ومستواك مع توزيع تلقائي للآيات على أيامك حسب قدرتك
                    </p>
                </div>

                <div class="glass-effect p-8 rounded-3xl hover-lift card-shine">
                    <div class="text-5xl mb-6">🔄</div>
                    <h3 class="text-2xl font-bold text-primary-300 mb-4">مراجعة علمية متباعدة</h3>
                    <p class="text-white/80 leading-relaxed">
                        نظام مراجعة متقدم بفترات متدرجة (1، 3، 7، 14، 30، 60، 90 يوم) لضمان ثبات الحفظ
                    </p>
                </div>

                <div class="glass-effect p-8 rounded-3xl hover-lift card-shine">
                    <div class="text-5xl mb-6">📊</div>
                    <h3 class="text-2xl font-bold text-primary-300 mb-4">تتبع التقدم المفصل</h3>
                    <p class="text-white/80 leading-relaxed">
                        مراقبة دقيقة لتقدمك مع إحصائيات مفصلة لكل سورة ومعدلات النجاح والأداء
                    </p>
                </div>

                <div class="glass-effect p-8 rounded-3xl hover-lift card-shine">
                    <div class="text-5xl mb-6">🎯</div>
                    <h3 class="text-2xl font-bold text-primary-300 mb-4">نظام التحفيز المتقدم</h3>
                    <p class="text-white/80 leading-relaxed">
                        شارات متنوعة ونقاط وجوائز للانجازات المختلفة مع لوحة صدارة شهرية وأسبوعية
                    </p>
                </div>

                <div class="glass-effect p-8 rounded-3xl hover-lift card-shine">
                    <div class="text-5xl mb-6">⚙️</div>
                    <h3 class="text-2xl font-bold text-primary-300 mb-4">تعديل تلقائي للخطط</h3>
                    <p class="text-white/80 leading-relaxed">
                        النظام يراقب أداءك ويعدل خطتك تلقائياً - يسرعها للأداء الممتاز أو يبطئها للتركيز على المراجعة
                    </p>
                </div>

                <div class="glass-effect p-8 rounded-3xl hover-lift card-shine">
                    <div class="text-5xl mb-6">🔔</div>
                    <h3 class="text-2xl font-bold text-primary-300 mb-4">تذكيرات تلقائية</h3>
                    <p class="text-white/80 leading-relaxed">
                        إشعارات تلقائية لمواعيد المراجعة مع تذكيرات مخصصة حسب عاداتك وأوقات نشاطك
                    </p>
                </div>

                <div class="glass-effect p-8 rounded-3xl hover-lift card-shine">
                    <div class="text-5xl mb-6">📈</div>
                    <h3 class="text-2xl font-bold text-primary-300 mb-4">تحليلات الأداء</h3>
                    <p class="text-white/80 leading-relaxed">
                        تقارير شاملة عن معدلات النجاح والوقت المستغرق ونقاط القوة والضعف في الحفظ
                    </p>
                </div>

                <div class="glass-effect p-8 rounded-3xl hover-lift card-shine">
                    <div class="text-5xl mb-6">🎮</div>
                    <h3 class="text-2xl font-bold text-primary-300 mb-4">تجربة تفاعلية</h3>
                    <p class="text-white/80 leading-relaxed">
                        واجهة سهلة الاستخدام مع تقييم فوري للأداء وتفاعل سلس مع المحتوى
                    </p>
                </div>

                <div class="glass-effect p-8 rounded-3xl hover-lift card-shine">
                    <div class="text-5xl mb-6">🔒</div>
                    <h3 class="text-2xl font-bold text-primary-300 mb-4">أمان وخصوصية</h3>
                    <p class="text-white/80 leading-relaxed">
                        حماية كاملة لبياناتك مع نظام تسجيل دخول آمن وتشفير البيانات الحساسة
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم كيف يعمل -->
    <section id="how" class="py-20 px-4 bg-black/20 backdrop-blur-sm">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-6xl font-black mb-6 text-gradient">كيف تعمل المنصة؟</h2>
                <p class="text-xl text-white/70">أربع خطوات بسيطة لبداية رحلة حفظ ناجحة</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="glass-effect p-8 rounded-3xl text-center hover-lift card-shine">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center text-2xl font-black text-white mx-auto mb-6">1</div>
                    <h3 class="text-xl font-bold text-white mb-4">إعداد التفضيلات</h3>
                    <p class="text-white/70">حدد وقتك اليومي المتاح ومستواك الحالي (مبتدئ/متوسط/متقدم) لإنشاء خطة مناسبة</p>
                </div>

                <div class="glass-effect p-8 rounded-3xl text-center hover-lift card-shine">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center text-2xl font-black text-white mx-auto mb-6">2</div>
                    <h3 class="text-xl font-bold text-white mb-4">إنشاء خطة مخصصة</h3>
                    <p class="text-white/70">اختر السور المراد حفظها والنظام يحسب تلقائياً عدد الأيام المطلوبة ويوزع الآيات</p>
                </div>

                <div class="glass-effect p-8 rounded-3xl text-center hover-lift card-shine">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center text-2xl font-black text-white mx-auto mb-6">3</div>
                    <h3 class="text-xl font-bold text-white mb-4">حفظ وتقييم يومي</h3>
                    <p class="text-white/70">اتبع خطتك اليومية مع تقييم أدائك (0-5) وتسجيل ملاحظاتك الشخصية</p>
                </div>

                <div class="glass-effect p-8 rounded-3xl text-center hover-lift card-shine">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center text-2xl font-black text-white mx-auto mb-6">4</div>
                    <h3 class="text-xl font-bold text-white mb-4">مراجعة متباعدة تلقائية</h3>
                    <p class="text-white/70">النظام يجدول مراجعاتك بفترات متدرجة (1، 3، 7، 14، 30، 60، 90 يوم) حسب أدائك</p>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم الأسئلة الشائعة -->
    <section id="faq" class="py-20 px-4">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-4xl md:text-6xl font-black text-center mb-16 text-gradient">الأسئلة الشائعة</h2>
            
            <div class="space-y-6">
                <div class="glass-effect rounded-2xl overflow-hidden hover-lift">
                    <div class="p-8 cursor-pointer" onclick="toggleFaq(this)">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-bold text-white">هل التطبيق متاح للهواتف الذكية؟</h3>
                            <span class="text-2xl text-primary-400">+</span>
                        </div>
                        <div class="hidden mt-6 text-white/80 leading-relaxed">
                            التطبيق قيد التطوير وسيتم إتاحته للتنزيل عند الإطلاق. زر التحميل الحالي معطّل إلى حين صدور النسخة الأولى.
                        </div>
                    </div>
                </div>

                <div class="glass-effect rounded-2xl overflow-hidden hover-lift">
                    <div class="p-8 cursor-pointer" onclick="toggleFaq(this)">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-bold text-white">كيف يعمل نظام التكرار المتباعد؟</h3>
                            <span class="text-2xl text-primary-400">+</span>
                        </div>
                        <div class="hidden mt-6 text-white/80 leading-relaxed">
                            النظام يستخدم فترات مراجعة متدرجة: 1 يوم، 3 أيام، 7 أيام، 14 يوم، 30 يوم، 60 يوم، 90 يوم. كلما كان أداؤك أفضل، كلما زادت الفترة بين المراجعات لضمان ثبات الحفظ في الذاكرة طويلة المدى.
                        </div>
                    </div>
                </div>

                <div class="glass-effect rounded-2xl overflow-hidden hover-lift">
                    <div class="p-8 cursor-pointer" onclick="toggleFaq(this)">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-bold text-white">كيف يعمل التعديل التلقائي للخطط؟</h3>
                            <span class="text-2xl text-primary-400">+</span>
                        </div>
                        <div class="hidden mt-6 text-white/80 leading-relaxed">
                            النظام يراقب أداءك لمدة 7 أيام ويحلل معدل النجاح والتقييمات. إذا كان أداؤك ممتازاً (90%+ نجاح) يسرع الخطة، وإذا كان ضعيفاً يبطئها للتركيز على المراجعة، وإذا فاتتك 3 جلسات متتالية يوقف الخطة مؤقتاً.
                        </div>
                    </div>
                </div>

                <div class="glass-effect rounded-2xl overflow-hidden hover-lift">
                    <div class="p-8 cursor-pointer" onclick="toggleFaq(this)">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-bold text-white">ما أنواع الشارات والنقاط المتاحة؟</h3>
                            <span class="text-2xl text-primary-400">+</span>
                        </div>
                        <div class="hidden mt-6 text-white/80 leading-relaxed">
                            لدينا شارات متنوعة: أولى الخطوات (10 آيات)، المتعلم المتفاني (50 آية)، عالم القرآن (100 آية)، المتعلم المنتظم (7 أيام متتالية)، المراجعة المثالية (5 مراجعات ممتازة)، جامع النقاط (1000 نقطة). كل شارة تعطيك نقاط إضافية.
                        </div>
                    </div>
                </div>

                <div class="glass-effect rounded-2xl overflow-hidden hover-lift">
                    <div class="p-8 cursor-pointer" onclick="toggleFaq(this)">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-bold text-white">كيف يتم حساب الوقت المطلوب للحفظ؟</h3>
                            <span class="text-2xl text-primary-400">+</span>
                        </div>
                        <div class="hidden mt-6 text-white/80 leading-relaxed">
                            النظام يحسب الوقت حسب مستواك: المبتدئ (1.5 كلمة/دقيقة)، المتوسط (2.5 كلمة/دقيقة)، المتقدم (6 كلمات/دقيقة). 60% من وقتك للحفظ الجديد و40% للمراجعة. النظام يحسب تلقائياً عدد الأيام المطلوبة لكل خطة.
                        </div>
                    </div>
                </div>

                <div class="glass-effect rounded-2xl overflow-hidden hover-lift">
                    <div class="p-8 cursor-pointer" onclick="toggleFaq(this)">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-bold text-white">هل يمكنني تعديل الخطة يدوياً؟</h3>
                            <span class="text-2xl text-primary-400">+</span>
                        </div>
                        <div class="hidden mt-6 text-white/80 leading-relaxed">
                            نعم، يمكنك إيقاف الخطة مؤقتاً أو تفعيلها، وتعديل التفضيلات (الوقت اليومي والمستوى) مما يؤدي إلى إعادة حساب الخطة تلقائياً. كما يمكنك تأجيل المراجعات إذا احتجت وقتاً إضافياً.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- التذييل -->
    <footer class="py-12 px-4 border-t border-white/10 bg-black/20 backdrop-blur-sm">
        <div class="max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.svg') }}" alt="شعار مشكاة" class="w-12 h-12 rounded-xl animate-float" />
                    <span class="text-2xl font-bold text-primary-300">مشكاة 2025</span>
                </div>
                
                <div class="flex gap-8 text-white/60">
                    <a href="#features" class="hover:text-primary-300 transition-colors">المزايا</a>
                    <a href="#how" class="hover:text-primary-300 transition-colors">كيف يعمل</a>
                    <a href="#faq" class="hover:text-primary-300 transition-colors">الأسئلة</a>
                </div>
            </div>
            
            <div class="text-center mt-8 pt-8 border-t border-white/10 text-white/50">
                © 2025 مشكاة. جميع الحقوق محفوظة. | منصة الحفظ الذكية الأولى عربياً
            </div>
        </div>
    </footer>

    <script>
        // إنشاء الجزيئات المتحركة
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 50;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                const size = Math.random() * 4 + 2;
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 8 + 's';
                particle.style.animationDuration = (Math.random() * 8 + 8) + 's';
                
                particlesContainer.appendChild(particle);
            }
        }

        // تبديل الأسئلة الشائعة
        function toggleFaq(element) {
            const answer = element.querySelector('div:last-child');
            const icon = element.querySelector('span');
            
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

        // تمرير سلس للروابط
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

        // تحريك شريط التنقل عند التمرير
        let lastScrollY = window.scrollY;
        const nav = document.querySelector('nav');

        window.addEventListener('scroll', () => {
            const currentScrollY = window.scrollY;
            
            if (currentScrollY > lastScrollY && currentScrollY > 100) {
                nav.style.transform = 'translateX(-50%) translateY(-100%)';
            } else {
                nav.style.transform = 'translateX(-50%) translateY(0)';
            }
            
            lastScrollY = currentScrollY;
        });

        // تهيئة الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
        });
    </script>
</body>
</html>