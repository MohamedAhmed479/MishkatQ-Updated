import { Head, router } from '@inertiajs/react';
import { useState, useCallback, useMemo, useRef, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    X,
    Eye,
    EyeOff,
    RefreshCw,
    Star,
    Check,
    Brain,
    Shuffle,
    Volume2,
    VolumeX,
    Lightbulb,
    ChevronRight,
    ChevronLeft,
    BookOpen,
    Play,
    Pause,
    SkipBack,
    SkipForward,
    Repeat,
    User
} from 'lucide-react';
import { ThemeProvider, useTheme } from '@/Contexts/ThemeContext';
import Button from '@/Components/UI/Button';

// Review modes
const REVIEW_MODES = {
    CLASSIC: 'classic',        // Traditional show/hide
    WORD_RECALL: 'word_recall', // Hide random words
    FULL_RECALL: 'full_recall', // Hide entire verse
};

function RevisionContent({ revision, verses, reciters, preferredReciterId }) {
    const { theme } = useTheme();
    const [showText, setShowText] = useState(false);
    const [completed, setCompleted] = useState(false);
    const [rating, setRating] = useState(null);
    const [reviewMode, setReviewMode] = useState(REVIEW_MODES.CLASSIC);
    const [currentVerseIndex, setCurrentVerseIndex] = useState(0);
    const [revealedWords, setRevealedWords] = useState({});
    const [hintsUsed, setHintsUsed] = useState(0);
    const [perfectRecall, setPerfectRecall] = useState(true);

    // Audio & Tafsir states
    const [showTafsir, setShowTafsir] = useState(false);
    const [selectedReciterId, setSelectedReciterId] = useState(preferredReciterId || 1);
    const [isPlaying, setIsPlaying] = useState(false);
    const [currentAudioVerseIndex, setCurrentAudioVerseIndex] = useState(null);
    const [repeatMode, setRepeatMode] = useState(false);
    const [showReciterSelector, setShowReciterSelector] = useState(false);

    const audioRef = useRef(null);

    // Helper to check if text is a real Arabic word
    const isRealWord = (text) => {
        if (!text) return false;
        const cleanText = text.trim();
        const arabicPattern = /[\u0600-\u06FF]/;
        const numberPattern = /^[\d\u0660-\u0669]+$/;
        return arabicPattern.test(cleanText) && !numberPattern.test(cleanText) && cleanText.length > 1;
    };

    // Generate hidden word indices for word recall mode
    const hiddenWordIndices = useMemo(() => {
        const hidden = {};
        verses.forEach((verse, vIndex) => {
            const words = verse.words || [];
            // Filter to only get indices of real words
            const realWordIndices = words
                .map((word, idx) => ({ idx, text: word.text }))
                .filter(w => isRealWord(w.text))
                .map(w => w.idx);

            const numToHide = Math.max(1, Math.floor(realWordIndices.length * 0.4));
            const indices = new Set();

            // Only hide from real words
            while (indices.size < numToHide && indices.size < realWordIndices.length) {
                const randomIdx = Math.floor(Math.random() * realWordIndices.length);
                indices.add(realWordIndices[randomIdx]);
            }
            hidden[vIndex] = Array.from(indices);
        });
        return hidden;
    }, [verses]);

    // Get current verse audio URL for selected reciter
    const getCurrentAudioUrl = useCallback((verseIndex) => {
        const verse = verses[verseIndex];
        if (!verse?.recitations) return null;
        const recitation = verse.recitations.find(r => r.reciter_id === selectedReciterId);
        return recitation?.audio_url || verse.audio_url;
    }, [verses, selectedReciterId]);

    // Audio controls
    const playVerse = useCallback((verseIndex) => {
        const audioUrl = getCurrentAudioUrl(verseIndex);
        if (!audioUrl) return;

        if (audioRef.current) {
            audioRef.current.src = audioUrl;
            audioRef.current.play();
            setIsPlaying(true);
            setCurrentAudioVerseIndex(verseIndex);
        }
    }, [getCurrentAudioUrl]);

    const pauseAudio = useCallback(() => {
        if (audioRef.current) {
            audioRef.current.pause();
            setIsPlaying(false);
        }
    }, []);

    const playAllVerses = useCallback(() => {
        playVerse(0);
    }, [playVerse]);

    const handleAudioEnded = useCallback(() => {
        if (repeatMode && currentAudioVerseIndex !== null) {
            // Repeat current verse
            playVerse(currentAudioVerseIndex);
        } else if (currentAudioVerseIndex !== null && currentAudioVerseIndex < verses.length - 1) {
            // Play next verse
            playVerse(currentAudioVerseIndex + 1);
        } else {
            setIsPlaying(false);
            setCurrentAudioVerseIndex(null);
        }
    }, [currentAudioVerseIndex, repeatMode, verses.length, playVerse]);

    useEffect(() => {
        const audio = audioRef.current;
        if (audio) {
            audio.addEventListener('ended', handleAudioEnded);
            return () => audio.removeEventListener('ended', handleAudioEnded);
        }
    }, [handleAudioEnded]);

    const handleRevealWord = (verseIndex, wordIndex) => {
        const key = `${verseIndex}-${wordIndex}`;
        if (!revealedWords[key]) {
            setRevealedWords(prev => ({ ...prev, [key]: true }));
            setHintsUsed(prev => prev + 1);
            setPerfectRecall(false);
        }
    };

    const handleComplete = () => {
        let finalRating = rating;
        if (hintsUsed > 0 && rating > 3) {
            finalRating = Math.max(2, rating - Math.floor(hintsUsed / 3));
        }

        router.post(`/app/revisions/${revision.id}/record`, {
            performance_rating: finalRating,
        }, {
            preserveScroll: false,
        });
    };

    const handleClose = () => {
        pauseAudio();
        if (confirm('هل تريد الخروج من جلسة المراجعة؟')) {
            router.visit('/app/revisions');
        }
    };

    const nextVerse = () => {
        if (currentVerseIndex < verses.length - 1) {
            setCurrentVerseIndex(prev => prev + 1);
            setShowText(false);
        }
    };

    const prevVerse = () => {
        if (currentVerseIndex > 0) {
            setCurrentVerseIndex(prev => prev - 1);
            setShowText(false);
        }
    };

    const getMemoryStateColor = () => {
        switch (revision.memory_state) {
            case 'young': return 'text-warning bg-warning/10';
            case 'mature': return 'text-accent-600 bg-accent-100';
            case 'mastered': return 'text-success bg-success/10';
            default: return 'text-gray-500 bg-gray-100';
        }
    };

    return (
        <div className="min-h-screen bg-surface-50 dark:bg-dark-500 flex flex-col">
            {/* Hidden Audio Element */}
            <audio ref={audioRef} className="hidden" />

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
                            <Brain className="w-4 h-4 text-primary-500" />
                            <h1 className="font-bold text-text-primary dark:text-text-dark-primary">
                                مراجعة سورة {revision.chapter_name}
                            </h1>
                        </div>
                        <div className="flex items-center justify-center gap-2 mt-1">
                            <span className={`text-xs px-2 py-0.5 rounded-full ${getMemoryStateColor()}`}>
                                {revision.memory_state_ar}
                            </span>
                            <span className="text-xs text-text-muted">
                                قوة التذكر: {revision.retrievability}%
                            </span>
                        </div>
                    </div>

                    <div className="flex items-center gap-1">
                        <button
                            onClick={() => setShowTafsir(!showTafsir)}
                            className={`p-2 rounded-xl transition-colors ${showTafsir ? 'bg-primary-100 text-primary-600' : 'hover:bg-surface-200 dark:hover:bg-dark-300'}`}
                            title="التفسير"
                        >
                            <BookOpen className="w-5 h-5" />
                        </button>
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
                </div>

                {/* Mode Selector */}
                <div className="px-4 pb-3 flex gap-2 overflow-x-auto">
                    <ModeButton
                        active={reviewMode === REVIEW_MODES.CLASSIC}
                        onClick={() => setReviewMode(REVIEW_MODES.CLASSIC)}
                        icon={Eye}
                        label="تقليدي"
                    />
                    <ModeButton
                        active={reviewMode === REVIEW_MODES.WORD_RECALL}
                        onClick={() => setReviewMode(REVIEW_MODES.WORD_RECALL)}
                        icon={Shuffle}
                        label="كلمات مخفية"
                    />
                    <ModeButton
                        active={reviewMode === REVIEW_MODES.FULL_RECALL}
                        onClick={() => setReviewMode(REVIEW_MODES.FULL_RECALL)}
                        icon={Brain}
                        label="تذكر كامل"
                    />
                </div>
            </header>

            {/* Audio Controls Bar */}
            <div className="bg-white dark:bg-dark-400 border-b border-surface-200 dark:border-dark-300 px-4 py-2">
                <div className="flex items-center justify-between max-w-3xl mx-auto">
                    <div className="flex items-center gap-2">
                        <button
                            onClick={() => currentAudioVerseIndex > 0 && playVerse(currentAudioVerseIndex - 1)}
                            disabled={currentAudioVerseIndex === null || currentAudioVerseIndex === 0}
                            className="p-2 rounded-lg hover:bg-surface-100 dark:hover:bg-dark-300 disabled:opacity-50 transition-colors"
                        >
                            <SkipForward className="w-5 h-5 text-text-muted" />
                        </button>

                        <button
                            onClick={() => isPlaying ? pauseAudio() : playAllVerses()}
                            className="p-3 rounded-full bg-primary-500 text-white hover:bg-primary-600 transition-colors"
                        >
                            {isPlaying ? <Pause className="w-5 h-5" /> : <Play className="w-5 h-5" />}
                        </button>

                        <button
                            onClick={() => currentAudioVerseIndex !== null && currentAudioVerseIndex < verses.length - 1 && playVerse(currentAudioVerseIndex + 1)}
                            disabled={currentAudioVerseIndex === null || currentAudioVerseIndex >= verses.length - 1}
                            className="p-2 rounded-lg hover:bg-surface-100 dark:hover:bg-dark-300 disabled:opacity-50 transition-colors"
                        >
                            <SkipBack className="w-5 h-5 text-text-muted" />
                        </button>

                        <button
                            onClick={() => setRepeatMode(!repeatMode)}
                            className={`p-2 rounded-lg transition-colors ${repeatMode ? 'bg-primary-100 text-primary-600' : 'hover:bg-surface-100 dark:hover:bg-dark-300 text-text-muted'}`}
                            title="تكرار الآية"
                        >
                            <Repeat className="w-5 h-5" />
                        </button>
                    </div>

                    <div className="flex items-center gap-2">
                        {currentAudioVerseIndex !== null && (
                            <span className="text-sm text-text-muted">
                                الآية {verses[currentAudioVerseIndex]?.verse_number}
                            </span>
                        )}

                        {/* Reciter Selector */}
                        <div className="relative">
                            <button
                                onClick={() => setShowReciterSelector(!showReciterSelector)}
                                className="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-surface-100 dark:bg-dark-300 text-sm text-text-muted hover:bg-surface-200 dark:hover:bg-dark-200 transition-colors"
                            >
                                <User className="w-4 h-4" />
                                <span className="hidden sm:inline">
                                    {reciters?.find(r => r.id === selectedReciterId)?.reciter_name || 'القارئ'}
                                </span>
                            </button>

                            {showReciterSelector && (
                                <div className="absolute left-0 top-full mt-1 bg-white dark:bg-dark-400 rounded-xl shadow-lg border border-surface-200 dark:border-dark-300 py-2 min-w-[200px] z-50">
                                    {reciters?.map(reciter => (
                                        <button
                                            key={reciter.id}
                                            onClick={() => {
                                                setSelectedReciterId(reciter.id);
                                                setShowReciterSelector(false);
                                            }}
                                            className={`w-full px-4 py-2 text-right text-sm hover:bg-surface-100 dark:hover:bg-dark-300 transition-colors ${selectedReciterId === reciter.id ? 'bg-primary-50 text-primary-600' : 'text-text-primary dark:text-text-dark-primary'
                                                }`}
                                        >
                                            {reciter.reciter_name}
                                        </button>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <main className="flex-1 flex flex-col items-center justify-start p-6 overflow-y-auto">
                <AnimatePresence mode="wait">
                    {!completed ? (
                        <motion.div
                            key="reviewing"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="w-full max-w-3xl"
                        >
                            {/* Progress Indicator */}
                            {reviewMode !== REVIEW_MODES.CLASSIC && (
                                <div className="mb-4">
                                    <div className="flex items-center justify-between text-sm text-text-muted mb-2">
                                        <span>الآية {currentVerseIndex + 1} من {verses.length}</span>
                                        {hintsUsed > 0 && (
                                            <span className="text-warning">
                                                <Lightbulb className="w-4 h-4 inline ml-1" />
                                                {hintsUsed} تلميح
                                            </span>
                                        )}
                                    </div>
                                    <div className="h-2 bg-surface-200 dark:bg-dark-300 rounded-full overflow-hidden">
                                        <div
                                            className="h-full bg-primary-500 rounded-full transition-all duration-300"
                                            style={{ width: `${((currentVerseIndex + 1) / verses.length) * 100}%` }}
                                        />
                                    </div>
                                </div>
                            )}

                            {/* Instructions */}
                            <div className="text-center mb-6">
                                <p className="text-text-muted dark:text-text-dark-muted">
                                    {reviewMode === REVIEW_MODES.CLASSIC && 'حاول تذكر الآيات ثم اضغط على "إظهار النص" للتحقق'}
                                    {reviewMode === REVIEW_MODES.WORD_RECALL && 'اضغط على الكلمات المخفية لكشفها بعد محاولة تذكرها'}
                                    {reviewMode === REVIEW_MODES.FULL_RECALL && 'حاول تسميع الآية كاملة ثم اكشف للتحقق'}
                                </p>
                            </div>

                            {/* Verses Container */}
                            <div className="bg-white dark:bg-dark-400 rounded-3xl p-6 md:p-8 shadow-lg">
                                {reviewMode === REVIEW_MODES.CLASSIC ? (
                                    <ClassicMode
                                        verses={verses}
                                        showText={showText}
                                        setShowText={setShowText}
                                        revision={revision}
                                        showTafsir={showTafsir}
                                        onPlayVerse={playVerse}
                                        currentAudioVerseIndex={currentAudioVerseIndex}
                                        isPlaying={isPlaying}
                                    />
                                ) : reviewMode === REVIEW_MODES.WORD_RECALL ? (
                                    <WordRecallMode
                                        verses={verses}
                                        currentVerseIndex={currentVerseIndex}
                                        hiddenWordIndices={hiddenWordIndices}
                                        revealedWords={revealedWords}
                                        onRevealWord={handleRevealWord}
                                        showTafsir={showTafsir}
                                        onPlayVerse={playVerse}
                                        isPlaying={isPlaying}
                                        currentAudioVerseIndex={currentAudioVerseIndex}
                                    />
                                ) : (
                                    <FullRecallMode
                                        verses={verses}
                                        currentVerseIndex={currentVerseIndex}
                                        showText={showText}
                                        setShowText={setShowText}
                                        showTafsir={showTafsir}
                                        onPlayVerse={playVerse}
                                        isPlaying={isPlaying}
                                        currentAudioVerseIndex={currentAudioVerseIndex}
                                    />
                                )}

                                {/* Navigation for non-classic modes */}
                                {reviewMode !== REVIEW_MODES.CLASSIC && (
                                    <div className="flex items-center justify-between mt-6 pt-4 border-t border-surface-200 dark:border-dark-300">
                                        <Button
                                            onClick={prevVerse}
                                            disabled={currentVerseIndex === 0}
                                            variant="ghost"
                                            size="sm"
                                        >
                                            <ChevronRight className="w-5 h-5" />
                                            السابقة
                                        </Button>

                                        {currentVerseIndex === verses.length - 1 ? (
                                            <Button onClick={() => setCompleted(true)} icon={Check}>
                                                انتهيت
                                            </Button>
                                        ) : (
                                            <Button onClick={nextVerse} variant="primary" size="sm">
                                                التالية
                                                <ChevronLeft className="w-5 h-5" />
                                            </Button>
                                        )}
                                    </div>
                                )}
                            </div>

                            {/* Complete Button for Classic Mode */}
                            {reviewMode === REVIEW_MODES.CLASSIC && showText && (
                                <motion.div
                                    initial={{ opacity: 0, y: 20 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    className="mt-8 text-center"
                                >
                                    <Button onClick={() => setCompleted(true)} size="lg" icon={Check}>
                                        انتهيت من المراجعة
                                    </Button>
                                </motion.div>
                            )}
                        </motion.div>
                    ) : (
                        <RatingScreen
                            rating={rating}
                            setRating={setRating}
                            onComplete={handleComplete}
                            hintsUsed={hintsUsed}
                            perfectRecall={perfectRecall}
                            revision={revision}
                        />
                    )}
                </AnimatePresence>
            </main>
        </div>
    );
}

function ModeButton({ active, onClick, icon: Icon, label }) {
    return (
        <button
            onClick={onClick}
            className={`
                flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-all whitespace-nowrap
                ${active
                    ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30'
                    : 'bg-surface-100 dark:bg-dark-300 text-text-muted hover:bg-surface-200 dark:hover:bg-dark-200'
                }
            `}
        >
            <Icon className="w-4 h-4" />
            {label}
        </button>
    );
}

function VerseAudioButton({ verse, verseIndex, onPlayVerse, isPlaying, currentAudioVerseIndex }) {
    const isCurrentlyPlaying = isPlaying && currentAudioVerseIndex === verseIndex;

    return (
        <button
            onClick={() => onPlayVerse(verseIndex)}
            className={`p-1.5 rounded-lg transition-colors ${isCurrentlyPlaying
                ? 'bg-primary-100 text-primary-600'
                : 'hover:bg-surface-100 dark:hover:bg-dark-300 text-text-muted'
                }`}
            title="تشغيل الآية"
        >
            {isCurrentlyPlaying ? <Volume2 className="w-4 h-4" /> : <Play className="w-4 h-4" />}
        </button>
    );
}

function TafsirPanel({ tafsir }) {
    if (!tafsir) return null;

    return (
        <motion.div
            initial={{ opacity: 0, height: 0 }}
            animate={{ opacity: 1, height: 'auto' }}
            exit={{ opacity: 0, height: 0 }}
            className="mt-3 p-4 bg-accent-50 dark:bg-accent-900/20 rounded-xl border border-accent-200 dark:border-accent-800"
        >
            <div className="flex items-center gap-2 mb-2">
                <BookOpen className="w-4 h-4 text-accent-600 dark:text-accent-400" />
                <span className="text-sm font-bold text-accent-700 dark:text-accent-300">التفسير</span>
            </div>
            <p className="text-sm text-text-primary dark:text-text-dark-primary leading-relaxed">
                {tafsir}
            </p>
        </motion.div>
    );
}

function ClassicMode({ verses, showText, setShowText, revision, showTafsir, onPlayVerse, currentAudioVerseIndex, isPlaying }) {
    // Check if any verse has tafsir
    const hasTafsir = verses.some(v => v.tafsir);

    return (
        <>
            {/* Page Header */}
            <div className="mb-6 text-center border-b border-surface-200 dark:border-dark-300 pb-4">
                <span className="inline-flex items-center gap-2 text-sm text-text-muted dark:text-text-dark-muted">
                    <span>الآيات {revision.start_verse} - {revision.end_verse}</span>
                </span>
                {/* Audio Controls for all verses */}
                <div className="flex items-center justify-center gap-2 mt-2 flex-wrap">
                    {verses.map((verse, index) => (
                        <button
                            key={verse.id}
                            onClick={() => onPlayVerse(index)}
                            className={`w-7 h-7 rounded-full text-xs font-bold transition-all ${isPlaying && currentAudioVerseIndex === index
                                ? 'bg-primary-500 text-white scale-110'
                                : 'bg-surface-100 dark:bg-dark-300 text-text-muted hover:bg-primary-100 hover:text-primary-600'
                                }`}
                            title={`تشغيل الآية ${verse.verse_number}`}
                        >
                            {verse.verse_number}
                        </button>
                    ))}
                </div>
            </div>

            {/* Quran Page Style - Continuous Text */}
            <motion.div
                animate={{ filter: showText ? 'blur(0px)' : 'blur(8px)' }}
                className="font-amiri text-2xl md:text-3xl leading-[2.5] md:leading-[2.8] text-text-primary dark:text-text-dark-primary text-center rtl"
                dir="rtl"
            >
                {verses.map((verse, index) => (
                    <span key={verse.id} className="inline">
                        {verse.text}
                        {' '}
                        <span
                            className="inline-flex items-center justify-center w-7 h-7 md:w-8 md:h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 text-sm font-bold text-primary-600 dark:text-primary-400 mx-1 align-middle"
                        >
                            {verse.verse_number}
                        </span>
                        {' '}
                    </span>
                ))}
            </motion.div>

            {/* Full Tafsir Section - All verses together */}
            <AnimatePresence>
                {showTafsir && showText && hasTafsir && (
                    <motion.div
                        initial={{ opacity: 0, y: 10 }}
                        animate={{ opacity: 1, y: 0 }}
                        exit={{ opacity: 0, y: 10 }}
                        className="mt-8 p-5 bg-accent-50 dark:bg-accent-900/20 rounded-2xl border border-accent-200 dark:border-accent-800"
                    >
                        <div className="flex items-center gap-2 mb-4 pb-3 border-b border-accent-200 dark:border-accent-700">
                            <BookOpen className="w-5 h-5 text-accent-600 dark:text-accent-400" />
                            <span className="text-base font-bold text-accent-700 dark:text-accent-300">
                                التفسير
                            </span>
                        </div>
                        <div className="space-y-4">
                            {verses.map((verse) => (
                                verse.tafsir && (
                                    <div key={verse.id} className="pb-4 border-b border-accent-100 dark:border-accent-800 last:border-0 last:pb-0">
                                        <div className="flex items-center gap-2 mb-2">
                                            <span className="inline-flex items-center justify-center w-6 h-6 rounded-full bg-accent-200 dark:bg-accent-800 text-xs font-bold text-accent-700 dark:text-accent-300">
                                                {verse.verse_number}
                                            </span>
                                            <span className="text-xs text-accent-600 dark:text-accent-400 font-medium">
                                                الآية {verse.verse_number}
                                            </span>
                                        </div>
                                        <p className="text-sm text-text-primary dark:text-text-dark-primary leading-relaxed pr-8">
                                            {verse.tafsir}
                                        </p>
                                    </div>
                                )
                            ))}
                        </div>
                    </motion.div>
                )}
            </AnimatePresence>

            {!showText && (
                <div className="mt-8 text-center">
                    <Button onClick={() => setShowText(true)} variant="outline" icon={Eye}>
                        إظهار النص
                    </Button>
                </div>
            )}
        </>
    );
}

function WordRecallMode({ verses, currentVerseIndex, hiddenWordIndices, revealedWords, onRevealWord, showTafsir, onPlayVerse, isPlaying, currentAudioVerseIndex }) {
    const verse = verses[currentVerseIndex];
    const words = verse.words || [];
    const hiddenIndices = hiddenWordIndices[currentVerseIndex] || [];

    // Filter function to check if a word is a real Arabic word (not a number or special character)
    const isRealWord = (text) => {
        if (!text) return false;
        // Remove spaces and check if it has Arabic letters
        const cleanText = text.trim();
        // Check if it's mostly Arabic characters (not numbers or special chars)
        const arabicPattern = /[\u0600-\u06FF]/;
        const numberPattern = /^[\d\u0660-\u0669]+$/; // Arabic and Western numerals
        return arabicPattern.test(cleanText) && !numberPattern.test(cleanText) && cleanText.length > 1;
    };

    // Get display words and filter out non-words
    const rawWords = words.length > 0
        ? words
        : verse.text.split(' ').map((text, i) => ({ id: i, text: text.trim(), position: i }));

    // Filter to only real words for display
    const displayWords = rawWords.filter(word => isRealWord(word.text));

    return (
        <div className="text-center">
            <div className="flex items-center justify-center gap-2 mb-4">
                <span className="inline-flex items-center justify-center w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 text-sm font-bold text-primary-600 dark:text-primary-400">
                    {verse.verse_number}
                </span>
                <VerseAudioButton
                    verse={verse}
                    verseIndex={currentVerseIndex}
                    onPlayVerse={onPlayVerse}
                    isPlaying={isPlaying}
                    currentAudioVerseIndex={currentAudioVerseIndex}
                />
            </div>

            <div className="font-amiri text-2xl md:text-3xl leading-loose text-text-primary dark:text-text-dark-primary flex flex-wrap justify-center gap-2 rtl">
                {displayWords.map((word, displayIndex) => {
                    // Use the original index from the rawWords for hidden check
                    const originalIndex = rawWords.findIndex(w => w.id === word.id);
                    const isHidden = hiddenIndices.includes(originalIndex);
                    const isRevealed = revealedWords[`${currentVerseIndex}-${originalIndex}`];

                    if (!isHidden) {
                        return (
                            <span key={word.id || displayIndex} className="inline-block">
                                {word.text}
                            </span>
                        );
                    }

                    return (
                        <motion.button
                            key={word.id || displayIndex}
                            onClick={() => onRevealWord(currentVerseIndex, originalIndex)}
                            whileHover={{ scale: 1.05 }}
                            whileTap={{ scale: 0.95 }}
                            className={`
                                inline-block px-3 py-1 rounded-lg transition-all
                                ${isRevealed
                                    ? 'bg-success/20 text-success-700 dark:text-success-300'
                                    : 'bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 cursor-pointer hover:bg-primary-200'
                                }
                            `}
                        >
                            {isRevealed ? word.text : '؟؟؟'}
                        </motion.button>
                    );
                })}
            </div>

            <p className="mt-6 text-sm text-text-muted">
                اضغط على الكلمات المخفية للكشف عنها
            </p>

            <AnimatePresence>
                {showTafsir && <TafsirPanel tafsir={verse.tafsir} />}
            </AnimatePresence>
        </div>
    );
}

function FullRecallMode({ verses, currentVerseIndex, showText, setShowText, showTafsir, onPlayVerse, isPlaying, currentAudioVerseIndex }) {
    const verse = verses[currentVerseIndex];

    return (
        <div className="text-center">
            <div className="flex items-center justify-center gap-2 mb-6">
                <span className="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/30 text-lg font-bold text-primary-600 dark:text-primary-400">
                    {verse.verse_number}
                </span>
                <VerseAudioButton
                    verse={verse}
                    verseIndex={currentVerseIndex}
                    onPlayVerse={onPlayVerse}
                    isPlaying={isPlaying}
                    currentAudioVerseIndex={currentAudioVerseIndex}
                />
            </div>

            <AnimatePresence mode="wait">
                {!showText ? (
                    <motion.div
                        key="hidden"
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        className="space-y-6"
                    >
                        <div className="p-8 bg-surface-100 dark:bg-dark-300 rounded-2xl border-2 border-dashed border-surface-300 dark:border-dark-200">
                            <Brain className="w-12 h-12 mx-auto text-primary-400 mb-4" />
                            <p className="text-lg text-text-muted dark:text-text-dark-muted">
                                حاول تسميع الآية رقم {verse.verse_number} كاملة
                            </p>
                            <p className="text-sm text-text-muted mt-2">
                                يمكنك الاستماع للآية للمساعدة
                            </p>
                        </div>

                        <Button onClick={() => setShowText(true)} variant="outline" icon={Eye}>
                            كشف الآية للتحقق
                        </Button>
                    </motion.div>
                ) : (
                    <motion.div
                        key="revealed"
                        initial={{ opacity: 0, scale: 0.95 }}
                        animate={{ opacity: 1, scale: 1 }}
                        className="space-y-4"
                    >
                        <p className="font-amiri text-2xl md:text-3xl leading-loose text-text-primary dark:text-text-dark-primary">
                            {verse.text}
                        </p>

                        <Button onClick={() => setShowText(false)} variant="ghost" size="sm" icon={EyeOff}>
                            إخفاء مرة أخرى
                        </Button>

                        <AnimatePresence>
                            {showTafsir && <TafsirPanel tafsir={verse.tafsir} />}
                        </AnimatePresence>
                    </motion.div>
                )}
            </AnimatePresence>
        </div>
    );
}

function RatingScreen({ rating, setRating, onComplete, hintsUsed, perfectRecall, revision }) {
    const getRatingDescription = (r) => {
        switch (r) {
            case 1: return 'لم أتذكر شيئاً - سأراجع أكثر';
            case 2: return 'تذكرت قليلاً مع صعوبة';
            case 3: return 'تذكرت مع بعض الأخطاء';
            case 4: return 'تذكرت جيداً مع أخطاء بسيطة';
            case 5: return 'تذكرت بشكل ممتاز!';
            default: return '';
        }
    };

    return (
        <motion.div
            key="rating"
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
            className="w-full max-w-md text-center"
        >
            <div className="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center">
                <Brain className="w-12 h-12 text-white" />
            </div>

            <h2 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary mb-2">
                أحسنت!
            </h2>

            <p className="text-text-muted dark:text-text-dark-muted mb-4">
                كيف كانت جودة تذكرك؟
            </p>

            <div className="bg-surface-100 dark:bg-dark-300 rounded-xl p-4 mb-6">
                <div className="flex items-center justify-between text-sm">
                    <span className="text-text-muted">التلميحات المستخدمة:</span>
                    <span className={hintsUsed === 0 ? 'text-success font-bold' : 'text-warning font-bold'}>
                        {hintsUsed === 0 ? 'لا شيء - ممتاز!' : `${hintsUsed} تلميح`}
                    </span>
                </div>
                <div className="flex items-center justify-between text-sm mt-2">
                    <span className="text-text-muted">حالة الذاكرة:</span>
                    <span className="font-bold text-primary-600 dark:text-primary-400">
                        {revision.memory_state_ar}
                    </span>
                </div>
            </div>

            <div className="flex justify-center gap-3 mb-4">
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

            <p className="text-sm text-text-muted dark:text-text-dark-muted h-6 mb-6">
                {getRatingDescription(rating)}
            </p>

            <div className="grid grid-cols-5 gap-1 mb-8 text-xs text-text-muted">
                <span>ضعيف جداً</span>
                <span>ضعيف</span>
                <span>متوسط</span>
                <span>جيد</span>
                <span>ممتاز</span>
            </div>

            <Button
                onClick={onComplete}
                disabled={!rating}
                className="w-full"
                size="lg"
                icon={Check}
            >
                حفظ التقييم
            </Button>
        </motion.div>
    );
}

export default function RevisionShow({ revision, verses, reciters, preferredReciterId }) {
    return (
        <ThemeProvider>
            <Head title={`مراجعة سورة ${revision.chapter_name}`} />
            <RevisionContent
                revision={revision}
                verses={verses}
                reciters={reciters}
                preferredReciterId={preferredReciterId}
            />
        </ThemeProvider>
    );
}
