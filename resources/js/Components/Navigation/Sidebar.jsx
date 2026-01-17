import { Link, usePage } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    Home,
    BookOpen,
    RefreshCw,
    Trophy,
    BarChart3,
    Award,
    Settings,
    X,
    ChevronLeft
} from 'lucide-react';

const navItems = [
    { name: 'الرئيسية', href: '/app/dashboard', icon: Home },
    { name: 'خططي', href: '/app/plans', icon: BookOpen },
    { name: 'المراجعات', href: '/app/revisions', icon: RefreshCw },
    { name: 'تصفح القرآن', href: '/app/quran', icon: BookOpen },
    { name: 'الإنجازات', href: '/app/achievements', icon: Award },
    { name: 'لوحة الصدارة', href: '/app/leaderboard', icon: Trophy },
    { name: 'التحليلات', href: '/app/analytics', icon: BarChart3 },
    { name: 'الإعدادات', href: '/app/settings', icon: Settings },
];

export default function Sidebar({ isOpen, onClose, user }) {
    const { url } = usePage();

    if (!user) return null;

    return (
        <>
            {/* Mobile Overlay */}
            <AnimatePresence>
                {isOpen && (
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        onClick={onClose}
                        className="fixed inset-0 bg-black/50 z-40 md:hidden"
                    />
                )}
            </AnimatePresence>

            {/* Sidebar */}
            <aside
                className={`
                    fixed top-16 right-0 h-[calc(100vh-4rem)] w-64 
                    bg-white dark:bg-dark-400 border-l border-surface-300 dark:border-dark-300
                    transform transition-transform duration-300 z-50
                    ${isOpen ? 'translate-x-0' : 'translate-x-full md:translate-x-0'}
                    md:translate-x-0
                `}
            >
                {/* Close button - Mobile */}
                <button
                    onClick={onClose}
                    className="absolute top-4 left-4 p-2 rounded-xl hover:bg-surface-200 dark:hover:bg-dark-300 transition-colors md:hidden"
                >
                    <X className="w-5 h-5" />
                </button>

                {/* User Info Card */}
                <div className="p-4 mt-2">
                    <div className="bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl p-4 text-white">
                        <div className="flex items-center gap-3">
                            <div className="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                                <span className="text-xl font-bold">
                                    {user.name?.charAt(0) || 'م'}
                                </span>
                            </div>
                            <div>
                                <h3 className="font-bold">{user.name}</h3>
                                <p className="text-sm text-white/80">المستوى: متوسط</p>
                            </div>
                        </div>
                        <div className="mt-4 flex items-center justify-between text-sm">
                            <span>النقاط</span>
                            <span className="font-bold text-accent-300">0</span>
                        </div>
                        <div className="mt-2 h-2 bg-white/20 rounded-full overflow-hidden">
                            <div className="h-full w-0 bg-accent-400 rounded-full transition-all duration-500"></div>
                        </div>
                    </div>
                </div>

                {/* Navigation */}
                <nav className="px-3 mt-4 space-y-1">
                    {navItems.map((item) => {
                        const isActive = url.startsWith(item.href);
                        const Icon = item.icon;

                        return (
                            <Link
                                key={item.name}
                                href={item.href}
                                onClick={onClose}
                                className={`
                                    flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200
                                    ${isActive
                                        ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 font-medium'
                                        : 'text-text-secondary dark:text-text-dark-secondary hover:bg-surface-100 dark:hover:bg-dark-300'
                                    }
                                `}
                            >
                                <Icon className="w-5 h-5" />
                                <span>{item.name}</span>
                                {isActive && (
                                    <ChevronLeft className="w-4 h-4 mr-auto" />
                                )}
                            </Link>
                        );
                    })}
                </nav>

                {/* Quick Stats */}
                <div className="absolute bottom-4 left-4 right-4">
                    <div className="bg-surface-100 dark:bg-dark-300 rounded-2xl p-4">
                        <h4 className="text-sm font-medium text-text-muted dark:text-text-dark-muted mb-3">
                            إحصائيات سريعة
                        </h4>
                        <div className="grid grid-cols-2 gap-3">
                            <div className="text-center">
                                <p className="text-2xl font-bold text-primary-600 dark:text-primary-400">0</p>
                                <p className="text-xs text-text-muted dark:text-text-dark-muted">آية محفوظة</p>
                            </div>
                            <div className="text-center">
                                <p className="text-2xl font-bold text-accent-500">0</p>
                                <p className="text-xs text-text-muted dark:text-text-dark-muted">يوم متتالي</p>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </>
    );
}
