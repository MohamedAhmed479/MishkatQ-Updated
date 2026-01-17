import { Head, useForm, router } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import { useState } from 'react';
import {
    Clock,
    Gauge,
    ChevronLeft,
    ChevronRight,
    CheckCircle,
    BookOpen
} from 'lucide-react';
import GuestLayout from '@/Layouts/GuestLayout';
import Button from '@/Components/UI/Button';

const steps = [
    { id: 1, title: 'مستواك', icon: Gauge },
    { id: 2, title: 'وقتك', icon: Clock },
    { id: 3, title: 'تأكيد', icon: CheckCircle },
];

const levels = [
    {
        value: 'beginner',
        label: 'مبتدئ',
        description: 'أبدأ رحلة الحفظ لأول مرة أو حافظ قليل',
        emoji: '🌱',
        rate: '1.5 كلمة/دقيقة'
    },
    {
        value: 'intermediate',
        label: 'متوسط',
        description: 'لدي خبرة سابقة في الحفظ',
        emoji: '🌿',
        rate: '2.5 كلمة/دقيقة'
    },
    {
        value: 'advanced',
        label: 'متقدم',
        description: 'حافظ لأجزاء كبيرة أو القرآن كاملاً',
        emoji: '🌳',
        rate: '6 كلمات/دقيقة'
    },
];

const timeOptions = [
    { value: 15, label: '15 دقيقة', description: 'للمشغولين جداً' },
    { value: 30, label: '30 دقيقة', description: 'توازن مثالي' },
    { value: 45, label: '45 دقيقة', description: 'تقدم جيد' },
    { value: 60, label: 'ساعة', description: 'للجادين' },
    { value: 90, label: 'ساعة ونصف', description: 'تقدم سريع' },
];

export default function Preferences() {
    const [currentStep, setCurrentStep] = useState(1);
    const { data, setData, post, processing } = useForm({
        hifz_level: '',
        daily_time_minutes: 30,
    });

    const canProceed = () => {
        if (currentStep === 1) return !!data.hifz_level;
        if (currentStep === 2) return !!data.daily_time_minutes;
        return true;
    };

    const handleNext = () => {
        if (currentStep < 3) {
            setCurrentStep(currentStep + 1);
        } else {
            post('/app/preferences');
        }
    };

    const handleBack = () => {
        if (currentStep > 1) {
            setCurrentStep(currentStep - 1);
        }
    };

    return (
        <GuestLayout>
            <Head title="إعداد التفضيلات" />

            <div className="bg-white dark:bg-dark-400 rounded-3xl shadow-2xl p-8 max-w-lg mx-auto">
                {/* Progress Steps */}
                <div className="flex items-center justify-center gap-4 mb-8">
                    {steps.map((step, index) => (
                        <div key={step.id} className="flex items-center">
                            <motion.div
                                initial={false}
                                animate={{
                                    scale: currentStep === step.id ? 1.1 : 1,
                                    backgroundColor: currentStep >= step.id
                                        ? 'var(--color-primary-500)'
                                        : 'var(--color-surface-200)'
                                }}
                                className={`
                                    w-10 h-10 rounded-full flex items-center justify-center
                                    ${currentStep >= step.id ? 'text-white' : 'text-text-muted dark:text-text-dark-muted'}
                                `}
                            >
                                {currentStep > step.id ? (
                                    <CheckCircle className="w-5 h-5" />
                                ) : (
                                    <step.icon className="w-5 h-5" />
                                )}
                            </motion.div>
                            {index < steps.length - 1 && (
                                <div
                                    className={`w-12 h-1 mx-2 rounded-full transition-colors ${currentStep > step.id
                                        ? 'bg-primary-500'
                                        : 'bg-surface-200 dark:bg-dark-300'
                                        }`}
                                />
                            )}
                        </div>
                    ))}
                </div>

                {/* Step Content */}
                <AnimatePresence mode="wait">
                    {currentStep === 1 && (
                        <motion.div
                            key="step1"
                            initial={{ opacity: 0, x: 50 }}
                            animate={{ opacity: 1, x: 0 }}
                            exit={{ opacity: 0, x: -50 }}
                            className="space-y-6"
                        >
                            <div className="text-center">
                                <h2 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                                    ما هو مستواك في الحفظ؟
                                </h2>
                                <p className="text-text-muted dark:text-text-dark-muted mt-2">
                                    سنخصص تجربتك بناءً على مستواك
                                </p>
                            </div>

                            <div className="space-y-3">
                                {levels.map((level) => (
                                    <motion.button
                                        key={level.value}
                                        whileHover={{ scale: 1.02 }}
                                        whileTap={{ scale: 0.98 }}
                                        onClick={() => setData('hifz_level', level.value)}
                                        className={`
                                            w-full p-4 rounded-2xl border-2 text-right transition-all
                                            ${data.hifz_level === level.value
                                                ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                                : 'border-surface-300 dark:border-dark-300 hover:border-primary-300'
                                            }
                                        `}
                                    >
                                        <div className="flex items-center gap-4">
                                            <span className="text-3xl">{level.emoji}</span>
                                            <div className="flex-1">
                                                <h3 className="font-bold text-text-primary dark:text-text-dark-primary">
                                                    {level.label}
                                                </h3>
                                                <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                                    {level.description}
                                                </p>
                                            </div>
                                            <span className="text-xs text-primary-600 dark:text-primary-400 bg-primary-100 dark:bg-primary-900/30 px-2 py-1 rounded-lg">
                                                {level.rate}
                                            </span>
                                        </div>
                                    </motion.button>
                                ))}
                            </div>
                        </motion.div>
                    )}

                    {currentStep === 2 && (
                        <motion.div
                            key="step2"
                            initial={{ opacity: 0, x: 50 }}
                            animate={{ opacity: 1, x: 0 }}
                            exit={{ opacity: 0, x: -50 }}
                            className="space-y-6"
                        >
                            <div className="text-center">
                                <h2 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                                    كم وقت يمكنك تخصيصه يومياً؟
                                </h2>
                                <p className="text-text-muted dark:text-text-dark-muted mt-2">
                                    60% للحفظ الجديد و 40% للمراجعة
                                </p>
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                {timeOptions.map((option) => (
                                    <motion.button
                                        key={option.value}
                                        whileHover={{ scale: 1.02 }}
                                        whileTap={{ scale: 0.98 }}
                                        onClick={() => setData('daily_time_minutes', option.value)}
                                        className={`
                                            p-4 rounded-2xl border-2 text-center transition-all
                                            ${data.daily_time_minutes === option.value
                                                ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                                : 'border-surface-300 dark:border-dark-300 hover:border-primary-300'
                                            }
                                        `}
                                    >
                                        <div className="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                            {option.label}
                                        </div>
                                        <div className="text-xs text-text-muted dark:text-text-dark-muted mt-1">
                                            {option.description}
                                        </div>
                                    </motion.button>
                                ))}
                            </div>
                        </motion.div>
                    )}

                    {currentStep === 3 && (
                        <motion.div
                            key="step3"
                            initial={{ opacity: 0, x: 50 }}
                            animate={{ opacity: 1, x: 0 }}
                            exit={{ opacity: 0, x: -50 }}
                            className="space-y-6"
                        >
                            <div className="text-center">
                                <motion.div
                                    initial={{ scale: 0 }}
                                    animate={{ scale: 1 }}
                                    transition={{ type: "spring", delay: 0.2 }}
                                    className="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center"
                                >
                                    <BookOpen className="w-10 h-10 text-white" />
                                </motion.div>
                                <h2 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                                    أنت جاهز للانطلاق!
                                </h2>
                                <p className="text-text-muted dark:text-text-dark-muted mt-2">
                                    تأكد من اختياراتك قبل البدء
                                </p>
                            </div>

                            <div className="bg-surface-100 dark:bg-dark-300 rounded-2xl p-6 space-y-4">
                                <div className="flex items-center justify-between">
                                    <span className="text-text-muted dark:text-text-dark-muted">المستوى</span>
                                    <span className="font-bold text-text-primary dark:text-text-dark-primary">
                                        {levels.find(l => l.value === data.hifz_level)?.label}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-text-muted dark:text-text-dark-muted">الوقت اليومي</span>
                                    <span className="font-bold text-text-primary dark:text-text-dark-primary">
                                        {timeOptions.find(t => t.value === data.daily_time_minutes)?.label}
                                    </span>
                                </div>
                            </div>

                            <p className="text-sm text-text-muted dark:text-text-dark-muted text-center">
                                يمكنك تغيير هذه الإعدادات لاحقاً من صفحة الإعدادات
                            </p>
                        </motion.div>
                    )}
                </AnimatePresence>

                {/* Navigation */}
                <div className="flex items-center justify-between mt-8 pt-6 border-t border-surface-200 dark:border-dark-300">
                    <Button
                        variant="ghost"
                        onClick={handleBack}
                        disabled={currentStep === 1}
                        icon={ChevronRight}
                    >
                        السابق
                    </Button>

                    <Button
                        onClick={handleNext}
                        disabled={!canProceed()}
                        loading={processing}
                        icon={currentStep === 3 ? CheckCircle : ChevronLeft}
                        iconPosition="left"
                    >
                        {currentStep === 3 ? 'ابدأ الآن' : 'التالي'}
                    </Button>
                </div>
            </div>
        </GuestLayout>
    );
}
