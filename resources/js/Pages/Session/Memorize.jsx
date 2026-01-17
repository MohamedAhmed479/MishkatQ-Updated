import { Head, router } from '@inertiajs/react';
import { useState, useEffect, useRef } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    ArrowRight,
    ArrowLeft,
    Check,
    Eye,
    EyeOff,
    X,
    BookOpen
} from 'lucide-react';
import { ThemeProvider, useTheme } from '@/Contexts/ThemeContext';
import Button from '@/Components/UI/Button';
import TafsirSection from '@/Components/Session/TafsirSection';
import AudioPlayer from '@/Components/Session/AudioPlayer';
import ViewModeToggle from '@/Components/Session/ViewModeToggle';
import VerseCard from '@/Components/Session/VerseCard';
import ContinuousPlayer from '@/Components/Session/ContinuousPlayer';

function MemorizeContent({ planItem, verses, reciters }) {
    const { theme } = useTheme();

    // View mode: 'single', 'all', or 'reading'
    const [viewMode, setViewMode] = useState(() => {
        if (typeof window !== 'undefined') {
            return localStorage.getItem('memorize_view_mode') || 'single';
        }
        return 'single';
    });

    // Current verse index (for single mode and reading mode highlight)
    const [currentVerseIndex, setCurrentVerseIndex] = useState(0);

    // UI states
    const [showText, setShowText] = useState(true);
    const [completed, setCompleted] = useState(false);
    const [rating, setRating] = useState(null);

    // Tafsir open states (map of verse id to boolean)
    const [tafsirOpen, setTafsirOpen] = useState({});

    // Selected reciter
    const [selectedReciterId, setSelectedReciterId] = useState(() => {
        if (typeof window !== 'undefined') {
            const saved = localStorage.getItem('memorize_selected_reciter');
            if (saved) return parseInt(saved);
        }
        // Default to first reciter if available
        return reciters?.[0]?.id || null;
    });

    // Ref for scrolling to current verse in reading mode
    const verseRefs = useRef({});

    const currentVerse = verses[currentVerseIndex];
    const progress = ((currentVerseIndex + 1) / verses.length) * 100;

    // Save preferences to localStorage
    useEffect(() => {
        if (typeof window !== 'undefined') {
            localStorage.setItem('memorize_view_mode', viewMode);
        }
    }, [viewMode]);

    useEffect(() => {
        if (typeof window !== 'undefined' && selectedReciterId) {
            localStorage.setItem('memorize_selected_reciter', selectedReciterId.toString());
        }
    }, [selectedReciterId]);

    // Scroll to current verse in reading mode when it changes
    useEffect(() => {
        if (viewMode === 'reading' && verseRefs.current[currentVerseIndex]) {
            verseRefs.current[currentVerseIndex].scrollIntoView({
                behavior: 'smooth',
                block: 'center',
            });
        }
    }, [currentVerseIndex, viewMode]);

    const handleNext = () => {
        if (currentVerseIndex < verses.length - 1) {
            setCurrentVerseIndex(currentVerseIndex + 1);
            setShowText(true);
        } else {
            setCompleted(true);
        }
    };

    const handlePrevious = () => {
        if (currentVerseIndex > 0) {
            setCurrentVerseIndex(currentVerseIndex - 1);
            setShowText(true);
        }
    };

    const handleComplete = () => {
        router.post(`/app/session/${planItem.id}/complete`, {
            quality_rating: rating,
        }, {
            preserveScroll: true,
        });
    };

    const handleClose = () => {
        if (confirm('هل تريد الخروج من جلسة الحفظ؟')) {
            router.visit('/app/dashboard');
        }
    };

    const handleViewModeChange = (mode) => {
        setViewMode(mode);
    };

    const toggleTafsir = (verseId) => {
        setTafsirOpen(prev => ({
            ...prev,
            [verseId]: !prev[verseId]
        }));
    };

    // Handle verse change from continuous player
    const handleVerseChange = (index) => {
        setCurrentVerseIndex(index);
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
                        <h1 className="font-bold text-text-primary dark:text-text-dark-primary">
                            سورة {planItem.chapter_name}
                        </h1>
                        <p className="text-xs text-text-muted dark:text-text-dark-muted">
                            الآيات {planItem.start_verse} - {planItem.end_verse}
                        </p>
                    </div>

                    <div className="flex items-center gap-2">
                        {/* View Mode Toggle */}
                        <ViewModeToggle viewMode={viewMode} onChange={handleViewModeChange} />

                        {/* Show/Hide Text (only in single mode) */}
                        {viewMode === 'single' && (
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
                        )}
                    </div>
                </div>

                {/* Progress Bar */}
                <div className="h-1 bg-surface-200 dark:bg-dark-300">
                    <motion.div
                        className="h-full bg-primary-500"
                        initial={{ width: 0 }}
                        animate={{ width: viewMode === 'single' ? `${progress}%` : '100%' }}
                        transition={{ duration: 0.3 }}
                    />
                </div>
            </header>

            {/* Main Content */}
            <main className="flex-1 flex flex-col p-4 md:p-6">
                <AnimatePresence mode="wait">
                    {!completed ? (
                        <>
                            {/* Single Verse Mode */}
                            {viewMode === 'single' && (
                                <motion.div
                                    key={`single-${currentVerseIndex}`}
                                    initial={{ opacity: 0, x: 50 }}
                                    animate={{ opacity: 1, x: 0 }}
                                    exit={{ opacity: 0, x: -50 }}
                                    className="flex-1 flex flex-col items-center justify-center max-w-3xl mx-auto w-full"
                                >
                                    {/* Verse Number */}
                                    <div className="mb-6">
                                        <span className="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 font-bold text-lg">
                                            {currentVerse.verse_number}
                                        </span>
                                    </div>

                                    {/* Verse Text */}
                                    <motion.div
                                        initial={{ opacity: 0 }}
                                        animate={{ opacity: showText ? 1 : 0.1 }}
                                        className={`
                                            font-amiri text-3xl md:text-4xl lg:text-5xl leading-loose
                                            text-text-primary dark:text-text-dark-primary
                                            transition-opacity duration-300 text-center
                                            ${!showText ? 'select-none blur-sm' : ''}
                                        `}
                                        dir="rtl"
                                    >
                                        {currentVerse.text}
                                    </motion.div>

                                    {/* Page Number */}
                                    {currentVerse.page_number && (
                                        <div className="mt-4">
                                            <span className="text-xs text-text-muted dark:text-text-dark-muted bg-surface-100 dark:bg-dark-300 px-3 py-1 rounded-full">
                                                صفحة {currentVerse.page_number}
                                            </span>
                                        </div>
                                    )}

                                    {/* Verse Counter */}
                                    <p className="mt-6 text-text-muted dark:text-text-dark-muted">
                                        آية {currentVerseIndex + 1} من {verses.length}
                                    </p>

                                    {/* Tafsir Section */}
                                    <div className="mt-6 w-full max-w-xl">
                                        <TafsirSection
                                            tafsir={currentVerse.tafsir}
                                            isOpen={tafsirOpen[currentVerse.id]}
                                            onToggle={() => toggleTafsir(currentVerse.id)}
                                        />
                                    </div>

                                    {/* Audio Player */}
                                    <div className="mt-6 w-full max-w-xl">
                                        <AudioPlayer
                                            recitations={currentVerse.recitations}
                                            selectedReciterId={selectedReciterId}
                                            onReciterChange={setSelectedReciterId}
                                        />
                                    </div>
                                </motion.div>
                            )}

                            {/* All Verses Mode (Cards) */}
                            {viewMode === 'all' && (
                                <motion.div
                                    key="all-verses"
                                    initial={{ opacity: 0 }}
                                    animate={{ opacity: 1 }}
                                    exit={{ opacity: 0 }}
                                    className="space-y-4 max-w-3xl mx-auto w-full"
                                >
                                    {verses.map((verse, index) => (
                                        <VerseCard
                                            key={verse.id}
                                            verse={verse}
                                            index={index}
                                            isTafsirOpen={tafsirOpen[verse.id]}
                                            onToggleTafsir={() => toggleTafsir(verse.id)}
                                            selectedReciterId={selectedReciterId}
                                            onReciterChange={setSelectedReciterId}
                                        />
                                    ))}

                                    {/* Complete Button */}
                                    <div className="pt-6 pb-8">
                                        <Button
                                            onClick={() => setCompleted(true)}
                                            className="w-full"
                                            size="lg"
                                            icon={Check}
                                        >
                                            أتممت الحفظ
                                        </Button>
                                    </div>
                                </motion.div>
                            )}

                            {/* Reading Mode (Continuous Text) */}
                            {viewMode === 'reading' && (
                                <motion.div
                                    key="reading-mode"
                                    initial={{ opacity: 0 }}
                                    animate={{ opacity: 1 }}
                                    exit={{ opacity: 0 }}
                                    className="max-w-4xl mx-auto w-full"
                                >
                                    {/* Continuous Audio Player */}
                                    <div className="mb-6">
                                        <ContinuousPlayer
                                            verses={verses}
                                            selectedReciterId={selectedReciterId}
                                            onReciterChange={setSelectedReciterId}
                                            reciters={reciters}
                                            onVerseChange={handleVerseChange}
                                        />
                                    </div>

                                    {/* Continuous Text Display */}
                                    <div className="bg-white dark:bg-dark-400 rounded-2xl p-6 md:p-8 shadow-lg border border-surface-300 dark:border-dark-300">
                                        {/* Page Number */}
                                        {verses[0]?.page_number && (
                                            <div className="text-center mb-6">
                                                <span className="text-sm text-text-muted dark:text-text-dark-muted bg-surface-100 dark:bg-dark-300 px-4 py-2 rounded-full">
                                                    صفحة {verses[0].page_number}
                                                    {verses[verses.length - 1].page_number !== verses[0].page_number && (
                                                        <> - {verses[verses.length - 1].page_number}</>
                                                    )}
                                                </span>
                                            </div>
                                        )}

                                        {/* Continuous Text */}
                                        <div
                                            className="font-amiri text-2xl md:text-3xl lg:text-4xl leading-[2.5] text-text-primary dark:text-text-dark-primary text-justify"
                                            dir="rtl"
                                        >
                                            {verses.map((verse, index) => (
                                                <span
                                                    key={verse.id}
                                                    ref={(el) => verseRefs.current[index] = el}
                                                    className={`
                                                        transition-all duration-300 cursor-pointer
                                                        ${currentVerseIndex === index
                                                            ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 rounded px-1'
                                                            : 'hover:text-primary-600 dark:hover:text-primary-400'
                                                        }
                                                    `}
                                                    onClick={() => setCurrentVerseIndex(index)}
                                                >
                                                    {verse.text}
                                                    <span className="inline-flex items-center justify-center w-6 h-6 mx-1 text-xs font-bold text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 rounded-full align-middle">
                                                        {verse.verse_number}
                                                    </span>
                                                </span>
                                            ))}
                                        </div>

                                        {/* Tafsir for current verse */}
                                        <div className="mt-8 pt-6 border-t border-surface-200 dark:border-dark-300">
                                            <div className="flex items-center gap-2 mb-3">
                                                <span className="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 font-bold text-sm">
                                                    {currentVerse?.verse_number}
                                                </span>
                                                <span className="text-sm font-medium text-text-secondary dark:text-text-dark-secondary">
                                                    تفسير الآية المحددة
                                                </span>
                                            </div>
                                            <TafsirSection
                                                tafsir={currentVerse?.tafsir}
                                                isOpen={true}
                                                onToggle={() => { }}
                                            />
                                        </div>
                                    </div>

                                    {/* Complete Button */}
                                    <div className="pt-6 pb-8">
                                        <Button
                                            onClick={() => setCompleted(true)}
                                            className="w-full"
                                            size="lg"
                                            icon={Check}
                                        >
                                            أتممت الحفظ
                                        </Button>
                                    </div>
                                </motion.div>
                            )}
                        </>
                    ) : (
                        /* Completion Screen */
                        <motion.div
                            key="completed"
                            initial={{ opacity: 0, scale: 0.9 }}
                            animate={{ opacity: 1, scale: 1 }}
                            className="flex-1 flex items-center justify-center"
                        >
                            <div className="w-full max-w-md text-center">
                                <div className="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center">
                                    <BookOpen className="w-12 h-12 text-white" />
                                </div>
                                <h2 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary mb-2">
                                    أحسنت! 🎉
                                </h2>
                                <p className="text-text-muted dark:text-text-dark-muted mb-8">
                                    أتممت حفظ هذا الجزء
                                </p>

                                {/* Rating */}
                                <div className="mb-8">
                                    <p className="text-sm font-medium text-text-primary dark:text-text-dark-primary mb-4">
                                        كيف تقيّم جودة حفظك؟
                                    </p>
                                    <div className="flex justify-center gap-2">
                                        {[1, 2, 3, 4, 5].map((r) => (
                                            <motion.button
                                                key={r}
                                                whileHover={{ scale: 1.1 }}
                                                whileTap={{ scale: 0.9 }}
                                                onClick={() => setRating(r)}
                                                className={`
                                                    w-12 h-12 rounded-xl text-lg font-bold transition-all
                                                    ${rating === r
                                                        ? 'bg-primary-500 text-white'
                                                        : 'bg-surface-200 dark:bg-dark-300 text-text-primary dark:text-text-dark-primary hover:bg-surface-300 dark:hover:bg-dark-200'
                                                    }
                                                `}
                                            >
                                                {r}
                                            </motion.button>
                                        ))}
                                    </div>
                                    <p className="mt-2 text-xs text-text-muted dark:text-text-dark-muted">
                                        1 = صعب جداً، 5 = سهل جداً
                                    </p>
                                </div>

                                <Button
                                    onClick={handleComplete}
                                    disabled={!rating}
                                    className="w-full"
                                    size="lg"
                                    icon={Check}
                                >
                                    إنهاء الجلسة
                                </Button>
                            </div>
                        </motion.div>
                    )}
                </AnimatePresence>
            </main>

            {/* Footer Navigation (only in single mode and not completed) */}
            {viewMode === 'single' && !completed && (
                <footer className="sticky bottom-0 bg-white/90 dark:bg-dark-400/90 backdrop-blur-xl border-t border-surface-300 dark:border-dark-300 p-4">
                    <div className="max-w-3xl mx-auto flex items-center justify-between">
                        <Button
                            variant="ghost"
                            onClick={handlePrevious}
                            disabled={currentVerseIndex === 0}
                            icon={ArrowRight}
                        >
                            السابقة
                        </Button>

                        <Button
                            onClick={handleNext}
                            icon={currentVerseIndex === verses.length - 1 ? Check : ArrowLeft}
                            iconPosition="left"
                        >
                            {currentVerseIndex === verses.length - 1 ? 'إنهاء' : 'التالية'}
                        </Button>
                    </div>
                </footer>
            )}
        </div>
    );
}

export default function Memorize({ planItem, verses, reciters = [] }) {
    return (
        <ThemeProvider>
            <Head title={`حفظ سورة ${planItem.chapter_name}`} />
            <MemorizeContent planItem={planItem} verses={verses} reciters={reciters} />
        </ThemeProvider>
    );
}
