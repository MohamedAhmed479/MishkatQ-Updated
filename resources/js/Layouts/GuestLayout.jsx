import { Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ThemeProvider, useTheme } from '@/Contexts/ThemeContext';
import { Sun, Moon } from 'lucide-react';

function GuestLayoutContent({ children, title }) {
    const { theme, toggleTheme } = useTheme();

    return (
        <div className="min-h-screen bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950 flex flex-col">
            {/* Floating Particles Background */}
            <div className="fixed inset-0 overflow-hidden pointer-events-none">
                {[...Array(30)].map((_, i) => (
                    <motion.div
                        key={i}
                        className="absolute w-2 h-2 bg-primary-400/20 rounded-full"
                        initial={{
                            x: Math.random() * (typeof window !== 'undefined' ? window.innerWidth : 1000),
                            y: Math.random() * (typeof window !== 'undefined' ? window.innerHeight : 800),
                        }}
                        animate={{
                            y: [null, -20, 20, -20],
                            x: [null, 10, -10, 10],
                        }}
                        transition={{
                            duration: 8 + Math.random() * 4,
                            repeat: Infinity,
                            delay: Math.random() * 2,
                        }}
                    />
                ))}
            </div>

            {/* Header */}
            <header className="relative z-10 p-4 flex items-center justify-between">
                <Link href="/" className="flex items-center gap-3">
                    <motion.img
                        src="/images/logo.svg"
                        alt="مشكاة"
                        className="w-10 h-10"
                        whileHover={{ rotate: 10 }}
                    />
                    <span className="text-xl font-bold text-white">مشكاة</span>
                </Link>

                <motion.button
                    onClick={toggleTheme}
                    className="p-2 rounded-xl bg-white/10 hover:bg-white/20 transition-colors"
                    whileTap={{ scale: 0.95 }}
                >
                    {theme === 'dark' ? (
                        <Sun className="w-5 h-5 text-accent-400" />
                    ) : (
                        <Moon className="w-5 h-5 text-white" />
                    )}
                </motion.button>
            </header>

            {/* Main Content */}
            <main className="relative z-10 flex-1 flex items-center justify-center p-4">
                <motion.div
                    initial={{ opacity: 0, y: 30 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5 }}
                    className="w-full max-w-md"
                >
                    {children}
                </motion.div>
            </main>

            {/* Footer */}
            <footer className="relative z-10 p-4 text-center text-white/50 text-sm">
                © 2025 مشكاة. جميع الحقوق محفوظة.
            </footer>
        </div>
    );
}

export default function GuestLayout({ children, title }) {
    return (
        <ThemeProvider>
            <GuestLayoutContent title={title}>
                {children}
            </GuestLayoutContent>
        </ThemeProvider>
    );
}
