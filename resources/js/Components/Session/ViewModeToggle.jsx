import { motion } from 'framer-motion';
import { BookOpen, List, FileText } from 'lucide-react';

const VIEW_MODES = [
    { id: 'single', icon: BookOpen, title: 'آية واحدة' },
    { id: 'all', icon: List, title: 'جميع الآيات' },
    { id: 'reading', icon: FileText, title: 'وضع القراءة' },
];

export default function ViewModeToggle({ viewMode, onChange }) {
    return (
        <div className="flex items-center bg-surface-100 dark:bg-dark-300 rounded-xl p-1">
            {VIEW_MODES.map((mode) => {
                const Icon = mode.icon;
                const isActive = viewMode === mode.id;
                
                return (
                    <motion.button
                        key={mode.id}
                        whileHover={{ scale: 1.05 }}
                        whileTap={{ scale: 0.95 }}
                        onClick={() => onChange(mode.id)}
                        className={`
                            p-2 rounded-lg transition-all relative
                            ${isActive 
                                ? 'bg-white dark:bg-dark-400 shadow-sm text-primary-600 dark:text-primary-400' 
                                : 'text-text-muted dark:text-text-dark-muted hover:text-text-primary dark:hover:text-text-dark-primary'
                            }
                        `}
                        title={mode.title}
                    >
                        <Icon className="w-5 h-5" />
                    </motion.button>
                );
            })}
        </div>
    );
}
