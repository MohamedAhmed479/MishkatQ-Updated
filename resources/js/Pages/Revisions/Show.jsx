import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    X,
    Eye,
    EyeOff,
    RefreshCw,
    Star,
    Check
} from 'lucide-react';
import { ThemeProvider, useTheme } from '@/Contexts/ThemeContext';
import Button from '@/Components/UI/Button';

function RevisionContent({ revision, verses }) {
    const { theme } = useTheme();
    const [showText, setShowText] = useState(false);
    const [completed, setCompleted] = useState(false);
    const [rating, setRating] = useState(null);

    const handleComplete = () => {
        router.post(`/api/v1/revision-reviews/${revision.id}/record`, {
            quality_rating: rating,
        }, {
            preserveScroll: true,
            onSuccess: () => router.visit('/app/revisions'),
        });
    };

    const handleClose = () => {
        if (confirm('هل تريد الخروج من جلسة المراجعة؟')) {
            router.visit('/app/revisions');
        }
    };

    return (
        <div className="min-h-screen bg-surface-50 dark:bg-dark-500 flex flex-col">
            {/* Header */}
            <header className="sticky top-0 z-40 bg-white/90 dark:bg-dark-400/90 backdrop-blur-xl border-b border-surface-300 dark:border-dark-300">
                <div className="px-4 py-3 flex items-center justify-between">
                    <button
                        onClick={handleClose}
                        className="p-2 rounded-xl hover:bg-surface-200 dark:hover:bg-dark-300 transition-colors"
                    >
                        <X className="w-6 h-6 text-text-primary dark:text-text-dark-primary" />
                    </button>

                    <div className="text-center">
                        <div className="flex items-center gap-2 justify-center">
                            <RefreshCw className="w-4 h-4 text-accent-500" />
                            <h1 className="font-bold text-text-primary dark:text-text-dark-primary">
                                مراجعة سورة {revision.chapter_name}
                            </h1>
                        </div>
                        <p className="text-xs text-text-muted dark:text-text-dark-muted">
                            المراجعة رقم {revision.repetition_number}
                        </p>
                    </div>

                    <button
                        onClick={() => setShowText(!showText)}
                        className="p-2 rounded-xl hover:bg-surface-200 dark:hover:bg-dark-300 transition-colors"
                    >
                        {showText ? (
                            <EyeOff className="w-6 h-6 text-text-primary dark:text-text-dark-primary" />
                        ) : (
                            <Eye className="w-6 h-6 text-text-primary dark:text-text-dark-primary" />
                        )}
                    </button>
                </div>
            </header>

            {/* Main Content */}
            <main className="flex-1 flex flex-col items-center justify-center p-6">
                <AnimatePresence mode="wait">
                    {!completed ? (
                        <motion.div
                            key="reviewing"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="w-full max-w-3xl"
                        >
                            {/* Instructions */}
                            <div className="text-center mb-8">
                                <p className="text-text-muted dark:text-text-dark-muted">
                                    حاول تذكر الآيات ثم اضغط على "إظهار النص" للتحقق
                                </p>
                            </div>

                            {/* Verses Container */}
                            <div className="bg-white dark:bg-dark-400 rounded-3xl p-6 md:p-8 shadow-lg">
                                <div className="mb-4 text-center">
                                    <span className="inline-flex items-center gap-2 text-sm text-text-muted dark:text-text-dark-muted">
                                        <span>الآيات {revision.start_verse} - {revision.end_verse}</span>
                                    </span>
                                </div>

                                <motion.div
                                    animate={{ filter: showText ? 'blur(0px)' : 'blur(8px)' }}
                                    className="space-y-4"
                                >
                                    {verses.map((verse) => (
                                        <div
                                            key={verse.id}
                                            className="flex items-start gap-4"
                                        >
                                            <span className="shrink-0 w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-sm font-bold text-primary-600 dark:text-primary-400">
                                                {verse.verse_number}
                                            </span>
                                            <p className="font-amiri text-2xl md:text-3xl leading-loose text-text-primary dark:text-text-dark-primary">
                                                {verse.text}
                                            </p>
                                        </div>
                                    ))}
                                </motion.div>

                                {!showText && (
                                    <div className="mt-8 text-center">
                                        <Button
                                            onClick={() => setShowText(true)}
                                            variant="outline"
                                            icon={Eye}
                                        >
                                            إظهار النص
                                        </Button>
                                    </div>
                                )}
                            </div>

                            {/* Complete Button */}
                            {showText && (
                                <motion.div
                                    initial={{ opacity: 0, y: 20 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    className="mt-8 text-center"
                                >
                                    <Button
                                        onClick={() => setCompleted(true)}
                                        size="lg"
                                        icon={Check}
                                    >
                                        انتهيت من المراجعة
                                    </Button>
                                </motion.div>
                            )}
                        </motion.div>
                    ) : (
                        <motion.div
                            key="rating"
                            initial={{ opacity: 0, scale: 0.9 }}
                            animate={{ opacity: 1, scale: 1 }}
                            className="w-full max-w-md text-center"
                        >
                            <div className="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-accent-400 to-accent-600 rounded-full flex items-center justify-center">
                                <RefreshCw className="w-12 h-12 text-white" />
                            </div>
                            <h2 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary mb-2">
                                أحسنت! 🎉
                            </h2>
                            <p className="text-text-muted dark:text-text-dark-muted mb-8">
                                كيف كانت جودة تذكرك؟
                            </p>

                            {/* Rating Stars */}
                            <div className="flex justify-center gap-3 mb-8">
                                {[1, 2, 3, 4, 5].map((r) => (
                                    <motion.button
                                        key={r}
                                        whileHover={{ scale: 1.2 }}
                                        whileTap={{ scale: 0.9 }}
                                        onClick={() => setRating(r)}
                                        className="transition-colors"
                                    >
                                        <Star
                                            className={`w-10 h-10 ${rating >= r
                                                ? 'fill-accent-400 text-accent-400'
                                                : 'text-surface-300 dark:text-dark-200'
                                                }`}
                                        />
                                    </motion.button>
                                ))}
                            </div>

                            <div className="space-y-2 mb-8">
                                <p className="text-xs text-text-muted dark:text-text-dark-muted">
                                    {rating === 1 && 'لم أتذكر شيئاً - سأراجع أكثر'}
                                    {rating === 2 && 'تذكرت قليلاً مع صعوبة'}
                                    {rating === 3 && 'تذكرت مع بعض الأخطاء'}
                                    {rating === 4 && 'تذكرت جيداً مع أخطاء بسيطة'}
                                    {rating === 5 && 'تذكرت بشكل ممتاز!'}
                                </p>
                            </div>

                            <Button
                                onClick={handleComplete}
                                disabled={!rating}
                                className="w-full"
                                size="lg"
                                icon={Check}
                            >
                                حفظ التقييم
                            </Button>
                        </motion.div>
                    )}
                </AnimatePresence>
            </main>
        </div>
    );
}

export default function RevisionShow({ revision, verses }) {
    return (
        <ThemeProvider>
            <Head title={`مراجعة سورة ${revision.chapter_name}`} />
            <RevisionContent revision={revision} verses={verses} />
        </ThemeProvider>
    );
}
