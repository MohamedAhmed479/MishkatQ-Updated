import { useState, useEffect } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    BookOpen,
    Calendar,
    Target,
    ChevronLeft,
    ChevronRight,
    Check,
    Zap,
    Moon,
    Sun,
    BookMarked,
    Clock,
} from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card, { CardContent, CardHeader } from '@/Components/UI/Card';
import Button from '@/Components/UI/Button';

const TOTAL_PAGES = 604;

export default function CreateReadingPlan({ suggestions, errors: pageErrors }) {
    const { url } = usePage();
    const urlParams = new URLSearchParams(url.split('?')[1] || '');
    
    const [step, setStep] = useState(1);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState(null);
    const [formData, setFormData] = useState({
        name: 'خطة ختم القرآن',
        type: 'sequential',
        pages_per_day: parseInt(urlParams.get('pages')) || 20,
        start_page: 1,
        end_page: TOTAL_PAGES,
        start_date: new Date().toISOString().split('T')[0],
        reading_mode: 'hadr',
        settings: {
            theme: 'classic',
            font_size: 24,
            auto_scroll: false,
            haptic_feedback: true,
        },
    });

    const estimatedDays = Math.ceil((formData.end_page - formData.start_page + 1) / formData.pages_per_day);
    const estimatedEndDate = new Date(formData.start_date);
    estimatedEndDate.setDate(estimatedEndDate.getDate() + estimatedDays - 1);

    // Show errors from server
    useEffect(() => {
        if (pageErrors?.error) {
            setError(pageErrors.error);
        }
    }, [pageErrors]);

    const handleSubmit = () => {
        setError(null);
        setIsSubmitting(true);
        
        router.post('/app/reading/plans', formData, {
            onSuccess: () => {
                router.visit('/app/reading');
            },
            onError: (errors) => {
                console.error('Validation errors:', errors);
                if (errors.error) {
                    setError(errors.error);
                } else if (typeof errors === 'string') {
                    setError(errors);
                } else {
                    setError('حدث خطأ أثناء إنشاء الخطة. يرجى المحاولة مرة أخرى.');
                }
                setIsSubmitting(false);
            },
            onFinish: () => {
                setIsSubmitting(false);
            },
        });
    };

    const suggestedPlans = suggestions?.data?.suggestions || [
        { name: 'ختمة شهرية', days: 30, pages_per_day: 20, difficulty: 'متوسط' },
        { name: 'ختمة في شهرين', days: 60, pages_per_day: 10, difficulty: 'سهل' },
        { name: 'صفحة يومياً', days: 604, pages_per_day: 1, difficulty: 'يسير جداً' },
    ];

    return (
        <MainLayout title="إنشاء خطة قراءة">
            <Head title="إنشاء خطة قراءة - مشكاة" />

            <div className="max-w-xl mx-auto space-y-6 pb-8">
                {/* Header */}
                <div className="flex items-center gap-4">
                    <button
                        onClick={() => step > 1 ? setStep(step - 1) : router.visit('/app/reading')}
                        className="p-2 rounded-xl bg-surface-100 dark:bg-dark-300 hover:bg-surface-200 dark:hover:bg-dark-200 transition"
                    >
                        <ChevronRight className="w-5 h-5 text-text-primary dark:text-text-dark-primary" />
                    </button>
                    <div className="flex-1">
                        <h1 className="text-xl font-bold text-text-primary dark:text-text-dark-primary">
                            إنشاء خطة قراءة
                        </h1>
                        <p className="text-sm text-text-muted dark:text-text-dark-muted">
                            الخطوة {step} من 3
                        </p>
                    </div>
                </div>

                {/* Progress Indicator */}
                <div className="flex gap-2">
                    {[1, 2, 3].map((s) => (
                        <motion.div
                            key={s}
                            className={`h-1.5 flex-1 rounded-full ${
                                s <= step ? 'bg-primary-500' : 'bg-surface-200 dark:bg-dark-300'
                            }`}
                            initial={{ scaleX: 0 }}
                            animate={{ scaleX: 1 }}
                            transition={{ delay: s * 0.1 }}
                        />
                    ))}
                </div>

                {/* Error Message */}
                {error && (
                    <motion.div
                        initial={{ opacity: 0, y: -10 }}
                        animate={{ opacity: 1, y: 0 }}
                        className="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800"
                    >
                        <p className="text-red-600 dark:text-red-400 text-sm">{error}</p>
                    </motion.div>
                )}

                <AnimatePresence mode="wait">
                    {step === 1 && (
                        <StepOne
                            key="step1"
                            formData={formData}
                            setFormData={setFormData}
                            suggestions={suggestedPlans}
                            onNext={() => setStep(2)}
                        />
                    )}
                    {step === 2 && (
                        <StepTwo
                            key="step2"
                            formData={formData}
                            setFormData={setFormData}
                            estimatedDays={estimatedDays}
                            estimatedEndDate={estimatedEndDate}
                            onNext={() => setStep(3)}
                        />
                    )}
                    {step === 3 && (
                        <StepThree
                            key="step3"
                            formData={formData}
                            setFormData={setFormData}
                            estimatedDays={estimatedDays}
                            estimatedEndDate={estimatedEndDate}
                            onSubmit={handleSubmit}
                            isSubmitting={isSubmitting}
                        />
                    )}
                </AnimatePresence>
            </div>
        </MainLayout>
    );
}

// Step 1: Choose Plan Type and Daily Target
function StepOne({ formData, setFormData, suggestions, onNext }) {
    const quickOptions = [
        { pages: 1, label: 'صفحة', description: 'يسير جداً' },
        { pages: 5, label: 'صفحات', description: 'سهل' },
        { pages: 10, label: 'صفحات', description: 'متوسط' },
        { pages: 20, label: 'صفحة', description: 'جزء/يوم' },
    ];

    return (
        <motion.div
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            exit={{ opacity: 0, x: -20 }}
            className="space-y-6"
        >
            {/* Plan Name */}
            <Card>
                <CardContent className="pt-6">
                    <label className="block text-sm font-medium text-text-primary dark:text-text-dark-primary mb-2">
                        اسم الخطة
                    </label>
                    <input
                        type="text"
                        value={formData.name}
                        onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                        className="w-full px-4 py-3 rounded-xl border border-surface-300 dark:border-dark-300 
                                   bg-white dark:bg-dark-400 text-text-primary dark:text-text-dark-primary
                                   focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                        placeholder="مثال: ختمة رمضان"
                    />
                </CardContent>
            </Card>

            {/* Daily Target Selection */}
            <Card>
                <CardHeader>
                    <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                        الورد اليومي
                    </h2>
                    <p className="text-sm text-text-muted dark:text-text-dark-muted">
                        كم صفحة تريد قراءتها يومياً؟
                    </p>
                </CardHeader>
                <CardContent>
                    <div className="grid grid-cols-2 gap-3 mb-4">
                        {quickOptions.map((option) => (
                            <motion.button
                                key={option.pages}
                                whileHover={{ scale: 1.02 }}
                                whileTap={{ scale: 0.98 }}
                                onClick={() => setFormData({ ...formData, pages_per_day: option.pages })}
                                className={`p-4 rounded-xl border-2 transition ${
                                    formData.pages_per_day === option.pages
                                        ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                        : 'border-surface-200 dark:border-dark-300 hover:border-primary-300'
                                }`}
                            >
                                <p className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                                    {option.pages}
                                </p>
                                <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                    {option.label}
                                </p>
                                <p className="text-xs text-primary-600 dark:text-primary-400 mt-1">
                                    {option.description}
                                </p>
                            </motion.button>
                        ))}
                    </div>

                    {/* Custom Input */}
                    <div className="flex items-center gap-3 p-4 rounded-xl bg-surface-50 dark:bg-dark-300">
                        <Target className="w-5 h-5 text-primary-500" />
                        <input
                            type="number"
                            min="1"
                            max="100"
                            value={formData.pages_per_day}
                            onChange={(e) => setFormData({ 
                                ...formData, 
                                pages_per_day: Math.max(1, Math.min(100, parseInt(e.target.value) || 1)) 
                            })}
                            className="flex-1 px-3 py-2 rounded-lg border border-surface-300 dark:border-dark-200 
                                       bg-white dark:bg-dark-400 text-text-primary dark:text-text-dark-primary
                                       text-center font-bold"
                        />
                        <span className="text-text-muted dark:text-text-dark-muted">صفحة / يوم</span>
                    </div>
                </CardContent>
            </Card>

            <Button onClick={onNext} size="lg" className="w-full" icon={ChevronLeft} iconPosition="left">
                التالي
            </Button>
        </motion.div>
    );
}

// Step 2: Reading Mode and Schedule
function StepTwo({ formData, setFormData, estimatedDays, estimatedEndDate, onNext }) {
    const readingModes = [
        {
            id: 'hadr',
            icon: Zap,
            title: 'القراءة السريعة',
            description: 'قراءة متواصلة بدون توقف للتفسير',
            color: 'primary',
        },
        {
            id: 'tadabbur',
            icon: BookMarked,
            title: 'التدبر',
            description: 'قراءة متأنية مع التفسير والتأمل',
            color: 'accent',
        },
    ];

    return (
        <motion.div
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            exit={{ opacity: 0, x: -20 }}
            className="space-y-6"
        >
            {/* Reading Mode */}
            <Card>
                <CardHeader>
                    <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                        نية القراءة
                    </h2>
                    <p className="text-sm text-text-muted dark:text-text-dark-muted">
                        اختر الطريقة التي تناسبك
                    </p>
                </CardHeader>
                <CardContent>
                    <div className="space-y-3">
                        {readingModes.map((mode) => (
                            <motion.button
                                key={mode.id}
                                whileHover={{ scale: 1.01 }}
                                whileTap={{ scale: 0.99 }}
                                onClick={() => setFormData({ ...formData, reading_mode: mode.id })}
                                className={`w-full p-4 rounded-xl border-2 flex items-center gap-4 transition ${
                                    formData.reading_mode === mode.id
                                        ? `border-${mode.color}-500 bg-${mode.color}-50 dark:bg-${mode.color}-900/20`
                                        : 'border-surface-200 dark:border-dark-300 hover:border-primary-300'
                                }`}
                            >
                                <div className={`w-12 h-12 rounded-xl flex items-center justify-center ${
                                    formData.reading_mode === mode.id
                                        ? `bg-${mode.color}-500 text-white`
                                        : 'bg-surface-100 dark:bg-dark-300 text-text-muted'
                                }`}>
                                    <mode.icon className="w-6 h-6" />
                                </div>
                                <div className="flex-1 text-right">
                                    <h3 className="font-bold text-text-primary dark:text-text-dark-primary">
                                        {mode.title}
                                    </h3>
                                    <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                        {mode.description}
                                    </p>
                                </div>
                                {formData.reading_mode === mode.id && (
                                    <Check className={`w-5 h-5 text-${mode.color}-500`} />
                                )}
                            </motion.button>
                        ))}
                    </div>
                </CardContent>
            </Card>

            {/* Start Date */}
            <Card>
                <CardHeader>
                    <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                        تاريخ البدء
                    </h2>
                </CardHeader>
                <CardContent>
                    <input
                        type="date"
                        value={formData.start_date}
                        min={new Date().toISOString().split('T')[0]}
                        onChange={(e) => setFormData({ ...formData, start_date: e.target.value })}
                        className="w-full px-4 py-3 rounded-xl border border-surface-300 dark:border-dark-300 
                                   bg-white dark:bg-dark-400 text-text-primary dark:text-text-dark-primary
                                   focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                    />

                    {/* Estimated Completion */}
                    <div className="mt-4 p-4 rounded-xl bg-surface-50 dark:bg-dark-300">
                        <div className="flex items-center gap-3 text-text-muted dark:text-text-dark-muted">
                            <Calendar className="w-5 h-5" />
                            <div>
                                <p className="text-sm">تاريخ الانتهاء المتوقع</p>
                                <p className="font-bold text-text-primary dark:text-text-dark-primary">
                                    {estimatedEndDate.toLocaleDateString('ar-SA', { 
                                        year: 'numeric', 
                                        month: 'long', 
                                        day: 'numeric' 
                                    })}
                                    <span className="text-sm font-normal text-text-muted mr-2">
                                        ({estimatedDays} يوم)
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Button onClick={onNext} size="lg" className="w-full" icon={ChevronLeft} iconPosition="left">
                التالي
            </Button>
        </motion.div>
    );
}

// Step 3: Theme and Confirmation
function StepThree({ formData, setFormData, estimatedDays, estimatedEndDate, onSubmit, isSubmitting }) {
    const themes = [
        { id: 'classic', name: 'كلاسيكي', bg: 'bg-amber-50', text: 'text-amber-900', icon: Sun },
        { id: 'night', name: 'ليلي', bg: 'bg-gray-900', text: 'text-gray-100', icon: Moon },
        { id: 'soft_blue', name: 'أزرق هادئ', bg: 'bg-blue-50', text: 'text-blue-900', icon: BookOpen },
        { id: 'mint', name: 'نعناعي', bg: 'bg-emerald-50', text: 'text-emerald-900', icon: BookMarked },
    ];

    const updateSettings = (key, value) => {
        setFormData({
            ...formData,
            settings: { ...formData.settings, [key]: value }
        });
    };

    return (
        <motion.div
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            exit={{ opacity: 0, x: -20 }}
            className="space-y-6"
        >
            {/* Theme Selection */}
            <Card>
                <CardHeader>
                    <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                        اختر المظهر
                    </h2>
                    <p className="text-sm text-text-muted dark:text-text-dark-muted">
                        يمكنك تغييره لاحقاً
                    </p>
                </CardHeader>
                <CardContent>
                    <div className="grid grid-cols-2 gap-3">
                        {themes.map((theme) => (
                            <motion.button
                                key={theme.id}
                                whileHover={{ scale: 1.02 }}
                                whileTap={{ scale: 0.98 }}
                                onClick={() => updateSettings('theme', theme.id)}
                                className={`p-4 rounded-xl border-2 transition ${
                                    formData.settings.theme === theme.id
                                        ? 'border-primary-500'
                                        : 'border-surface-200 dark:border-dark-300'
                                }`}
                            >
                                <div className={`w-full h-16 rounded-lg ${theme.bg} ${theme.text} flex items-center justify-center mb-2`}>
                                    <theme.icon className="w-6 h-6" />
                                </div>
                                <p className="text-sm font-medium text-text-primary dark:text-text-dark-primary">
                                    {theme.name}
                                </p>
                            </motion.button>
                        ))}
                    </div>
                </CardContent>
            </Card>

            {/* Summary */}
            <Card className="bg-gradient-to-br from-primary-500 to-primary-600 text-white">
                <CardContent className="py-6">
                    <h2 className="text-lg font-bold mb-4">ملخص الخطة</h2>
                    <div className="space-y-3 text-primary-100">
                        <div className="flex justify-between">
                            <span>اسم الخطة</span>
                            <span className="text-white font-medium">{formData.name}</span>
                        </div>
                        <div className="flex justify-between">
                            <span>الورد اليومي</span>
                            <span className="text-white font-medium">{formData.pages_per_day} صفحة</span>
                        </div>
                        <div className="flex justify-between">
                            <span>نية القراءة</span>
                            <span className="text-white font-medium">
                                {formData.reading_mode === 'tadabbur' ? 'تدبر' : 'قراءة سريعة'}
                            </span>
                        </div>
                        <div className="flex justify-between">
                            <span>المدة المتوقعة</span>
                            <span className="text-white font-medium">{estimatedDays} يوم</span>
                        </div>
                        <div className="flex justify-between">
                            <span>تاريخ الانتهاء</span>
                            <span className="text-white font-medium">
                                {estimatedEndDate.toLocaleDateString('ar-SA')}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Button 
                onClick={onSubmit} 
                size="lg" 
                className="w-full" 
                loading={isSubmitting}
                icon={Check}
            >
                بدء الختمة
            </Button>
        </motion.div>
    );
}
