import { Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    Menu,
    Sun,
    Moon,
    Bell,
    User,
    LogOut,
    Settings
} from 'lucide-react';
import { useTheme } from '@/Contexts/ThemeContext';
import { useState } from 'react';

export default function Navbar({ user, onMenuClick }) {
    const { theme, toggleTheme } = useTheme();
    const [dropdownOpen, setDropdownOpen] = useState(false);

    return (
        <nav className="sticky top-0 z-40 h-16 bg-white/80 dark:bg-dark-400/80 backdrop-blur-xl border-b border-surface-300 dark:border-dark-300">
            <div className="h-full px-4 flex items-center justify-between">
                {/* Right Side - Logo & Menu */}
                <div className="flex items-center gap-4">
                    {user && (
                        <button
                            onClick={onMenuClick}
                            className="md:hidden p-2 rounded-xl hover:bg-surface-200 dark:hover:bg-dark-300 transition-colors"
                            aria-label="القائمة"
                        >
                            <Menu className="w-6 h-6 text-text-primary dark:text-text-dark-primary" />
                        </button>
                    )}

                    <Link href="/" className="flex items-center gap-3">
                        <motion.img
                            src="/images/logo.svg"
                            alt="مشكاة"
                            className="w-10 h-10"
                            whileHover={{ rotate: 10 }}
                            transition={{ type: "spring", stiffness: 300 }}
                        />
                        <span className="text-xl font-bold text-primary-700 dark:text-primary-400 hidden sm:block">
                            مشكاة
                        </span>
                    </Link>
                </div>

                {/* Left Side - Actions */}
                <div className="flex items-center gap-2">
                    {/* Theme Toggle */}
                    <motion.button
                        onClick={toggleTheme}
                        className="p-2 rounded-xl hover:bg-surface-200 dark:hover:bg-dark-300 transition-colors"
                        whileTap={{ scale: 0.95 }}
                        aria-label={theme === 'dark' ? 'الوضع الفاتح' : 'الوضع الداكن'}
                    >
                        {theme === 'dark' ? (
                            <Sun className="w-5 h-5 text-accent-400" />
                        ) : (
                            <Moon className="w-5 h-5 text-primary-600" />
                        )}
                    </motion.button>

                    {user ? (
                        <>
                            {/* Notifications */}
                            <button className="p-2 rounded-xl hover:bg-surface-200 dark:hover:bg-dark-300 transition-colors relative">
                                <Bell className="w-5 h-5 text-text-secondary dark:text-text-dark-secondary" />
                                <span className="absolute top-1 left-1 w-2 h-2 bg-error rounded-full"></span>
                            </button>

                            {/* User Menu */}
                            <div className="relative">
                                <button
                                    onClick={() => setDropdownOpen(!dropdownOpen)}
                                    className="flex items-center gap-2 p-2 rounded-xl hover:bg-surface-200 dark:hover:bg-dark-300 transition-colors"
                                >
                                    <div className="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center">
                                        <span className="text-white text-sm font-bold">
                                            {user.name?.charAt(0) || 'م'}
                                        </span>
                                    </div>
                                    <span className="hidden md:block text-sm font-medium text-text-primary dark:text-text-dark-primary">
                                        {user.name}
                                    </span>
                                </button>

                                {dropdownOpen && (
                                    <motion.div
                                        initial={{ opacity: 0, y: 10 }}
                                        animate={{ opacity: 1, y: 0 }}
                                        exit={{ opacity: 0, y: 10 }}
                                        className="absolute left-0 mt-2 w-48 bg-white dark:bg-dark-400 rounded-2xl shadow-xl border border-surface-300 dark:border-dark-300 overflow-hidden"
                                    >
                                        <Link
                                            href="/app/settings"
                                            className="flex items-center gap-3 px-4 py-3 hover:bg-surface-100 dark:hover:bg-dark-300 transition-colors"
                                        >
                                            <Settings className="w-4 h-4" />
                                            <span>الإعدادات</span>
                                        </Link>
                                        <Link
                                            href="/logout"
                                            method="post"
                                            as="button"
                                            className="w-full flex items-center gap-3 px-4 py-3 hover:bg-surface-100 dark:hover:bg-dark-300 transition-colors text-error"
                                        >
                                            <LogOut className="w-4 h-4" />
                                            <span>تسجيل الخروج</span>
                                        </Link>
                                    </motion.div>
                                )}
                            </div>
                        </>
                    ) : (
                        <div className="flex items-center gap-2">
                            <Link
                                href="/login"
                                className="px-4 py-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:bg-surface-200 dark:hover:bg-dark-300 rounded-xl transition-colors"
                            >
                                تسجيل الدخول
                            </Link>
                            <Link
                                href="/register"
                                className="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-xl transition-colors"
                            >
                                إنشاء حساب
                            </Link>
                        </div>
                    )}
                </div>
            </div>
        </nav>
    );
}
