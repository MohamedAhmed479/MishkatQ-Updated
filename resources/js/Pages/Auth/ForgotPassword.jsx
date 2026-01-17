import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Mail, ArrowLeft, KeyRound } from 'lucide-react';
import GuestLayout from '@/Layouts/GuestLayout';
import Button from '@/Components/UI/Button';
import Input from '@/Components/UI/Input';

export default function ForgotPassword({ status }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/forgot-password');
    };

    return (
        <GuestLayout>
            <Head title="نسيت كلمة المرور" />

            <div className="bg-white dark:bg-dark-400 rounded-3xl shadow-2xl p-8">
                {/* Header */}
                <div className="text-center mb-8">
                    <motion.div
                        initial={{ scale: 0 }}
                        animate={{ scale: 1 }}
                        transition={{ type: "spring", delay: 0.2 }}
                        className="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-primary-400 to-primary-600 rounded-2xl flex items-center justify-center"
                    >
                        <KeyRound className="w-8 h-8 text-white" />
                    </motion.div>
                    <h1 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                        استعادة كلمة المرور
                    </h1>
                    <p className="text-text-muted dark:text-text-dark-muted mt-2 leading-relaxed">
                        أدخل بريدك الإلكتروني وسنرسل لك رمز التحقق
                    </p>
                </div>

                {/* Success Message */}
                {status && (
                    <motion.div
                        initial={{ opacity: 0, y: -10 }}
                        animate={{ opacity: 1, y: 0 }}
                        className="mb-6 p-4 bg-success/10 border border-success/20 rounded-xl text-success text-center"
                    >
                        {status}
                    </motion.div>
                )}

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

                    <Button
                        type="submit"
                        loading={processing}
                        className="w-full"
                        size="lg"
                    >
                        إرسال رمز التحقق
                    </Button>
                </form>

                {/* Back to Login */}
                <div className="mt-8 text-center">
                    <Link
                        href="/login"
                        className="inline-flex items-center gap-2 text-primary-600 dark:text-primary-400 hover:underline"
                    >
                        <ArrowLeft className="w-4 h-4" />
                        <span>العودة لتسجيل الدخول</span>
                    </Link>
                </div>
            </div>
        </GuestLayout>
    );
}
