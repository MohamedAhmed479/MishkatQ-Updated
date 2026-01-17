import { Head, useForm, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    User,
    Mail,
    Clock,
    Gauge,
    Bell,
    Moon,
    LogOut,
    Save
} from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card, { CardContent, CardHeader } from '@/Components/UI/Card';
import Button from '@/Components/UI/Button';
import Input from '@/Components/UI/Input';
import { useTheme } from '@/Contexts/ThemeContext';

const levels = [
    { value: 'beginner', label: 'مبتدئ' },
    { value: 'intermediate', label: 'متوسط' },
    { value: 'advanced', label: 'متقدم' },
];

const timeOptions = [15, 30, 45, 60, 90];

function SettingsContent({ user = {}, preferences = {}, tafsirs = [] }) {
    const { theme, toggleTheme } = useTheme();

    // Ensure we have default values
    const safeUser = {
        id: user?.id || 0,
        name: user?.name || '',
        email: user?.email || '',
        created_at: user?.created_at || '',
    };

    const safePreferences = {
        hifz_level: preferences?.hifz_level || 'beginner',
        daily_time_minutes: preferences?.daily_time_minutes || 30,
        notification_enabled: preferences?.notification_enabled ?? true,
        tafsir_id: preferences?.tafsir_id ?? 1,
    };

    const { data, setData, put, processing } = useForm({
        name: safeUser.name,
        hifz_level: safePreferences.hifz_level,
        daily_time_minutes: safePreferences.daily_time_minutes,
        notification_enabled: safePreferences.notification_enabled,
        tafsir_id: safePreferences.tafsir_id,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put('/app/settings');
    };

    const handleLogout = () => {
        if (confirm('هل تريد تسجيل الخروج؟')) {
            router.post('/app/logout');
        }
    };

    return (
        <>
            <Head title="الإعدادات" />

            <div className="max-w-2xl mx-auto space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                        الإعدادات
                    </h1>
                    <p className="text-text-muted dark:text-text-dark-muted mt-1">
                        إدارة حسابك وتفضيلاتك
                    </p>
                </div>

                {/* Profile Section */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                <User className="w-5 h-5 text-primary-600 dark:text-primary-400" />
                            </div>
                            <div>
                                <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                    الملف الشخصي
                                </h2>
                                <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                    معلومات حسابك
                                </p>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <Input
                            label="الاسم"
                            icon={User}
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                        />
                        <div>
                            <label className="block text-sm font-medium text-text-primary dark:text-text-dark-primary mb-1.5">
                                البريد الإلكتروني
                            </label>
                            <div className="flex items-center gap-3 px-4 py-3 rounded-xl bg-surface-100 dark:bg-dark-300">
                                <Mail className="w-5 h-5 text-text-muted" />
                                <span className="text-text-primary dark:text-text-dark-primary" dir="ltr">
                                    {safeUser.email}
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Preferences Section */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-xl bg-accent-100 dark:bg-accent-900/30 flex items-center justify-center">
                                <Gauge className="w-5 h-5 text-accent-600 dark:text-accent-400" />
                            </div>
                            <div>
                                <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                    تفضيلات الحفظ
                                </h2>
                                <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                    خصص تجربة الحفظ
                                </p>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent className="space-y-6">
                        {/* Level */}
                        <div>
                            <label className="block text-sm font-medium text-text-primary dark:text-text-dark-primary mb-3">
                                مستوى الحفظ
                            </label>
                            <div className="grid grid-cols-3 gap-3">
                                {levels.map((level) => (
                                    <button
                                        key={level.value}
                                        onClick={() => setData('hifz_level', level.value)}
                                        className={`
                                            p-3 rounded-xl border-2 text-center transition-all
                                            ${data.hifz_level === level.value
                                                ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                                : 'border-surface-300 dark:border-dark-300 hover:border-primary-300'
                                            }
                                        `}
                                    >
                                        <span className={`font-medium ${data.hifz_level === level.value
                                            ? 'text-primary-600 dark:text-primary-400'
                                            : 'text-text-primary dark:text-text-dark-primary'
                                            }`}>
                                            {level.label}
                                        </span>
                                    </button>
                                ))}
                            </div>
                        </div>

                        {/* Daily Time */}
                        <div>
                            <label className="block text-sm font-medium text-text-primary dark:text-text-dark-primary mb-3">
                                <Clock className="w-4 h-4 inline ml-2" />
                                الوقت اليومي للحفظ
                            </label>
                            <div className="flex flex-wrap gap-2">
                                {timeOptions.map((time) => (
                                    <button
                                        key={time}
                                        onClick={() => setData('daily_time_minutes', time)}
                                        className={`
                                            px-4 py-2 rounded-xl transition-all
                                            ${data.daily_time_minutes === time
                                                ? 'bg-primary-500 text-white'
                                                : 'bg-surface-200 dark:bg-dark-300 text-text-primary dark:text-text-dark-primary hover:bg-surface-300 dark:hover:bg-dark-200'
                                            }
                                        `}
                                    >
                                        {time} دقيقة
                                    </button>
                                ))}
                            </div>
                        </div>

                        {/* Tafsir Selection */}
                        {tafsirs && tafsirs.length > 0 && (
                            <div>
                                <label className="block text-sm font-medium text-text-primary dark:text-text-dark-primary mb-3">
                                    التفسير المفضل
                                </label>
                                <select
                                    value={data.tafsir_id}
                                    onChange={(e) => setData('tafsir_id', parseInt(e.target.value))}
                                    className="w-full px-4 py-3 rounded-xl border-2 border-surface-300 dark:border-dark-300 bg-white dark:bg-dark-400 text-text-primary dark:text-text-dark-primary focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                    dir="rtl"
                                >
                                    {tafsirs.map((tafsir) => (
                                        <option key={tafsir.id} value={tafsir.id}>
                                            {tafsir.name}
                                        </option>
                                    ))}
                                </select>
                                <p className="text-xs text-text-muted dark:text-text-dark-muted mt-2">
                                    سيتم استخدام هذا التفسير عند عرض التفسير في جلسات الحفظ
                                </p>
                            </div>
                        )}

                        {/* Notifications */}
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-3">
                                <Bell className="w-5 h-5 text-text-muted" />
                                <div>
                                    <p className="font-medium text-text-primary dark:text-text-dark-primary">
                                        الإشعارات
                                    </p>
                                    <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                        تذكيرات الحفظ والمراجعة
                                    </p>
                                </div>
                            </div>
                            <button
                                onClick={() => setData('notification_enabled', !data.notification_enabled)}
                                className={`
                                    relative w-14 h-8 rounded-full transition-colors
                                    ${data.notification_enabled
                                        ? 'bg-primary-500'
                                        : 'bg-surface-300 dark:bg-dark-300'
                                    }
                                `}
                            >
                                <motion.div
                                    animate={{ x: data.notification_enabled ? 24 : 4 }}
                                    className="absolute top-1 w-6 h-6 bg-white rounded-full shadow"
                                />
                            </button>
                        </div>
                    </CardContent>
                </Card>

                {/* Appearance */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-xl bg-surface-200 dark:bg-dark-300 flex items-center justify-center">
                                <Moon className="w-5 h-5 text-text-muted" />
                            </div>
                            <div>
                                <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                    المظهر
                                </h2>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="font-medium text-text-primary dark:text-text-dark-primary">
                                    الوضع الداكن
                                </p>
                                <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                    تفعيل المظهر الداكن
                                </p>
                            </div>
                            <button
                                type="button"
                                onClick={(e) => {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    toggleTheme();
                                }}
                                className={`
                                    relative w-14 h-8 rounded-full transition-colors cursor-pointer
                                    ${theme === 'dark'
                                        ? 'bg-primary-500'
                                        : 'bg-surface-300 dark:bg-dark-300'
                                    }
                                `}
                                aria-label="تبديل الوضع الداكن"
                            >
                                <motion.div
                                    key={theme}
                                    initial={false}
                                    animate={{ x: theme === 'dark' ? 24 : 4 }}
                                    transition={{ type: 'spring', stiffness: 500, damping: 30 }}
                                    className="absolute top-1 w-6 h-6 bg-white rounded-full shadow-lg"
                                />
                            </button>
                        </div>
                    </CardContent>
                </Card>

                {/* Save Button */}
                <div className="flex gap-4">
                    <Button
                        onClick={handleSubmit}
                        loading={processing}
                        className="flex-1"
                        size="lg"
                        icon={Save}
                    >
                        حفظ التغييرات
                    </Button>
                </div>

                {/* Logout */}
                <Card className="border-error/50">
                    <CardContent className="py-4">
                        <button
                            onClick={handleLogout}
                            className="flex items-center gap-3 w-full text-error hover:bg-error/10 p-3 rounded-xl transition-colors"
                        >
                            <LogOut className="w-5 h-5" />
                            <span className="font-medium">تسجيل الخروج</span>
                        </button>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

export default function SettingsIndex(props) {
    return (
        <MainLayout title="الإعدادات">
            <SettingsContent {...props} />
        </MainLayout>
    );
}
