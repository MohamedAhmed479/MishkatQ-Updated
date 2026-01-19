import { useState, useCallback, useMemo } from 'react';
import { motion, AnimatePresence, Reorder } from 'framer-motion';
import { Check, X, RotateCcw, GripVertical, ArrowUp, ArrowDown } from 'lucide-react';

/**
 * Shuffles an array using Fisher-Yates algorithm
 */
function shuffleArray(array) {
    const shuffled = [...array];
    for (let i = shuffled.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
    }
    return shuffled;
}

export default function VerseOrderingTest({
    verses = [],
    onComplete,
}) {
    // Create verse items with original indices
    const verseItems = useMemo(() => {
        return verses.map((verse, index) => ({
            id: verse.id || index,
            originalIndex: index,
            verseNumber: verse.verse_number,
            text: verse.text_imlaei || verse.text_uthmani || verse.text,
        }));
    }, [verses]);

    // Shuffle verses for the test
    const [orderedItems, setOrderedItems] = useState(() => shuffleArray(verseItems));
    const [isSubmitted, setIsSubmitted] = useState(false);
    const [showResults, setShowResults] = useState(false);

    // Check if the current order is correct
    const checkOrder = useCallback(() => {
        return orderedItems.every((item, index) => item.originalIndex === index);
    }, [orderedItems]);

    // Calculate score
    const calculateScore = useCallback(() => {
        let correctCount = 0;
        orderedItems.forEach((item, index) => {
            if (item.originalIndex === index) {
                correctCount++;
            }
        });
        return Math.round((correctCount / orderedItems.length) * 100);
    }, [orderedItems]);

    const handleSubmit = useCallback(() => {
        setIsSubmitted(true);
        setShowResults(true);
    }, []);

    const handleRetry = useCallback(() => {
        setOrderedItems(shuffleArray(verseItems));
        setIsSubmitted(false);
        setShowResults(false);
    }, [verseItems]);

    const handleComplete = useCallback(() => {
        const score = calculateScore();
        const isCorrect = checkOrder();

        if (onComplete) {
            onComplete({
                testType: 'verse_ordering',
                score,
                passed: score >= 70,
                details: {
                    originalOrder: verseItems.map((v) => v.verseNumber),
                    userOrder: orderedItems.map((v) => v.verseNumber),
                    correct: isCorrect,
                },
            });
        }
    }, [calculateScore, checkOrder, orderedItems, verseItems, onComplete]);

    // Move item up or down (for mobile/accessibility)
    const moveItem = useCallback((index, direction) => {
        if (isSubmitted) return;

        const newIndex = index + direction;
        if (newIndex < 0 || newIndex >= orderedItems.length) return;

        const newItems = [...orderedItems];
        [newItems[index], newItems[newIndex]] = [newItems[newIndex], newItems[index]];
        setOrderedItems(newItems);
    }, [orderedItems, isSubmitted]);

    if (verses.length < 2) {
        return (
            <div className="text-center py-8">
                <p className="text-text-muted dark:text-text-dark-muted">
                    يجب أن يكون هناك آيتان على الأقل لإجراء هذا الاختبار
                </p>
            </div>
        );
    }

    // Results Screen
    if (showResults) {
        const score = calculateScore();
        const passed = score >= 70;

        return (
            <motion.div
                initial={{ opacity: 0, scale: 0.95 }}
                animate={{ opacity: 1, scale: 1 }}
                className="text-center space-y-6"
            >
                {/* Score Circle */}
                <div className="relative w-32 h-32 mx-auto">
                    <svg className="w-full h-full transform -rotate-90">
                        <circle
                            cx="64"
                            cy="64"
                            r="56"
                            stroke="currentColor"
                            strokeWidth="12"
                            fill="none"
                            className="text-surface-200 dark:text-dark-300"
                        />
                        <motion.circle
                            cx="64"
                            cy="64"
                            r="56"
                            stroke="currentColor"
                            strokeWidth="12"
                            fill="none"
                            strokeLinecap="round"
                            className={passed ? 'text-green-500' : 'text-red-500'}
                            initial={{ strokeDasharray: '0 352' }}
                            animate={{ strokeDasharray: `${(score / 100) * 352} 352` }}
                            transition={{ duration: 1, ease: 'easeOut' }}
                        />
                    </svg>
                    <div className="absolute inset-0 flex items-center justify-center">
                        <span
                            className={`text-3xl font-bold ${passed ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
                                }`}
                        >
                            {score}%
                        </span>
                    </div>
                </div>

                {/* Result Message */}
                <div>
                    <h3 className="text-xl font-bold text-text-primary dark:text-text-dark-primary">
                        {passed ? 'أحسنت! ترتيب صحيح' : 'حاول مرة أخرى'}
                    </h3>
                    <p className="text-text-muted dark:text-text-dark-muted mt-1">
                        {score === 100
                            ? 'رتبت جميع الآيات بشكل صحيح!'
                            : `رتبت ${Math.round((score / 100) * orderedItems.length)} من ${orderedItems.length} آيات بشكل صحيح`}
                    </p>
                </div>

                {/* Correct Order Display */}
                <div className="bg-surface-100 dark:bg-dark-300 rounded-xl p-4 space-y-2 text-right max-h-60 overflow-y-auto">
                    <p className="text-sm font-medium text-text-secondary dark:text-text-dark-secondary mb-3">
                        الترتيب الصحيح:
                    </p>
                    {verseItems.map((item, index) => {
                        const userIndex = orderedItems.findIndex((v) => v.id === item.id);
                        const isCorrectPosition = userIndex === index;

                        return (
                            <div
                                key={item.id}
                                className={`p-3 rounded-lg flex items-start gap-3 ${isCorrectPosition
                                        ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800'
                                        : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800'
                                    }`}
                            >
                                <span
                                    className={`flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-sm font-bold ${isCorrectPosition
                                            ? 'bg-green-500 text-white'
                                            : 'bg-red-500 text-white'
                                        }`}
                                >
                                    {item.verseNumber}
                                </span>
                                <p className="font-amiri text-lg text-text-primary dark:text-text-dark-primary flex-1" dir="rtl">
                                    {item.text.substring(0, 50)}...
                                </p>
                                {isCorrectPosition ? (
                                    <Check className="w-5 h-5 text-green-500 flex-shrink-0" />
                                ) : (
                                    <X className="w-5 h-5 text-red-500 flex-shrink-0" />
                                )}
                            </div>
                        );
                    })}
                </div>

                {/* Action Buttons */}
                <div className="flex gap-3 justify-center">
                    <motion.button
                        whileHover={{ scale: 1.02 }}
                        whileTap={{ scale: 0.98 }}
                        onClick={handleRetry}
                        className="flex items-center gap-2 px-6 py-3 rounded-xl border-2 border-surface-300 dark:border-dark-200 text-text-primary dark:text-text-dark-primary hover:bg-surface-100 dark:hover:bg-dark-300 transition-colors"
                    >
                        <RotateCcw className="w-5 h-5" />
                        إعادة الاختبار
                    </motion.button>
                    {passed && (
                        <motion.button
                            whileHover={{ scale: 1.02 }}
                            whileTap={{ scale: 0.98 }}
                            onClick={handleComplete}
                            className="flex items-center gap-2 px-6 py-3 rounded-xl bg-green-500 text-white hover:bg-green-600 transition-colors"
                        >
                            <Check className="w-5 h-5" />
                            متابعة
                        </motion.button>
                    )}
                </div>
            </motion.div>
        );
    }

    // Test Screen
    return (
        <div className="space-y-6">
            {/* Instructions */}
            <div className="text-center">
                <h3 className="text-lg font-bold text-text-primary dark:text-text-dark-primary">
                    رتّب الآيات بالترتيب الصحيح
                </h3>
                <p className="text-sm text-text-muted dark:text-text-dark-muted mt-1">
                    اسحب الآيات أو استخدم الأسهم لترتيبها
                </p>
            </div>

            {/* Reorderable List */}
            <Reorder.Group
                axis="y"
                values={orderedItems}
                onReorder={setOrderedItems}
                className="space-y-3"
            >
                <AnimatePresence>
                    {orderedItems.map((item, index) => (
                        <Reorder.Item
                            key={item.id}
                            value={item}
                            className="cursor-grab active:cursor-grabbing"
                            whileDrag={{
                                scale: 1.02,
                                boxShadow: '0 10px 30px rgba(0,0,0,0.2)',
                            }}
                        >
                            <motion.div
                                layout
                                initial={{ opacity: 0, y: 20 }}
                                animate={{ opacity: 1, y: 0 }}
                                exit={{ opacity: 0, y: -20 }}
                                className="bg-white dark:bg-dark-400 rounded-xl p-4 border-2 border-surface-200 dark:border-dark-200 hover:border-primary-400 dark:hover:border-primary-500 transition-colors"
                            >
                                <div className="flex items-start gap-3">
                                    {/* Drag Handle */}
                                    <div className="flex flex-col items-center gap-1 pt-1">
                                        <GripVertical className="w-5 h-5 text-text-muted dark:text-text-dark-muted" />
                                        <span className="text-xs text-text-muted dark:text-text-dark-muted">
                                            {index + 1}
                                        </span>
                                    </div>

                                    {/* Verse Text */}
                                    <div className="flex-1">
                                        <p
                                            className="font-amiri text-xl leading-relaxed text-text-primary dark:text-text-dark-primary"
                                            dir="rtl"
                                        >
                                            {item.text}
                                        </p>
                                    </div>

                                    {/* Move Buttons (for accessibility) */}
                                    <div className="flex flex-col gap-1">
                                        <button
                                            onClick={() => moveItem(index, -1)}
                                            disabled={index === 0}
                                            className="p-1.5 rounded-lg hover:bg-surface-100 dark:hover:bg-dark-300 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                                        >
                                            <ArrowUp className="w-4 h-4 text-text-muted dark:text-text-dark-muted" />
                                        </button>
                                        <button
                                            onClick={() => moveItem(index, 1)}
                                            disabled={index === orderedItems.length - 1}
                                            className="p-1.5 rounded-lg hover:bg-surface-100 dark:hover:bg-dark-300 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                                        >
                                            <ArrowDown className="w-4 h-4 text-text-muted dark:text-text-dark-muted" />
                                        </button>
                                    </div>
                                </div>
                            </motion.div>
                        </Reorder.Item>
                    ))}
                </AnimatePresence>
            </Reorder.Group>

            {/* Submit Button */}
            <motion.button
                whileHover={{ scale: 1.02 }}
                whileTap={{ scale: 0.98 }}
                onClick={handleSubmit}
                className="w-full flex items-center justify-center gap-2 px-6 py-4 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition-colors"
            >
                <Check className="w-5 h-5" />
                تحقق من الترتيب
            </motion.button>
        </div>
    );
}
