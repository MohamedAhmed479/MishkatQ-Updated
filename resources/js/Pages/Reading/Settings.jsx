import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import axios from 'axios';
import {
    ChevronRight,
    Settings,
    Palette,
    Type,
    Volume2,
    Zap,
    BookMarked,
    Sun,
    Moon,
    BookOpen,
    Minus,
    Plus,
    Check,
    Pause,
    Play,
    Trash2,
    RefreshCw,
} from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card, { CardContent, CardHeader } from '@/Components/UI/Card';
import Button from '@/Components/UI/Button';

const THEMES = [
    { id: 'classic', name: 'كلاسيكي', bg: 'bg-amber-50', icon: Sun },
    { id: 'night', name: 'ليلي', bg: 'bg-gray-900', icon: Moon },
    { id: 'soft_blue', name: 'أزرق هادئ', bg: 'bg-blue-50', icon: BookOpen },
    { id: 'mint', name: 'نعناعي', bg: 'bg-emerald-50', icon: BookMarked },
];

export default function PlanSettings({ plan, reciters }) {
    const [settings, setSettings] = useState(plan.settings || {});
    const [readingMode, setReadingMode] = useState(plan.reading_mode || 'hadr');
    const [pagesPerDay, setPagesPerDay] = useState(plan.pages_per_day || 20);
    const [isSaving, setIsSaving] = useState(false);
    const [isPausing, setIsPausing] = useState(false);
    const [isAdjusting, setIsAdjusting] = useState(false);

    const updateSetting = (key, value) => {
        setSettings({ ...settings, [key]: value });
    };

    const saveSettings = async () => {
        setIsSaving(true);
        try {
            const response = await axios.patch(`/app/reading/api/plans/${plan.id}/settings`, {
                ...settings,
                reading_mode: readingMode,
            });

            if (response.data?.status) {
                // Reload page to show updated settings
                router.reload({
                    only: ['plan'],
                    preserveScroll: true,
                });
            }
        } catch (error) {
            console.error('Failed to save settings:', error);
            alert(error.response?.data?.message || 'حدث خطأ أثناء حفظ الإعدادات');
        } finally {
            setIsSaving(false);
        }
    };

    const togglePlanStatus = async () => {
        setIsPausing(true);
        try {
            if (plan.status === 'active') {
                await axios.post(`/app/reading/api/plans/${plan.id}/pause`);
            } else {
                await axios.post(`/app/reading/api/plans/${plan.id}/resume`);
            }
            router.reload();
        } catch (error) {
            console.error('Failed to toggle plan status:', error);
        } finally {
            setIsPausing(false);
        }
    };

    const autoAdjust = async () => {
        setIsAdjusting(true);
        try {
            await axios.post(`/app/reading/api/plans/${plan.id}/adjust`);
            router.reload();
        } catch (error) {
            console.error('Failed to adjust plan:', error);
        } finally {
            setIsAdjusting(false);
        }
    };

    const deletePlan = async () => {
        if (!confirm('هل أنت متأكد من حذف هذه الخطة؟')) return;

        try {
            await axios.delete(`/app/reading/api/plans/${plan.id}`);
            router.visit('/app/reading');
        } catch (error) {
            console.error('Failed to delete plan:', error);
        }
    };

    return (
        <MainLayout title="إعدادات الخطة">
            <Head title="إعدادات الخطة - مشكاة" />

            <div className="max-w-xl mx-auto space-y-6 pb-8">
                {/* Header */}
                <div className="flex items-center gap-4">
                    <Link
                        href="/app/reading"
                        className="p-2 rounded-xl bg-surface-100 dark:bg-dark-300 hover:bg-surface-200 dark:hover:bg-dark-200 transition"
                    >
                        <ChevronRight className="w-5 h-5 text-text-primary dark:text-text-dark-primary" />
                    </Link>
                    <div className="flex-1">
                        <h1 className="text-xl font-bold text-text-primary dark:text-text-dark-primary">
                            إعدادات الخطة
                        </h1>
                        <p className="text-sm text-text-muted dark:text-text-dark-muted">
                            {plan.name}
                        </p>
                    </div>
                </div>

                {/* Plan Info */}
                <Card className="bg-gradient-to-br from-primary-500 to-primary-600 text-white">
                    <CardContent className="py-6">
                        <div className="flex items-center justify-between mb-4">
                            <h2 className="font-bold">{plan.name}</h2>
                            <span className={`px-3 py-1 rounded-full text-sm ${plan.status === 'active'
                                ? 'bg-green-500/20 text-green-200'
                                : 'bg-yellow-500/20 text-yellow-200'
                                }`}>
                                {plan.status === 'active' ? 'نشطة' : 'متوقفة'}
                            </span>
                        </div>
                        <div className="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <p className="text-2xl font-bold">{Math.round(plan.progress_percentage)}%</p>
                                <p className="text-sm text-primary-200">مكتمل</p>
                            </div>
                            <div>
                                <p className="text-2xl font-bold">{plan.remaining_pages}</p>
                                <p className="text-sm text-primary-200">صفحة متبقية</p>
                            </div>
                            <div>
                                <p className="text-2xl font-bold">{plan.days_remaining}</p>
                                <p className="text-sm text-primary-200">يوم متبقي</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Theme Selection */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-3">
                            <Palette className="w-5 h-5 text-primary-500" />
                            <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                المظهر
                            </h2>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-4 gap-3">
                            {THEMES.map((theme) => (
                                <motion.button
                                    key={theme.id}
                                    whileHover={{ scale: 1.02 }}
                                    whileTap={{ scale: 0.98 }}
                                    onClick={() => updateSetting('theme', theme.id)}
                                    className={`p-3 rounded-xl border-2 transition ${settings.theme === theme.id
                                        ? 'border-primary-500'
                                        : 'border-surface-200 dark:border-dark-300'
                                        }`}
                                >
                                    <div className={`w-full h-10 rounded-lg ${theme.bg} mb-2 flex items-center justify-center`}>
                                        <theme.icon className="w-5 h-5 text-gray-600" />
                                    </div>
                                    <span className="text-xs text-text-primary dark:text-text-dark-primary">
                                        {theme.name}
                                    </span>
                                </motion.button>
                            ))}
                        </div>
                    </CardContent>
                </Card>

                {/* Font Size */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-3">
                            <Type className="w-5 h-5 text-primary-500" />
                            <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                حجم الخط
                            </h2>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div className="flex items-center gap-4">
                            <button
                                onClick={() => updateSetting('font_size', Math.max(18, (settings.font_size || 28) - 2))}
                                className="p-3 rounded-xl bg-surface-100 dark:bg-dark-300 hover:bg-surface-200 dark:hover:bg-dark-200"
                            >
                                <Minus className="w-5 h-5" />
                            </button>
                            <div className="flex-1 text-center">
                                <span className="text-3xl font-bold text-text-primary dark:text-text-dark-primary">
                                    {settings.font_size || 28}
                                </span>
                                <p className="text-sm text-text-muted">نقطة</p>
                            </div>
                            <button
                                onClick={() => updateSetting('font_size', Math.min(48, (settings.font_size || 28) + 2))}
                                className="p-3 rounded-xl bg-surface-100 dark:bg-dark-300 hover:bg-surface-200 dark:hover:bg-dark-200"
                            >
                                <Plus className="w-5 h-5" />
                            </button>
                        </div>
                    </CardContent>
                </Card>

                {/* Script Type */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-3">
                            <BookOpen className="w-5 h-5 text-primary-500" />
                            <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                نوع الخط
                            </h2>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-2 gap-3">
                            <button
                                onClick={() => updateSetting('script_type', 'uthmani')}
                                className={`p-4 rounded-xl border-2 flex items-center gap-3 transition ${(settings.script_type || 'uthmani') === 'uthmani'
                                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                    : 'border-surface-200 dark:border-dark-300 hover:border-primary-300'
                                    }`}
                            >
                                <div className="text-right flex-1">
                                    <p className="font-medium text-text-primary dark:text-text-dark-primary">
                                        عثماني
                                    </p>
                                    <p className="text-xs text-text-muted">خط المصحف</p>
                                </div>
                                {(settings.script_type || 'uthmani') === 'uthmani' && (
                                    <Check className="w-5 h-5 text-primary-500" />
                                )}
                            </button>
                            <button
                                onClick={() => updateSetting('script_type', 'imlaei')}
                                className={`p-4 rounded-xl border-2 flex items-center gap-3 transition ${settings.script_type === 'imlaei'
                                    ? 'border-accent-500 bg-accent-50 dark:bg-accent-900/20'
                                    : 'border-surface-200 dark:border-dark-300 hover:border-accent-300'
                                    }`}
                            >
                                <div className="text-right flex-1">
                                    <p className="font-medium text-text-primary dark:text-text-dark-primary">
                                        إملائي
                                    </p>
                                    <p className="text-xs text-text-muted">خط مبسط</p>
                                </div>
                                {settings.script_type === 'imlaei' && (
                                    <Check className="w-5 h-5 text-accent-500" />
                                )}
                            </button>
                        </div>
                    </CardContent>
                </Card>

                {/* Reading Mode */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-3">
                            <BookOpen className="w-5 h-5 text-primary-500" />
                            <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                نية القراءة الافتراضية
                            </h2>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-2 gap-3">
                            <button
                                onClick={() => setReadingMode('hadr')}
                                className={`p-4 rounded-xl border-2 flex items-center gap-3 transition ${readingMode === 'hadr'
                                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                    : 'border-surface-200 dark:border-dark-300'
                                    }`}
                            >
                                <Zap className={`w-6 h-6 ${readingMode === 'hadr' ? 'text-primary-500' : 'text-text-muted'}`} />
                                <div className="text-right">
                                    <p className="font-medium text-text-primary dark:text-text-dark-primary">
                                        قراءة سريعة
                                    </p>
                                    <p className="text-xs text-text-muted">بدون تفسير</p>
                                </div>
                            </button>
                            <button
                                onClick={() => setReadingMode('tadabbur')}
                                className={`p-4 rounded-xl border-2 flex items-center gap-3 transition ${readingMode === 'tadabbur'
                                    ? 'border-accent-500 bg-accent-50 dark:bg-accent-900/20'
                                    : 'border-surface-200 dark:border-dark-300'
                                    }`}
                            >
                                <BookMarked className={`w-6 h-6 ${readingMode === 'tadabbur' ? 'text-accent-500' : 'text-text-muted'}`} />
                                <div className="text-right">
                                    <p className="font-medium text-text-primary dark:text-text-dark-primary">
                                        تدبر
                                    </p>
                                    <p className="text-xs text-text-muted">مع التفسير</p>
                                </div>
                            </button>
                        </div>
                    </CardContent>
                </Card>

                {/* Reciter Selection */}
                {reciters?.length > 0 && (
                    <Card>
                        <CardHeader>
                            <div className="flex items-center gap-3">
                                <Volume2 className="w-5 h-5 text-primary-500" />
                                <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                    القارئ الافتراضي
                                </h2>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <select
                                value={settings.reciter_id || ''}
                                onChange={(e) => updateSetting('reciter_id', parseInt(e.target.value))}
                                className="w-full p-3 rounded-xl border border-surface-300 dark:border-dark-300 
                                           bg-white dark:bg-dark-400 text-text-primary dark:text-text-dark-primary"
                            >
                                <option value="">اختر قارئ</option>
                                {reciters.map((reciter) => (
                                    <option key={reciter.id} value={reciter.id}>
                                        {reciter.reciter_name || reciter.name}
                                    </option>
                                ))}
                            </select>
                        </CardContent>
                    </Card>
                )}

                {/* Save Button */}
                <Button
                    onClick={saveSettings}
                    loading={isSaving}
                    size="lg"
                    className="w-full"
                    icon={Check}
                >
                    حفظ الإعدادات
                </Button>

                {/* Plan Actions */}
                <Card>
                    <CardHeader>
                        <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                            إجراءات الخطة
                        </h2>
                    </CardHeader>
                    <CardContent className="space-y-3">
                        {/* Pause/Resume */}
                        <button
                            onClick={togglePlanStatus}
                            disabled={isPausing}
                            className="w-full flex items-center gap-3 p-4 rounded-xl bg-surface-100 dark:bg-dark-300 hover:bg-surface-200 dark:hover:bg-dark-200 transition"
                        >
                            {plan.status === 'active' ? (
                                <>
                                    <Pause className="w-5 h-5 text-yellow-600" />
                                    <span className="text-text-primary dark:text-text-dark-primary">
                                        إيقاف الخطة مؤقتاً
                                    </span>
                                </>
                            ) : (
                                <>
                                    <Play className="w-5 h-5 text-green-600" />
                                    <span className="text-text-primary dark:text-text-dark-primary">
                                        استئناف الخطة
                                    </span>
                                </>
                            )}
                        </button>

                        {/* Auto Adjust */}
                        <button
                            onClick={autoAdjust}
                            disabled={isAdjusting}
                            className="w-full flex items-center gap-3 p-4 rounded-xl bg-surface-100 dark:bg-dark-300 hover:bg-surface-200 dark:hover:bg-dark-200 transition"
                        >
                            <RefreshCw className={`w-5 h-5 text-blue-600 ${isAdjusting ? 'animate-spin' : ''}`} />
                            <span className="text-text-primary dark:text-text-dark-primary">
                                تعديل الخطة تلقائياً
                            </span>
                        </button>

                        {/* Delete */}
                        <button
                            onClick={deletePlan}
                            className="w-full flex items-center gap-3 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 transition"
                        >
                            <Trash2 className="w-5 h-5 text-red-600" />
                            <span className="text-red-600">
                                حذف الخطة
                            </span>
                        </button>
                    </CardContent>
                </Card>
            </div>
        </MainLayout>
    );
}
