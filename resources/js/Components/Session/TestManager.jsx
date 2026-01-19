import { useState, useCallback } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { 
    Mic, 
    PenTool, 
    List, 
    Check, 
    ChevronRight, 
    Trophy,
    RotateCcw,
    BookOpen,
    Lock
} from 'lucide-react';
import AudioRecorder from './AudioRecorder';
import GapFillingTest from './GapFillingTest';
import VerseOrderingTest from './VerseOrderingTest';

const TEST_STAGES = {
    SELECTION: 'selection',
    RECITATION: 'recitation',
    GAP_FILLING: 'gap_filling',
    VERSE_ORDERING: 'verse_ordering',
    RESULTS: 'results',
};

export default function TestManager({
    verses = [],
    planItemId,
    onAllTestsComplete,
    onSkipTests,
    requireTests = true,
    minimumScore = 70,
    canUseSmartRecitation = false,
}) {
    const [currentStage, setCurrentStage] = useState(TEST_STAGES.SELECTION);
    const [testResults, setTestResults] = useState({
        recitation: null,
        gap_filling: null,
        verse_ordering: null,
    });
    const [isVerifying, setIsVerifying] = useState(false);
    const [verificationResult, setVerificationResult] = useState(null);

    // Check if all required tests are passed
    const allTestsPassed = useCallback(() => {
        const requiredTests = ['gap_filling', 'verse_ordering'];
        return requiredTests.every(
            (test) => testResults[test]?.passed === true
        );
    }, [testResults]);

    // Calculate overall score
    const calculateOverallScore = useCallback(() => {
        const scores = Object.values(testResults).filter((r) => r !== null);
        if (scores.length === 0) return 0;
        return Math.round(scores.reduce((sum, r) => sum + r.score, 0) / scores.length);
    }, [testResults]);

    // Handle recitation submission (mock AI verification for now)
    const handleRecitationComplete = useCallback(async (audioBlob) => {
        setIsVerifying(true);

        try {
            // In a real implementation, this would send the audio to the backend
            // for Speech-to-Text processing and comparison
            // For now, we'll simulate the verification
            await new Promise((resolve) => setTimeout(resolve, 2000));

            // Simulated result
            const mockResult = {
                accuracy: Math.floor(Math.random() * 30) + 70, // 70-100%
                transcript: verses.map((v) => v.text_imlaei || v.text_uthmani || v.text).join(' '),
                errors: [],
            };

            setVerificationResult(mockResult);
            setTestResults((prev) => ({
                ...prev,
                recitation: {
                    testType: 'recitation',
                    score: mockResult.accuracy,
                    passed: mockResult.accuracy >= minimumScore,
                    details: mockResult,
                },
            }));
        } catch (error) {
            console.error('Verification error:', error);
            setVerificationResult({
                accuracy: 0,
                transcript: '',
                errors: ['حدث خطأ أثناء التحقق من التسميع'],
            });
        } finally {
            setIsVerifying(false);
        }
    }, [verses, minimumScore]);

    const handleRecitationContinue = useCallback(() => {
        // بعد اجتياز التسميع الصوتي بنجاح، نرجع المستخدم إلى شاشة اختيار/الاختبارات
        setCurrentStage(TEST_STAGES.SELECTION);
    }, []);

    // Handle gap filling test completion
    const handleGapFillingComplete = useCallback((result) => {
        setTestResults((prev) => ({
            ...prev,
            gap_filling: result,
        }));
        
        // Auto-advance to verse ordering if passed
        if (result.passed) {
            setTimeout(() => {
                setCurrentStage(TEST_STAGES.VERSE_ORDERING);
            }, 500);
        }
    }, []);

    // Handle verse ordering test completion
    const handleVerseOrderingComplete = useCallback((result) => {
        setTestResults((prev) => ({
            ...prev,
            verse_ordering: result,
        }));

        // Show results if completed
        if (result.passed) {
            setTimeout(() => {
                setCurrentStage(TEST_STAGES.RESULTS);
            }, 500);
        }
    }, []);

    // Handle retry for recitation
    const handleRecitationRetry = useCallback(() => {
        setVerificationResult(null);
    }, []);

    // Handle final completion
    const handleFinalComplete = useCallback(() => {
        if (onAllTestsComplete) {
            onAllTestsComplete({
                results: testResults,
                overallScore: calculateOverallScore(),
                passed: allTestsPassed(),
            });
        }
    }, [testResults, calculateOverallScore, allTestsPassed, onAllTestsComplete]);

    // Render test selection screen
    const renderTestSelection = () => (
        <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className="space-y-6"
        >
            <div className="text-center">
                <h2 className="text-xl font-bold text-text-primary dark:text-text-dark-primary">
                    اختبار الحفظ
                </h2>
                <p className="text-text-muted dark:text-text-dark-muted mt-1">
                    أكمل الاختبارات التالية لتأكيد حفظك
                </p>
            </div>

            {/* Test Cards */}
            <div className="space-y-3">
                {/* Recitation Test (Optional - Requires Admin Permission) */}
                <TestCard
                    icon={Mic}
                    title="التسميع الصوتي"
                    description="سجّل تلاوتك للتحقق منها"
                    status={testResults.recitation?.passed ? 'passed' : testResults.recitation ? 'failed' : 'pending'}
                    score={testResults.recitation?.score}
                    onClick={() => setCurrentStage(TEST_STAGES.RECITATION)}
                    optional={canUseSmartRecitation}
                    locked={!canUseSmartRecitation}
                    lockedMessage="هذه الميزة غير متاحة لحسابك حالياً. تواصل مع الإدارة لتفعيلها."
                />

                {/* Gap Filling Test */}
                <TestCard
                    icon={PenTool}
                    title="ملء الفراغات"
                    description="أكمل الكلمات الناقصة في الآيات"
                    status={testResults.gap_filling?.passed ? 'passed' : testResults.gap_filling ? 'failed' : 'pending'}
                    score={testResults.gap_filling?.score}
                    onClick={() => setCurrentStage(TEST_STAGES.GAP_FILLING)}
                    required={true}
                />

                {/* Verse Ordering Test */}
                <TestCard
                    icon={List}
                    title="ترتيب الآيات"
                    description="رتّب الآيات بالترتيب الصحيح"
                    status={testResults.verse_ordering?.passed ? 'passed' : testResults.verse_ordering ? 'failed' : 'pending'}
                    score={testResults.verse_ordering?.score}
                    onClick={() => setCurrentStage(TEST_STAGES.VERSE_ORDERING)}
                    required={true}
                    disabled={!testResults.gap_filling?.passed}
                />
            </div>

            {/* Progress Summary */}
            <div className="bg-surface-100 dark:bg-dark-300 rounded-xl p-4">
                <div className="flex justify-between items-center mb-2">
                    <span className="text-sm font-medium text-text-secondary dark:text-text-dark-secondary">
                        التقدم في الاختبارات
                    </span>
                    <span className="text-sm font-bold text-primary-600 dark:text-primary-400">
                        {Object.values(testResults).filter((r) => r?.passed).length} / 2 مكتمل
                    </span>
                </div>
                <div className="h-2 bg-surface-200 dark:bg-dark-200 rounded-full overflow-hidden">
                    <motion.div
                        className="h-full bg-primary-500 rounded-full"
                        initial={{ width: 0 }}
                        animate={{
                            width: `${(Object.values(testResults).filter((r) => r?.passed).length / 2) * 100}%`,
                        }}
                    />
                </div>
            </div>

            {/* Action Buttons */}
            <div className="flex gap-3">
                {!requireTests && (
                    <motion.button
                        whileHover={{ scale: 1.02 }}
                        whileTap={{ scale: 0.98 }}
                        onClick={onSkipTests}
                        className="flex-1 px-4 py-3 rounded-xl border-2 border-surface-300 dark:border-dark-200 text-text-primary dark:text-text-dark-primary hover:bg-surface-100 dark:hover:bg-dark-300 transition-colors"
                    >
                        تخطي الاختبارات
                    </motion.button>
                )}
                {allTestsPassed() && (
                    <motion.button
                        whileHover={{ scale: 1.02 }}
                        whileTap={{ scale: 0.98 }}
                        onClick={() => setCurrentStage(TEST_STAGES.RESULTS)}
                        className="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-green-500 text-white hover:bg-green-600 transition-colors"
                    >
                        <Check className="w-5 h-5" />
                        عرض النتائج
                    </motion.button>
                )}
            </div>
        </motion.div>
    );

    // Render final results screen
    const renderResults = () => {
        const overallScore = calculateOverallScore();
        const passed = allTestsPassed();

        return (
            <motion.div
                initial={{ opacity: 0, scale: 0.95 }}
                animate={{ opacity: 1, scale: 1 }}
                className="text-center space-y-6"
            >
                {/* Trophy Icon */}
                <motion.div
                    initial={{ scale: 0 }}
                    animate={{ scale: 1 }}
                    transition={{ type: 'spring', bounce: 0.5 }}
                    className={`w-24 h-24 mx-auto rounded-full flex items-center justify-center ${
                        passed
                            ? 'bg-gradient-to-br from-yellow-400 to-yellow-600'
                            : 'bg-gradient-to-br from-gray-400 to-gray-600'
                    }`}
                >
                    <Trophy className="w-12 h-12 text-white" />
                </motion.div>

                {/* Score */}
                <div>
                    <h2 className="text-3xl font-bold text-text-primary dark:text-text-dark-primary">
                        {overallScore}%
                    </h2>
                    <p className={`text-lg font-medium ${passed ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}`}>
                        {passed ? 'مبارك! اجتزت جميع الاختبارات' : 'لم تجتز بعض الاختبارات'}
                    </p>
                </div>

                {/* Test Results Summary */}
                <div className="bg-surface-100 dark:bg-dark-300 rounded-xl p-4 space-y-3">
                    {Object.entries(testResults).map(([key, result]) => {
                        if (!result) return null;
                        const labels = {
                            recitation: 'التسميع الصوتي',
                            gap_filling: 'ملء الفراغات',
                            verse_ordering: 'ترتيب الآيات',
                        };

                        return (
                            <div key={key} className="flex items-center justify-between">
                                <span className="text-text-secondary dark:text-text-dark-secondary">
                                    {labels[key]}
                                </span>
                                <div className="flex items-center gap-2">
                                    <span
                                        className={`font-bold ${
                                            result.passed
                                                ? 'text-green-600 dark:text-green-400'
                                                : 'text-red-600 dark:text-red-400'
                                        }`}
                                    >
                                        {result.score}%
                                    </span>
                                    {result.passed ? (
                                        <Check className="w-5 h-5 text-green-500" />
                                    ) : (
                                        <RotateCcw className="w-5 h-5 text-red-500" />
                                    )}
                                </div>
                            </div>
                        );
                    })}
                </div>

                {/* Action Buttons */}
                <div className="flex gap-3">
                    <motion.button
                        whileHover={{ scale: 1.02 }}
                        whileTap={{ scale: 0.98 }}
                        onClick={() => setCurrentStage(TEST_STAGES.SELECTION)}
                        className="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-surface-300 dark:border-dark-200 text-text-primary dark:text-text-dark-primary hover:bg-surface-100 dark:hover:bg-dark-300 transition-colors"
                    >
                        <RotateCcw className="w-5 h-5" />
                        إعادة الاختبارات
                    </motion.button>
                    {passed && (
                        <motion.button
                            whileHover={{ scale: 1.02 }}
                            whileTap={{ scale: 0.98 }}
                            onClick={handleFinalComplete}
                            className="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-green-500 text-white hover:bg-green-600 transition-colors"
                        >
                            <Check className="w-5 h-5" />
                            إتمام الحفظ
                        </motion.button>
                    )}
                </div>
            </motion.div>
        );
    };

    return (
        <div className="min-h-[400px]">
            <AnimatePresence mode="wait">
                {currentStage === TEST_STAGES.SELECTION && (
                    <motion.div key="selection" exit={{ opacity: 0, x: -20 }}>
                        {renderTestSelection()}
                    </motion.div>
                )}

                {currentStage === TEST_STAGES.RECITATION && (
                    <motion.div
                        key="recitation"
                        initial={{ opacity: 0, x: 20 }}
                        animate={{ opacity: 1, x: 0 }}
                        exit={{ opacity: 0, x: -20 }}
                        className="space-y-4"
                    >
                        <button
                            onClick={() => setCurrentStage(TEST_STAGES.SELECTION)}
                            className="flex items-center gap-1 text-text-muted dark:text-text-dark-muted hover:text-text-primary dark:hover:text-text-dark-primary transition-colors"
                        >
                            <ChevronRight className="w-5 h-5" />
                            العودة
                        </button>
                        <h3 className="text-lg font-bold text-text-primary dark:text-text-dark-primary text-center">
                            التسميع الصوتي
                        </h3>
                        <AudioRecorder
                            verses={verses}
                            onRecordingComplete={handleRecitationComplete}
                            isVerifying={isVerifying}
                            verificationResult={verificationResult}
                            onRetry={handleRecitationRetry}
                            onContinue={handleRecitationContinue}
                        />
                    </motion.div>
                )}

                {currentStage === TEST_STAGES.GAP_FILLING && (
                    <motion.div
                        key="gap_filling"
                        initial={{ opacity: 0, x: 20 }}
                        animate={{ opacity: 1, x: 0 }}
                        exit={{ opacity: 0, x: -20 }}
                        className="space-y-4"
                    >
                        <button
                            onClick={() => setCurrentStage(TEST_STAGES.SELECTION)}
                            className="flex items-center gap-1 text-text-muted dark:text-text-dark-muted hover:text-text-primary dark:hover:text-text-dark-primary transition-colors"
                        >
                            <ChevronRight className="w-5 h-5" />
                            العودة
                        </button>
                        <GapFillingTest
                            verses={verses}
                            onComplete={handleGapFillingComplete}
                            questionsCount={Math.min(5, verses.length * 2)}
                        />
                    </motion.div>
                )}

                {currentStage === TEST_STAGES.VERSE_ORDERING && (
                    <motion.div
                        key="verse_ordering"
                        initial={{ opacity: 0, x: 20 }}
                        animate={{ opacity: 1, x: 0 }}
                        exit={{ opacity: 0, x: -20 }}
                        className="space-y-4"
                    >
                        <button
                            onClick={() => setCurrentStage(TEST_STAGES.SELECTION)}
                            className="flex items-center gap-1 text-text-muted dark:text-text-dark-muted hover:text-text-primary dark:hover:text-text-dark-primary transition-colors"
                        >
                            <ChevronRight className="w-5 h-5" />
                            العودة
                        </button>
                        <VerseOrderingTest
                            verses={verses}
                            onComplete={handleVerseOrderingComplete}
                        />
                    </motion.div>
                )}

                {currentStage === TEST_STAGES.RESULTS && (
                    <motion.div key="results" exit={{ opacity: 0, x: -20 }}>
                        {renderResults()}
                    </motion.div>
                )}
            </AnimatePresence>
        </div>
    );
}

// Test Card Component
function TestCard({ icon: Icon, title, description, status, score, onClick, required, optional, disabled, locked, lockedMessage }) {
    const statusColors = {
        pending: 'border-surface-200 dark:border-dark-200',
        passed: 'border-green-400 dark:border-green-600 bg-green-50 dark:bg-green-900/20',
        failed: 'border-red-400 dark:border-red-600 bg-red-50 dark:bg-red-900/20',
        locked: 'border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-800/50',
    };

    const isLocked = locked === true;
    const effectiveStatus = isLocked ? 'locked' : status;
    const isDisabled = disabled || isLocked;

    return (
        <motion.button
            whileHover={!isDisabled ? { scale: 1.01 } : {}}
            whileTap={!isDisabled ? { scale: 0.99 } : {}}
            onClick={isLocked ? undefined : onClick}
            disabled={isDisabled}
            className={`
                w-full p-4 rounded-xl border-2 transition-all text-right
                ${statusColors[effectiveStatus]}
                ${isDisabled ? 'opacity-60 cursor-not-allowed' : 'hover:border-primary-400 dark:hover:border-primary-500 cursor-pointer'}
            `}
        >
            <div className="flex items-start gap-3">
                <div
                    className={`w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 ${
                        isLocked
                            ? 'bg-slate-300 dark:bg-slate-600 text-slate-500 dark:text-slate-400'
                            : status === 'passed'
                            ? 'bg-green-500 text-white'
                            : status === 'failed'
                            ? 'bg-red-500 text-white'
                            : 'bg-surface-200 dark:bg-dark-300 text-text-muted dark:text-text-dark-muted'
                    }`}
                >
                    {isLocked ? (
                        <Lock className="w-6 h-6" />
                    ) : status === 'passed' ? (
                        <Check className="w-6 h-6" />
                    ) : (
                        <Icon className="w-6 h-6" />
                    )}
                </div>

                <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-2">
                        <h4 className={`font-bold ${isLocked ? 'text-slate-500 dark:text-slate-400' : 'text-text-primary dark:text-text-dark-primary'}`}>
                            {title}
                        </h4>
                        {isLocked && (
                            <span className="text-xs px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400 flex items-center gap-1">
                                <Lock className="w-3 h-3" />
                                مغلق
                            </span>
                        )}
                        {!isLocked && optional && (
                            <span className="text-xs px-2 py-0.5 rounded-full bg-surface-200 dark:bg-dark-300 text-text-muted dark:text-text-dark-muted">
                                اختياري
                            </span>
                        )}
                        {!isLocked && required && (
                            <span className="text-xs px-2 py-0.5 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300">
                                مطلوب
                            </span>
                        )}
                    </div>
                    <p className={`text-sm mt-0.5 ${isLocked ? 'text-slate-400 dark:text-slate-500' : 'text-text-muted dark:text-text-dark-muted'}`}>
                        {isLocked && lockedMessage ? lockedMessage : description}
                    </p>
                    {!isLocked && score !== undefined && score !== null && (
                        <p
                            className={`text-sm font-medium mt-1 ${
                                status === 'passed'
                                    ? 'text-green-600 dark:text-green-400'
                                    : 'text-red-600 dark:text-red-400'
                            }`}
                        >
                            النتيجة: {score}%
                        </p>
                    )}
                </div>

                {isLocked ? (
                    <Lock className="w-5 h-5 text-slate-400 dark:text-slate-500 flex-shrink-0" />
                ) : (
                    <ChevronRight className="w-5 h-5 text-text-muted dark:text-text-dark-muted rotate-180 flex-shrink-0" />
                )}
            </div>
        </motion.button>
    );
}
