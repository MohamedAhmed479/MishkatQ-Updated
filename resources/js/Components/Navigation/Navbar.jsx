import { Link, router } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    Menu,
    Bell,
    User,
    LogOut,
    Settings
} from 'lucide-react';
import { useState, useEffect } from 'react';

export default function Navbar({ user, onMenuClick }) {
    const [dropdownOpen, setDropdownOpen] = useState(false);
    const [notificationsOpen, setNotificationsOpen] = useState(false);
    const [notifications, setNotifications] = useState([]);
    const [loadingNotifications, setLoadingNotifications] = useState(false);

    useEffect(() => {
        // Close dropdowns when clicking outside
        const handleClickOutside = (event) => {
            if (notificationsOpen && !event.target.closest('.notifications-dropdown')) {
                setNotificationsOpen(false);
            }
            if (dropdownOpen && !event.target.closest('.user-dropdown')) {
                setDropdownOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, [notificationsOpen, dropdownOpen]);

    const fetchNotifications = async () => {
        if (loadingNotifications || notifications.length > 0) return;
        
        setLoadingNotifications(true);
        try {
            const response = await fetch('/app/notifications/api', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            
            if (response.ok) {
                const data = await response.json();
                setNotifications(data.notifications || []);
            }
        } catch (error) {
            console.error('Error fetching notifications:', error);
        } finally {
            setLoadingNotifications(false);
        }
    };

    const handleNotificationsClick = () => {
        setNotificationsOpen(!notificationsOpen);
        if (!notificationsOpen && notifications.length === 0) {
            fetchNotifications();
        }
    };

    const unreadCount = notifications.filter(n => !n.read_at).length;

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
                    {user ? (
                        <>
                            {/* Notifications */}
                            <div className="relative notifications-dropdown">
                                <button 
                                    onClick={handleNotificationsClick}
                                    className="p-2 rounded-xl hover:bg-surface-200 dark:hover:bg-dark-300 transition-colors relative"
                                >
                                    <Bell className="w-5 h-5 text-text-secondary dark:text-text-dark-secondary" />
                                    {unreadCount > 0 && (
                                        <span className="absolute top-1 left-1 w-2 h-2 bg-error rounded-full"></span>
                                    )}
                                </button>

                                <AnimatePresence>
                                    {notificationsOpen && (
                                        <motion.div
                                            initial={{ opacity: 0, y: 10, scale: 0.95 }}
                                            animate={{ opacity: 1, y: 0, scale: 1 }}
                                            exit={{ opacity: 0, y: 10, scale: 0.95 }}
                                            className="absolute left-0 mt-2 w-80 bg-white dark:bg-dark-400 rounded-2xl shadow-xl border border-surface-300 dark:border-dark-300 overflow-hidden z-50"
                                        >
                                            <div className="p-4 border-b border-surface-200 dark:border-dark-300">
                                                <h3 className="font-bold text-text-primary dark:text-text-dark-primary">
                                                    الإشعارات
                                                </h3>
                                            </div>
                                            
                                            <div className="max-h-96 overflow-y-auto">
                                                {loadingNotifications ? (
                                                    <div className="p-8 text-center text-text-muted dark:text-text-dark-muted">
                                                        جاري التحميل...
                                                    </div>
                                                ) : notifications.length > 0 ? (
                                                    notifications.slice(0, 10).map((notification) => (
                                                        <div
                                                            key={notification.id}
                                                            className={`
                                                                p-4 border-b border-surface-200 dark:border-dark-300 
                                                                ${!notification.read_at ? 'bg-primary-50/50 dark:bg-primary-900/10' : ''}
                                                                hover:bg-surface-100 dark:hover:bg-dark-300 transition-colors cursor-pointer
                                                            `}
                                                            onClick={() => {
                                                                // Mark as read
                                                                if (!notification.read_at) {
                                                                    fetch(`/app/notifications/${notification.id}/read`, {
                                                                        method: 'PATCH',
                                                                        headers: {
                                                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                                                            'Accept': 'application/json',
                                                                        },
                                                                    }).then(() => {
                                                                        setNotifications(notifications.map(n => 
                                                                            n.id === notification.id ? {...n, read_at: new Date().toISOString()} : n
                                                                        ));
                                                                    }).catch(err => console.error('Error marking notification as read:', err));
                                                                }
                                                            }}
                                                        >
                                                            <p className="text-sm font-medium text-text-primary dark:text-text-dark-primary mb-1">
                                                                {notification.data?.title || 'إشعار جديد'}
                                                            </p>
                                                            <p className="text-xs text-text-muted dark:text-text-dark-muted">
                                                                {notification.data?.message || ''}
                                                            </p>
                                                            <p className="text-xs text-text-muted dark:text-text-dark-muted mt-2">
                                                                {new Date(notification.created_at).toLocaleDateString('ar-EG', {
                                                                    year: 'numeric',
                                                                    month: 'short',
                                                                    day: 'numeric',
                                                                    hour: '2-digit',
                                                                    minute: '2-digit'
                                                                })}
                                                            </p>
                                                        </div>
                                                    ))
                                                ) : (
                                                    <div className="p-8 text-center text-text-muted dark:text-text-dark-muted">
                                                        لا توجد إشعارات
                                                    </div>
                                                )}
                                            </div>

                                            {notifications.length > 0 && (
                                                <div className="p-4 border-t border-surface-200 dark:border-dark-300">
                                                    <Link
                                                        href="/app/notifications"
                                                        className="text-sm text-primary-600 dark:text-primary-400 hover:underline block text-center"
                                                        onClick={() => setNotificationsOpen(false)}
                                                    >
                                                        عرض جميع الإشعارات
                                                    </Link>
                                                </div>
                                            )}
                                        </motion.div>
                                    )}
                                </AnimatePresence>
                            </div>

                            {/* User Menu */}
                            <div className="relative user-dropdown">
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
