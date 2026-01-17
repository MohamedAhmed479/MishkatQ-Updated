import { motion } from 'framer-motion';

export default function Card({
    children,
    className = '',
    hover = false,
    gradient = false,
    ...props
}) {
    return (
        <motion.div
            whileHover={hover ? { y: -4, scale: 1.01 } : {}}
            transition={{ type: "spring", stiffness: 300, damping: 20 }}
            className={`
                bg-white dark:bg-dark-400 
                rounded-2xl 
                border border-surface-300 dark:border-dark-300
                ${gradient ? 'bg-gradient-to-br from-primary-500 to-primary-700 border-0 text-white' : ''}
                ${className}
            `}
            {...props}
        >
            {children}
        </motion.div>
    );
}

export function CardHeader({ children, className = '' }) {
    return (
        <div className={`px-6 py-4 border-b border-surface-200 dark:border-dark-300 ${className}`}>
            {children}
        </div>
    );
}

export function CardContent({ children, className = '' }) {
    return (
        <div className={`px-6 py-4 ${className}`}>
            {children}
        </div>
    );
}

export function CardFooter({ children, className = '' }) {
    return (
        <div className={`px-6 py-4 border-t border-surface-200 dark:border-dark-300 ${className}`}>
            {children}
        </div>
    );
}
