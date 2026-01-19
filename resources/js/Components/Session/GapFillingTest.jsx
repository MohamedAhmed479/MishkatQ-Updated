import { useState, useEffect, useMemo, useCallback } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Check, X, ArrowLeft, RotateCcw, HelpCircle } from 'lucide-react';

/**
 * Generates gap-filling questions from verses
 * @param {Array} verses - Array of verse objects with text property
 * @param {number} gapsPerVerse - Number of words to hide per verse
 * @returns {Array} Array of question objects
 */
function generateQuestions(verses, gapsPerVerse = 1) {
    const questions = [];

    verses.forEach((verse, verseIndex) => {
        const text = verse.text_imlaei || verse.text_uthmani || verse.text;
        const words = text.split(/\s+/).filter((w) => w.length > 0);

        if (words.length < 3) return; // Skip very short verses

        // Select random word positions to hide
        const numGaps = Math.min(gapsPerVerse, Math.floor(words.length / 3));
        const usedPositions = new Set();

        for (let i = 0; i < numGaps; i++) {
            let position;
            let attempts = 0;

            // Find a suitable position (not already used, not first or last word)
            do {
                position = 1 + Math.floor(Math.random() * (words.length - 2));
                attempts++;
            } while (usedPositions.has(position) && attempts < 20);

            if (usedPositions.has(position)) continue;
            usedPositions.add(position);

            const correctWord = words[position];

            // Generate wrong options from other words in the passage
            const allWords = verses.flatMap((v) => {
                const t = v.text_imlaei || v.text_uthmani || v.text;
                return t.split(/\s+/).filter((w) => w.length > 0 && w !== correctWord);
            });

            // Get 3 random wrong options
            const shuffledWords = [...new Set(allWords)].sort(() => Math.random() - 0.5);
            const wrongOptions = shuffledWords.slice(0, 3);

            // Create options array and shuffle
            const options = [...wrongOptions, correctWord].sort(() => Math.random() - 0.5);

            // Create the question text with gap
            const questionWords = [...words];
            questionWords[position] = '______';
            const questionText = questionWords.join(' ');

            questions.push({
                id: `${verseIndex}-${position}`,
                verseNumber: verse.verse_number,
                verseIndex,
                questionText,
                correctWord,
                options,
                gapPosition: position,
                fullText: text,
            });
        }
    });

    // Shuffle questions
    return questions.sort(() => Math.random() - 0.5);
}

export default function GapFillingTest({
    verses = [],
    onComplete,
    questionsCount = 5,
    gapsPerVerse = 1,
}) {
    const [currentQuestionIndex, setCurrentQuestionIndex] = useState(0);
    const [selectedAnswer, setSelectedAnswer] = useState(null);
    const [isAnswered, setIsAnswered] = useState(false);
    const [answers, setAnswers] = useState([]);
    const [showResults, setShowResults] = useState(false);
    const [showHint, setShowHint] = useState(false);

    // Generate questions once when component mounts or verses change
    const questions = useMemo(() => {
        const allQuestions = generateQuestions(verses, gapsPerVerse);
        return allQuestions.slice(0, Math.min(questionsCount, allQuestions.length));
    }, [verses, questionsCount, gapsPerVerse]);

    const currentQuestion = questions[currentQuestionIndex];
    const progress = ((currentQuestionIndex + 1) / questions.length) * 100;

    const handleSelectAnswer = useCallback(
        (option) => {
            if (isAnswered) return;

            setSelectedAnswer(option);
            setIsAnswered(true);

            const isCorrect = option === currentQuestion.correctWord;
            setAnswers((prev) => [
                ...prev,
                {
                    questionId: currentQuestion.id,
                    selectedAnswer: option,
                    correctAnswer: currentQuestion.correctWord,
                    isCorrect,
                },
            ]);
        },
        [isAnswered, currentQuestion]
    );

    const handleNext = useCallback(() => {
        if (currentQuestionIndex < questions.length - 1) {
            setCurrentQuestionIndex((prev) => prev + 1);
            setSelectedAnswer(null);
            setIsAnswered(false);
            setShowHint(false);
        } else {
            setShowResults(true);
        }
    }, [currentQuestionIndex, questions.length]);

    const handleRetry = useCallback(() => {
        setCurrentQuestionIndex(0);
        setSelectedAnswer(null);
        setIsAnswered(false);
        setAnswers([]);
        setShowResults(false);
        setShowHint(false);
    }, []);

    const handleComplete = useCallback(() => {
        const correctCount = answers.filter((a) => a.isCorrect).length;
        const score = Math.round((correctCount / questions.length) * 100);

        if (onComplete) {
            onComplete({
                testType: 'gap_filling',
                score,
                passed: score >= 70,
                details: {
                    questions: questions.map((q) => ({
                        id: q.id,
                        verseNumber: q.verseNumber,
                        correctWord: q.correctWord,
                    })),
                    answers,
                    correctCount,
                    totalQuestions: questions.length,
                },
            });
        }
    }, [answers, questions, onComplete]);

    // Auto-advance after showing result
    useEffect(() => {
        if (isAnswered && !showResults) {
            const timer = setTimeout(() => {
                handleNext();
            }, 1500);
            return () => clearTimeout(timer);
        }
    }, [isAnswered, showResults, handleNext]);

    if (questions.length === 0) {
        return (
            <div className="text-center py-8">
                <p className="text-text-muted dark:text-text-dark-muted">
                    لا توجد آيات كافية لإنشاء الاختبار
                </p>
            </div>
        );
    }

    // Results Screen
    if (showResults) {
        const correctCount = answers.filter((a) => a.isCorrect).length;
        const score = Math.round((correctCount / questions.length) * 100);
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
                        {passed ? 'أحسنت! اجتزت الاختبار' : 'حاول مرة أخرى'}
                    </h3>
                    <p className="text-text-muted dark:text-text-dark-muted mt-1">
                        أجبت على {correctCount} من {questions.length} أسئلة بشكل صحيح
                    </p>
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

    // Question Screen
    return (
        <div className="space-y-6">
            {/* Progress Bar */}
            <div className="space-y-2">
                <div className="flex justify-between text-sm text-text-muted dark:text-text-dark-muted">
                    <span>سؤال {currentQuestionIndex + 1} من {questions.length}</span>
                    <span>الآية {currentQuestion.verseNumber}</span>
                </div>
                <div className="h-2 bg-surface-200 dark:bg-dark-300 rounded-full overflow-hidden">
                    <motion.div
                        className="h-full bg-primary-500 rounded-full"
                        initial={{ width: 0 }}
                        animate={{ width: `${progress}%` }}
                        transition={{ duration: 0.3 }}
                    />
                </div>
            </div>

            {/* Question */}
            <AnimatePresence mode="wait">
                <motion.div
                    key={currentQuestion.id}
                    initial={{ opacity: 0, x: 20 }}
                    animate={{ opacity: 1, x: 0 }}
                    exit={{ opacity: 0, x: -20 }}
                    className="space-y-4"
                >
                    {/* Question Text */}
                    <div className="p-6 rounded-2xl bg-surface-100 dark:bg-dark-300 border border-surface-200 dark:border-dark-200">
                        <p className="text-lg font-medium text-text-secondary dark:text-text-dark-secondary mb-2">
                            أكمل الفراغ:
                        </p>
                        <p
                            className="font-amiri text-2xl md:text-3xl leading-loose text-text-primary dark:text-text-dark-primary text-center"
                            dir="rtl"
                        >
                            {currentQuestion.questionText.split('______').map((part, idx, arr) => (
                                <span key={idx}>
                                    {part}
                                    {idx < arr.length - 1 && (
                                        <span className="inline-block mx-2 px-4 py-1 rounded-lg bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 border-2 border-dashed border-primary-400">
                                            {isAnswered ? (
                                                <span
                                                    className={
                                                        selectedAnswer === currentQuestion.correctWord
                                                            ? 'text-green-600 dark:text-green-400'
                                                            : 'text-red-600 dark:text-red-400'
                                                    }
                                                >
                                                    {currentQuestion.correctWord}
                                                </span>
                                            ) : (
                                                '؟'
                                            )}
                                        </span>
                                    )}
                                </span>
                            ))}
                        </p>
                    </div>

                    {/* Hint Button */}
                    {!isAnswered && !showHint && (
                        <button
                            onClick={() => setShowHint(true)}
                            className="flex items-center gap-2 text-sm text-text-muted dark:text-text-dark-muted hover:text-primary-500 transition-colors mx-auto"
                        >
                            <HelpCircle className="w-4 h-4" />
                            إظهار تلميح
                        </button>
                    )}

                    {/* Hint */}
                    <AnimatePresence>
                        {showHint && !isAnswered && (
                            <motion.div
                                initial={{ opacity: 0, height: 0 }}
                                animate={{ opacity: 1, height: 'auto' }}
                                exit={{ opacity: 0, height: 0 }}
                                className="p-3 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-yellow-700 dark:text-yellow-300 text-sm text-center"
                            >
                                الحرف الأول: {currentQuestion.correctWord.charAt(0)}
                            </motion.div>
                        )}
                    </AnimatePresence>

                    {/* Options */}
                    <div className="grid grid-cols-2 gap-3">
                        {currentQuestion.options.map((option, idx) => {
                            const isSelected = selectedAnswer === option;
                            const isCorrect = option === currentQuestion.correctWord;
                            const showCorrect = isAnswered && isCorrect;
                            const showWrong = isAnswered && isSelected && !isCorrect;

                            return (
                                <motion.button
                                    key={idx}
                                    whileHover={!isAnswered ? { scale: 1.02 } : {}}
                                    whileTap={!isAnswered ? { scale: 0.98 } : {}}
                                    onClick={() => handleSelectAnswer(option)}
                                    disabled={isAnswered}
                                    className={`
                                        p-4 rounded-xl font-amiri text-xl transition-all
                                        ${!isAnswered
                                            ? 'bg-white dark:bg-dark-400 border-2 border-surface-200 dark:border-dark-200 hover:border-primary-400 dark:hover:border-primary-500 text-text-primary dark:text-text-dark-primary'
                                            : showCorrect
                                                ? 'bg-green-100 dark:bg-green-900/30 border-2 border-green-500 text-green-700 dark:text-green-300'
                                                : showWrong
                                                    ? 'bg-red-100 dark:bg-red-900/30 border-2 border-red-500 text-red-700 dark:text-red-300'
                                                    : 'bg-surface-100 dark:bg-dark-300 border-2 border-surface-200 dark:border-dark-200 text-text-muted dark:text-text-dark-muted'
                                        }
                                        disabled:cursor-default
                                    `}
                                >
                                    <div className="flex items-center justify-center gap-2">
                                        {showCorrect && <Check className="w-5 h-5 text-green-600" />}
                                        {showWrong && <X className="w-5 h-5 text-red-600" />}
                                        <span>{option}</span>
                                    </div>
                                </motion.button>
                            );
                        })}
                    </div>
                </motion.div>
            </AnimatePresence>
        </div>
    );
}
