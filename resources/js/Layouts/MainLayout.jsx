import { useState } from 'react';
import { usePage, Link } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import { ThemeProvider } from '@/Contexts/ThemeContext';
import Navbar from '@/Components/Navigation/Navbar';
import Sidebar from '@/Components/Navigation/Sidebar';
import BottomNav from '@/Components/Navigation/BottomNav';

export default function MainLayout({ children, title }) {
    const { auth } = usePage().props;
    const [sidebarOpen, setSidebarOpen] = useState(false);

    return (
        <ThemeProvider>
            <div className="min-h-screen bg-surface-50 dark:bg-dark-500 transition-colors duration-300">
                {/* Navbar - Desktop */}
                <Navbar
                    user={auth?.user}
                    onMenuClick={() => setSidebarOpen(!sidebarOpen)}
                />

                <div className="flex">
                    {/* Sidebar - Desktop */}
                    <Sidebar
                        isOpen={sidebarOpen}
                        onClose={() => setSidebarOpen(false)}
                        user={auth?.user}
                    />

                    {/* Main Content */}
                    <main className="flex-1 min-h-[calc(100vh-4rem)] pb-20 md:pb-8 md:mr-64">
                        <AnimatePresence mode="wait">
                            <motion.div
                                key={title}
                                initial={{ opacity: 0, y: 20 }}
                                animate={{ opacity: 1, y: 0 }}
                                exit={{ opacity: 0, y: -20 }}
                                transition={{ duration: 0.3 }}
                                className="container mx-auto px-4 py-6"
                            >
                                {children}
                            </motion.div>
                        </AnimatePresence>
                    </main>
                </div>

                {/* Bottom Navigation - Mobile */}
                <BottomNav user={auth?.user} />
            </div>
        </ThemeProvider>
    );
}
