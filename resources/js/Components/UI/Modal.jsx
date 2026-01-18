import { motion, AnimatePresence } from 'framer-motion';
import { X } from 'lucide-react';

export default function Modal({ isOpen, onClose, title, children, size = 'lg' }) {
    const sizeClasses = {
        sm: 'max-w-md',
        md: 'max-w-lg',
        lg: 'max-w-2xl',
        xl: 'max-w-4xl',
        '2xl': 'max-w-6xl',
    };

    if (!isOpen) return null;

    return (
        <AnimatePresence>
            <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                className="fixed inset-0 z-50 flex items-center justify-center p-4"
            >
                {/* Backdrop */}
                <motion.div
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    exit={{ opacity: 0 }}
                    className="absolute inset-0 bg-black/60 backdrop-blur-sm"
                    onClick={onClose}
                />

                {/* Modal Content */}
                <motion.div
                    initial={{ opacity: 0, scale: 0.95, y: 20 }}
                    animate={{ opacity: 1, scale: 1, y: 0 }}
                    exit={{ opacity: 0, scale: 0.95, y: 20 }}
                    transition={{ type: "spring", duration: 0.3 }}
                    className={`
                        relative bg-white dark:bg-dark-400 
                        rounded-2xl shadow-2xl
                        w-full ${sizeClasses[size]}
                        max-h-[90vh] overflow-hidden
                        flex flex-col
                    `}
                    onClick={(e) => e.stopPropagation()}
                >
                    {/* Header */}
                    {title && (
                        <div className="flex items-center justify-between px-6 py-4 border-b border-surface-200 dark:border-dark-300">
                            <h3 className="text-xl font-bold text-text-primary dark:text-text-dark-primary">
                                {title}
                            </h3>
                            <button
                                onClick={onClose}
                                className="p-2 rounded-lg hover:bg-surface-100 dark:hover:bg-dark-300 text-text-muted dark:text-text-dark-muted hover:text-text-primary dark:hover:text-text-dark-primary transition-colors"
                            >
                                <X className="w-5 h-5" />
                            </button>
                        </div>
                    )}

                    {/* Content */}
                    <div className="overflow-y-auto flex-1 px-6 py-4">
                        {children}
                    </div>
                </motion.div>
            </motion.div>
        </AnimatePresence>
    );
}
