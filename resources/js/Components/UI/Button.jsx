import { motion } from 'framer-motion';
import { Loader2 } from 'lucide-react';

const variants = {
    primary: 'bg-primary-600 hover:bg-primary-700 text-white shadow-lg shadow-primary-500/25',
    secondary: 'bg-surface-200 dark:bg-dark-300 hover:bg-surface-300 dark:hover:bg-dark-200 text-text-primary dark:text-text-dark-primary',
    accent: 'bg-accent-500 hover:bg-accent-600 text-white shadow-lg shadow-accent-500/25',
    outline: 'border-2 border-primary-500 text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20',
    ghost: 'hover:bg-surface-100 dark:hover:bg-dark-300 text-text-secondary dark:text-text-dark-secondary',
    danger: 'bg-error hover:bg-red-600 text-white shadow-lg shadow-red-500/25',
};

const sizes = {
    sm: 'px-3 py-1.5 text-sm rounded-lg',
    md: 'px-4 py-2.5 text-base rounded-xl',
    lg: 'px-6 py-3 text-lg rounded-xl',
    xl: 'px-8 py-4 text-xl rounded-2xl',
};

export default function Button({
    children,
    variant = 'primary',
    size = 'md',
    loading = false,
    disabled = false,
    className = '',
    icon: Icon,
    iconPosition = 'right',
    ...props
}) {
    return (
        <motion.button
            whileHover={{ scale: disabled ? 1 : 1.02 }}
            whileTap={{ scale: disabled ? 1 : 0.98 }}
            disabled={disabled || loading}
            className={`
                inline-flex items-center justify-center gap-2 font-medium transition-all duration-200
                disabled:opacity-50 disabled:cursor-not-allowed
                ${variants[variant]}
                ${sizes[size]}
                ${className}
            `}
            {...props}
        >
            {loading && <Loader2 className="w-4 h-4 animate-spin" />}
            {!loading && Icon && iconPosition === 'right' && <Icon className="w-4 h-4" />}
            {children}
            {!loading && Icon && iconPosition === 'left' && <Icon className="w-4 h-4" />}
        </motion.button>
    );
}
