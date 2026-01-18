import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ArrowLeft, ShieldCheck } from 'lucide-react';
import { useRef, useEffect } from 'react';
import GuestLayout from '@/Layouts/GuestLayout';
import Button from '@/Components/UI/Button';

export default function VerifyOtp({ email, status }) {
    const { data, setData, post, processing, errors } = useForm({
        email: email || '',
        otp: ['', '', '', '', '', ''],
    });

    const inputs = useRef([]);

    const handleChange = (index, value) => {
        if (!/^\d*$/.test(value)) return;

        const newOtp = [...data.otp];
        newOtp[index] = value;
        setData('otp', newOtp);

        // Auto focus next input
        if (value && index < 5) {
            inputs.current[index + 1]?.focus();
        }
    };

    const handleKeyDown = (index, e) => {
        if (e.key === 'Backspace' && !data.otp[index] && index > 0) {
            inputs.current[index - 1]?.focus();
        }
    };

    const handlePaste = (e) => {
        e.preventDefault();
        const pastedData = e.clipboardData.getData('text').slice(0, 6);
        if (!/^\d+$/.test(pastedData)) return;

        const newOtp = pastedData.split('').concat(Array(6 - pastedData.length).fill(''));
        setData('otp', newOtp);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/verify-otp', {
            email: data.email,
            otp: data.otp.join(''),
        });
    };

    return (
        <GuestLayout>
            <Head title="التحقق من الرمز" />

            <div className="bg-white dark:bg-dark-400 rounded-3xl shadow-2xl p-8">
                {/* Header */}
                <div className="text-center mb-8">
                    <motion.div
                        initial={{ scale: 0 }}
                        animate={{ scale: 1 }}
                        transition={{ type: "spring", delay: 0.2 }}
                        className="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-accent-400 to-accent-600 rounded-2xl flex items-center justify-center"
                    >
                        <ShieldCheck className="w-8 h-8 text-white" />
                    </motion.div>
                    <h1 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                        أدخل رمز التحقق
                    </h1>
                    <p className="text-text-muted dark:text-text-dark-muted mt-2 leading-relaxed">
                        أرسلنا رمزاً مكوناً من 6 أرقام إلى
                        <br />
                        <span className="text-primary-600 dark:text-primary-400 font-medium" dir="ltr">
                            {email}
                        </span>
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

                {/* OTP Input */}
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="flex justify-center gap-2" dir="ltr">
                        {data.otp.map((digit, index) => (
                            <motion.input
                                key={index}
                                ref={(el) => inputs.current[index] = el}
                                type="text"
                                inputMode="numeric"
                                maxLength={1}
                                value={digit}
                                onChange={(e) => handleChange(index, e.target.value)}
                                onKeyDown={(e) => handleKeyDown(index, e)}
                                onPaste={handlePaste}
                                initial={{ opacity: 0, y: 20 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ delay: index * 0.05 }}
                                className={`
                                    w-12 h-14 text-center text-2xl font-bold rounded-xl
                                    bg-surface-100 dark:bg-dark-300
                                    border-2 border-surface-300 dark:border-dark-200
                                    focus:border-primary-500 dark:focus:border-primary-400
                                    focus:outline-none transition-colors
                                    text-text-primary dark:text-text-dark-primary
                                    ${errors.otp ? 'border-error' : ''}
                                `}
                            />
                        ))}
                    </div>

                    {errors.otp && (
                        <p className="text-center text-error text-sm">{errors.otp}</p>
                    )}

                    <Button
                        type="submit"
                        loading={processing}
                        className="w-full"
                        size="lg"
                        disabled={data.otp.some(d => !d)}
                    >
                        تأكيد الرمز
                    </Button>
                </form>

                {/* Resend */}
                <div className="mt-6 text-center">
                    <p className="text-text-muted dark:text-text-dark-muted text-sm">
                        لم تستلم الرمز؟{' '}
                        <button className="text-primary-600 dark:text-primary-400 font-medium hover:underline">
                            إعادة الإرسال
                        </button>
                    </p>
                </div>

                {/* Back */}
                <div className="mt-6 text-center">
                    <Link
                        href="/forgot-password"
                        className="inline-flex items-center gap-2 text-text-muted dark:text-text-dark-muted hover:text-text-primary dark:hover:text-text-dark-primary transition-colors"
                    >
                        <ArrowLeft className="w-4 h-4" />
                        <span>تغيير البريد الإلكتروني</span>
                    </Link>
                </div>
            </div>
        </GuestLayout>
    );
}
