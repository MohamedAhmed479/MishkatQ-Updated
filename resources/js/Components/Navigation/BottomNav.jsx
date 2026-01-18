import { Link, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    Home,
    BookOpen,
    BookMarked,
    RefreshCw,
    Trophy,
    User
} from 'lucide-react';

const navItems = [
    { name: 'الرئيسية', href: '/app/dashboard', icon: Home },
    { name: 'الورد', href: '/app/reading', icon: BookMarked },
    { name: 'المراجعات', href: '/app/revisions', icon: RefreshCw },
    { name: 'الإنجازات', href: '/app/achievements', icon: Trophy },
    { name: 'حسابي', href: '/app/settings', icon: User },
];

export default function BottomNav({ user }) {
    const { url } = usePage();

    if (!user) return null;

    return (
        <nav className="fixed bottom-0 left-0 right-0 h-16 bg-white/90 dark:bg-dark-400/90 backdrop-blur-xl border-t border-surface-300 dark:border-dark-300 md:hidden z-40">
            <div className="h-full flex items-center justify-around px-2">
                {navItems.map((item) => {
                    const isActive = url.startsWith(item.href);
                    const Icon = item.icon;

                    return (
                        <Link
                            key={item.name}
                            href={item.href}
                            className="relative flex flex-col items-center gap-1 py-2 px-3"
                        >
                            {isActive && (
                                <motion.div
                                    layoutId="bottomNavIndicator"
                                    className="absolute -top-1 left-1/2 -translate-x-1/2 w-8 h-1 bg-primary-500 rounded-full"
                                    transition={{ type: "spring", stiffness: 500, damping: 30 }}
                                />
                            )}
                            <Icon
                                className={`w-5 h-5 transition-colors ${isActive
                                    ? 'text-primary-600 dark:text-primary-400'
                                    : 'text-text-muted dark:text-text-dark-muted'
                                    }`}
                            />
                            <span
                                className={`text-xs transition-colors ${isActive
                                    ? 'text-primary-600 dark:text-primary-400 font-medium'
                                    : 'text-text-muted dark:text-text-dark-muted'
                                    }`}
                            >
                                {item.name}
                            </span>
                        </Link>
                    );
                })}
            </div>
        </nav>
    );
}
