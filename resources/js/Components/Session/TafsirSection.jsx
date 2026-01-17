import { motion, AnimatePresence } from 'framer-motion';
import { BookOpen, ChevronDown } from 'lucide-react';

export default function TafsirSection({ tafsir, isOpen, onToggle }) {
    return (
        <div className="mt-4">
            <button
                onClick={onToggle}
                className="flex items-center gap-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors"
            >
                <BookOpen className="w-4 h-4" />
                <span>التفسير</span>
                <motion.div
                    animate={{ rotate: isOpen ? 180 : 0 }}
                    transition={{ duration: 0.2 }}
                >
                    <ChevronDown className="w-4 h-4" />
                </motion.div>
            </button>

            <AnimatePresence>
                {isOpen && (
                    <motion.div
                        initial={{ height: 0, opacity: 0 }}
                        animate={{ height: 'auto', opacity: 1 }}
                        exit={{ height: 0, opacity: 0 }}
                        transition={{ duration: 0.3, ease: 'easeInOut' }}
                        className="overflow-hidden"
                    >
                        <div className="mt-3 p-4 bg-surface-100 dark:bg-dark-300 rounded-xl border border-surface-200 dark:border-dark-200">
                            {tafsir ? (
                                <p className="text-sm md:text-base leading-relaxed text-text-secondary dark:text-text-dark-secondary text-right" dir="rtl">
                                    {tafsir}
                                </p>
                            ) : (
                                <div className="flex flex-col items-center gap-2 py-2">
                                    <BookOpen className="w-8 h-8 text-text-muted dark:text-text-dark-muted opacity-50" />
                                    <p className="text-sm text-text-muted dark:text-text-dark-muted text-center">
                                        التفسير غير متاح لهذه الآية
                                    </p>
                                </div>
                            )}
                        </div>
                    </motion.div>
                )}
            </AnimatePresence>
        </div>
    );
}
