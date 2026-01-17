import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { User, Mail, Lock, ArrowLeft, UserPlus } from 'lucide-react';
import GuestLayout from '@/Layouts/GuestLayout';
import Button from '@/Components/UI/Button';
import Input from '@/Components/UI/Input';

export default function Register() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/register', {
            preserveScroll: true,
        });
    };

    return (
        <GuestLayout>
            <Head title="إنشاء حساب" />

            <div className="bg-white dark:bg-dark-400 rounded-3xl shadow-2xl p-8">
                {/* Header */}
                <div className="text-center mb-8">
                    <motion.div
                        initial={{ scale: 0 }}
                        animate={{ scale: 1 }}
                        transition={{ type: "spring", delay: 0.2 }}
                        className="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-accent-400 to-accent-600 rounded-2xl flex items-center justify-center"
                    >
                        <UserPlus className="w-8 h-8 text-white" />
                    </motion.div>
                    <h1 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                        ابدأ رحلتك
                    </h1>
                    <p className="text-text-muted dark:text-text-dark-muted mt-2">
                        أنشئ حسابك وانطلق في حفظ القرآن
                    </p>
                </div>

                {/* Form */}
                <form onSubmit={handleSubmit} className="space-y-5">
                    <Input
                        label="الاسم الكامل"
                        type="text"
                        icon={User}
                        placeholder="أدخل اسمك"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        error={errors.name}
                    />

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

                    <Input
                        label="تأكيد كلمة المرور"
                        type="password"
                        icon={Lock}
                        placeholder="••••••••"
                        value={data.password_confirmation}
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                        error={errors.password_confirmation}
                        dir="ltr"
                    />

                    <Button
                        type="submit"
                        loading={processing}
                        className="w-full"
                        size="lg"
                        variant="accent"
                    >
                        إنشاء الحساب
                    </Button>
                </form>

                {/* Terms */}
                <p className="text-xs text-text-muted dark:text-text-dark-muted text-center mt-4">
                    بإنشائك حساباً، فإنك توافق على{' '}
                    <a href="#" className="text-primary-600 dark:text-primary-400 hover:underline">
                        شروط الاستخدام
                    </a>{' '}
                    و{' '}
                    <a href="#" className="text-primary-600 dark:text-primary-400 hover:underline">
                        سياسة الخصوصية
                    </a>
                </p>

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

                {/* Login Link */}
                <p className="text-center text-text-secondary dark:text-text-dark-secondary">
                    لديك حساب بالفعل؟{' '}
                    <Link
                        href="/login"
                        className="text-primary-600 dark:text-primary-400 font-medium hover:underline"
                    >
                        سجل الدخول
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
