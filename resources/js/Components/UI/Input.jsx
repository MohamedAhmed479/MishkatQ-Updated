import { forwardRef, useState } from 'react';
import { motion } from 'framer-motion';
import { Eye, EyeOff } from 'lucide-react';

const Input = forwardRef(({
    label,
    error,
    type = 'text',
    icon: Icon,
    className = '',
    ...props
}, ref) => {
    const [showPassword, setShowPassword] = useState(false);
    const isPassword = type === 'password';
    const inputType = isPassword && showPassword ? 'text' : type;

    return (
        <div className="space-y-1.5">
            {label && (
                <label className="block text-sm font-medium text-text-primary dark:text-text-dark-primary">
                    {label}
                </label>
            )}
            <div className="relative">
                {Icon && (
                    <div className="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                        <Icon className="w-5 h-5 text-text-muted dark:text-text-dark-muted" />
                    </div>
                )}
                <input
                    ref={ref}
                    type={inputType}
                    className={`
                        w-full px-4 py-3 rounded-xl
                        bg-white dark:bg-dark-300
                        border-2 border-surface-300 dark:border-dark-200
                        text-text-primary dark:text-text-dark-primary
                        placeholder:text-text-muted dark:placeholder:text-text-dark-muted
                        focus:outline-none focus:border-primary-500 dark:focus:border-primary-400
                        transition-colors duration-200
                        ${Icon ? 'pr-11' : ''}
                        ${isPassword ? 'pl-11' : ''}
                        ${error ? 'border-error' : ''}
                        ${className}
                    `}
                    {...props}
                />
                {isPassword && (
                    <button
                        type="button"
                        onClick={() => setShowPassword(!showPassword)}
                        className="absolute left-3 top-1/2 -translate-y-1/2 text-text-muted dark:text-text-dark-muted hover:text-text-primary dark:hover:text-text-dark-primary transition-colors"
                    >
                        {showPassword ? (
                            <EyeOff className="w-5 h-5" />
                        ) : (
                            <Eye className="w-5 h-5" />
                        )}
                    </button>
                )}
            </div>
            {error && (
                <motion.p
                    initial={{ opacity: 0, y: -5 }}
                    animate={{ opacity: 1, y: 0 }}
                    className="text-sm text-error"
                >
                    {error}
                </motion.p>
            )}
        </div>
    );
});

Input.displayName = 'Input';

export default Input;
