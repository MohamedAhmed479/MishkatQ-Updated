import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Mail, Lock, ArrowLeft } from 'lucide-react';
import GuestLayout from '@/Layouts/GuestLayout';
import Button from '@/Components/UI/Button';
import Input from '@/Components/UI/Input';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/login', {
            preserveScroll: true,
        });
    };

    return (
        <GuestLayout>
            <Head title="تسجيل الدخول" />

            <div className="bg-white dark:bg-dark-400 rounded-3xl shadow-2xl p-8">
                {/* Header */}
                <div className="text-center mb-8">
                    <motion.div
                        initial={{ scale: 0 }}
                        animate={{ scale: 1 }}
                        transition={{ type: "spring", delay: 0.2 }}
                        className="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-primary-400 to-primary-600 rounded-2xl flex items-center justify-center"
                    >
                        <Lock className="w-8 h-8 text-white" />
                    </motion.div>
                    <h1 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                        مرحباً بعودتك
                    </h1>
                    <p className="text-text-muted dark:text-text-dark-muted mt-2">
                        سجل دخولك لمتابعة رحلة الحفظ
                    </p>
                </div>

                {/* Form */}
                <form onSubmit={handleSubmit} className="space-y-5">
                    <Input
                        label="البريد الإلكتروني"
                        type="email"
                        icon={Mail}
                        placeholder="example@email.com"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        error={errors.email}
                        dir="ltr"
                    />

                    <Input
                        label="كلمة المرور"
                        type="password"
                        icon={Lock}
                        placeholder="••••••••"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        error={errors.password}
                        dir="ltr"
                    />

                    <div className="flex items-center justify-between">
                        <label className="flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                checked={data.remember}
                                onChange={(e) => setData('remember', e.target.checked)}
                                className="w-4 h-4 rounded border-surface-300 dark:border-dark-200 text-primary-600 focus:ring-primary-500"
                            />
                            <span className="text-sm text-text-secondary dark:text-text-dark-secondary">
                                تذكرني
                            </span>
                        </label>
                        <Link
                            href="/forgot-password"
                            className="text-sm text-primary-600 dark:text-primary-400 hover:underline"
                        >
                            نسيت كلمة المرور؟
                        </Link>
                    </div>

                    <Button
                        type="submit"
                        loading={processing}
                        className="w-full"
                        size="lg"
                    >
                        تسجيل الدخول
                    </Button>
                </form>

                {/* Divider */}
                <div className="relative my-8">
                    <div className="absolute inset-0 flex items-center">
                        <div className="w-full border-t border-surface-300 dark:border-dark-300"></div>
                    </div>
                    <div className="relative flex justify-center text-sm">
                        <span className="px-4 bg-white dark:bg-dark-400 text-text-muted dark:text-text-dark-muted">
                            أو
                        </span>
                    </div>
                </div>

                {/* Register Link */}
                <p className="text-center text-text-secondary dark:text-text-dark-secondary">
                    ليس لديك حساب؟{' '}
                    <Link
                        href="/register"
                        className="text-primary-600 dark:text-primary-400 font-medium hover:underline"
                    >
                        سجل الآن
                    </Link>
                </p>
            </div>

            {/* Back to Home */}
            <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                transition={{ delay: 0.5 }}
                className="mt-6 text-center"
            >
                <Link
                    href="/"
                    className="inline-flex items-center gap-2 text-white/70 hover:text-white transition-colors"
                >
                    <ArrowLeft className="w-4 h-4" />
                    <span>العودة للرئيسية</span>
                </Link>
            </motion.div>
        </GuestLayout>
    );
}
