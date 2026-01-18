import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Lock, ArrowLeft, KeyRound } from 'lucide-react';
import GuestLayout from '@/Layouts/GuestLayout';
import Button from '@/Components/UI/Button';
import Input from '@/Components/UI/Input';
import { usePage } from '@inertiajs/react';

export default function ResetPassword({ email, token }) {
    const { flash } = usePage().props;
    
    const { data, setData, post, processing, errors } = useForm({
        email: email || '',
        token: token || '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/reset-password');
    };

    return (
        <GuestLayout>
            <Head title="إعادة تعيين كلمة المرور" />

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
                        إعادة تعيين كلمة المرور
                    </h1>
                    <p className="text-text-muted dark:text-text-dark-muted mt-2 leading-relaxed">
                        أدخل كلمة المرور الجديدة
                    </p>
                </div>

                {/* Success Message */}
                {flash?.success && (
                    <motion.div
                        initial={{ opacity: 0, y: -10 }}
                        animate={{ opacity: 1, y: 0 }}
                        className="mb-6 p-4 bg-success/10 border border-success/20 rounded-xl text-success text-center"
                    >
                        {flash.success}
                    </motion.div>
                )}

                {/* Form */}
                <form onSubmit={handleSubmit} className="space-y-5">
                    <input type="hidden" name="email" value={data.email} />
                    <input type="hidden" name="token" value={data.token} />

                    <Input
                        label="كلمة المرور الجديدة"
                        type="password"
                        icon={Lock}
                        placeholder="أدخل كلمة المرور الجديدة"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        error={errors.password}
                        required
                    />

                    <Input
                        label="تأكيد كلمة المرور"
                        type="password"
                        icon={Lock}
                        placeholder="أعد إدخال كلمة المرور"
                        value={data.password_confirmation}
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                        error={errors.password_confirmation}
                        required
                    />

                    <Button
                        type="submit"
                        loading={processing}
                        className="w-full"
                        size="lg"
                    >
                        إعادة تعيين كلمة المرور
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
