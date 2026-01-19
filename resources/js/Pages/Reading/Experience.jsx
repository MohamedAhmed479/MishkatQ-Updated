import React, { useState, useEffect, useRef, useCallback } from 'react';
import { Head, router } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import axios from 'axios';
import {
    ChevronRight,
    ChevronLeft,
    Settings,
    X,
    Play,
    Pause,
    SkipForward,
    SkipBack,
    Volume2,
    VolumeX,
    BookOpen,
    BookMarked,
    Type,
    Minus,
    Plus,
    Check,
    Sun,
    Moon,
    Palette,
    MessageSquare,
    ArrowRight,
    Maximize2,
    Minimize2,
    Home,
    Layout,
    List,
    Edit3,
    Save,
    Trash2,
} from 'lucide-react';

// Theme configurations
const THEMES = {
    classic: {
        id: 'classic',
        name: 'كلاسيكي',
        bg: 'bg-amber-50',
        text: 'text-amber-950',
        verseBg: 'bg-amber-100/50',
        verseHover: 'hover:bg-amber-100',
        activeVerse: 'bg-amber-200',
        headerBg: 'bg-amber-100/80 backdrop-blur-sm',
        footerBg: 'bg-amber-100/80 backdrop-blur-sm',
        border: 'border-amber-200',
        accent: 'text-amber-700',
        button: 'bg-amber-200 hover:bg-amber-300 text-amber-900',
    },
    night: {
        id: 'night',
        name: 'ليلي',
        bg: 'bg-gray-950',
        text: 'text-gray-100',
        verseBg: 'bg-gray-900/50',
        verseHover: 'hover:bg-gray-800',
        activeVerse: 'bg-gray-800',
        headerBg: 'bg-gray-900/80 backdrop-blur-sm',
        footerBg: 'bg-gray-900/80 backdrop-blur-sm',
        border: 'border-gray-800',
        accent: 'text-gray-400',
        button: 'bg-gray-800 hover:bg-gray-700 text-gray-100',
    },
    soft_blue: {
        id: 'soft_blue',
        name: 'أزرق هادئ',
        bg: 'bg-blue-50',
        text: 'text-blue-950',
        verseBg: 'bg-blue-100/50',
        verseHover: 'hover:bg-blue-100',
        activeVerse: 'bg-blue-200',
        headerBg: 'bg-blue-100/80 backdrop-blur-sm',
        footerBg: 'bg-blue-100/80 backdrop-blur-sm',
        border: 'border-blue-200',
        accent: 'text-blue-700',
        button: 'bg-blue-200 hover:bg-blue-300 text-blue-900',
    },
    mint: {
        id: 'mint',
        name: 'نعناعي',
        bg: 'bg-emerald-50',
        text: 'text-emerald-950',
        verseBg: 'bg-emerald-100/50',
        verseHover: 'hover:bg-emerald-100',
        activeVerse: 'bg-emerald-200',
        headerBg: 'bg-emerald-100/80 backdrop-blur-sm',
        footerBg: 'bg-emerald-100/80 backdrop-blur-sm',
        border: 'border-emerald-200',
        accent: 'text-emerald-700',
        button: 'bg-emerald-200 hover:bg-emerald-300 text-emerald-900',
    },
};

export default function ReadingExperience({
    plan,
    verses,
    chapter,
    reciters,
    tafsirs,
    currentPage,
    startPage,
    endPage,
    totalPages = 604,
    todayProgress,
}) {
    const planData = plan?.data?.plan || plan || {};
    const dailyWird = plan?.data?.daily_wird || { start_page: startPage, end_page: endPage };
    const settings = planData.settings || {};

    // Calculate today's reading range
    const todayStartPage = dailyWird.start_page || startPage || planData.current_page || 1;
    const todayEndPage = dailyWird.end_page || endPage || (todayStartPage + (planData.pages_per_day || 1) - 1);

    // State
    const [theme, setTheme] = useState(THEMES[settings.theme] || THEMES.classic);
    const [readingMode, setReadingMode] = useState(planData.reading_mode || 'hadr');
    const [fontSize, setFontSize] = useState(settings.font_size || 28);
    const [isSettingsOpen, setIsSettingsOpen] = useState(false);
    const [isFullscreen, setIsFullscreen] = useState(false);
    const [showTafsir, setShowTafsir] = useState(readingMode === 'tadabbur');
    const [viewMode, setViewMode] = useState(settings.view_mode || 'page'); // 'page' or 'verse'
    const [scriptType, setScriptType] = useState(settings.script_type || 'uthmani'); // 'uthmani' or 'imlaei'

    // Audio state
    const [isPlaying, setIsPlaying] = useState(false);
    const [currentVerseIndex, setCurrentVerseIndex] = useState(-1);
    const [selectedReciter, setSelectedReciter] = useState(settings.reciter_id || reciters?.[0]?.id);
    const [isMuted, setIsMuted] = useState(false);
    const audioRef = useRef(null);

    // Tafsir state
    const [selectedVerse, setSelectedVerse] = useState(null);
    const [tafsirContent, setTafsirContent] = useState(null);
    const [isTafsirLoading, setIsTafsirLoading] = useState(false);

    // Page tracking
    const [page, setPage] = useState(currentPage || todayStartPage || 1);
    const [isMarkingProgress, setIsMarkingProgress] = useState(false);
    const [controlsVisible, setControlsVisible] = useState(false);
    const [noteVerse, setNoteVerse] = useState(null);
    const [noteText, setNoteText] = useState('');
    const [isSavingNote, setIsSavingNote] = useState(false);
    const [isDeletingNote, setIsDeletingNote] = useState(false);
    const [verseNotes, setVerseNotes] = useState(() =>
        Object.fromEntries((verses || []).map(v => [v.id, v.user_note || '']))
    );
    const NOTES_STORAGE_KEY = 'mishkat-reading-notes';

    // Swipe navigation (mobile)
    const touchStartRef = useRef({ x: 0, y: 0, t: 0 });
    const touchMovedRef = useRef(false);

    // Check if current page is the last page of today's wird
    const isLastPageOfToday = page >= todayEndPage;

    // Update page when currentPage prop changes
    useEffect(() => {
        setPage(currentPage || todayStartPage || 1);
    }, [currentPage, todayStartPage]);

    // Initialize notes from server + cache
    useEffect(() => {
        const baseNotes = Object.fromEntries((verses || []).map(v => [v.id, v.user_note || '']));
        try {
            const cached = JSON.parse(localStorage.getItem(NOTES_STORAGE_KEY) || '{}');
            if (cached && typeof cached === 'object') {
                setVerseNotes({ ...baseNotes, ...cached });
            } else {
                setVerseNotes(baseNotes);
            }
        } catch {
            setVerseNotes(baseNotes);
        }
    }, [verses]);

    const updateCache = (notesMap) => {
        try {
            localStorage.setItem(NOTES_STORAGE_KEY, JSON.stringify(notesMap));
        } catch (e) {
            console.warn('Failed to cache notes', e);
        }
    };

    // Fetch latest notes from Quran API in a single batch to keep in sync
    useEffect(() => {
        if (!verses?.length) return;
        let cancelled = false;
        const fetchNotes = async () => {
            try {
                const verseIds = verses.map(v => v.id);
                const res = await axios.post('/app/quran/notes/batch', { verse_ids: verseIds });
                const notes = res.data?.notes || {};
                if (cancelled) return;
                setVerseNotes((prev) => {
                    const updated = { ...prev };
                    verseIds.forEach((id) => {
                        const n = notes[id] ?? prev[id] ?? '';
                        updated[id] = n || '';
                    });
                    updateCache(updated);
                    return updated;
                });
            } catch (error) {
                console.error('Error fetching verse notes batch:', error);
            }
        };
        fetchNotes();
        return () => { cancelled = true; };
    }, [verses]);

    // Refs
    const containerRef = useRef(null);
    const verseRefs = useRef({});

    // Get current theme
    const t = theme;

    // Initialize audio
    useEffect(() => {
        audioRef.current = new Audio();
        audioRef.current.preload = 'auto';

        return () => {
            if (audioRef.current) {
                audioRef.current.pause();
                audioRef.current.src = '';
            }
        };
    }, []);

    // Handle audio playback
    useEffect(() => {
        if (!audioRef.current || !verses?.length) return;

        const audio = audioRef.current;

        const handleEnded = () => {
            if (currentVerseIndex < verses.length - 1) {
                setCurrentVerseIndex(prev => prev + 1);
            } else {
                setIsPlaying(false);
                setCurrentVerseIndex(-1);
            }
        };

        audio.addEventListener('ended', handleEnded);
        return () => audio.removeEventListener('ended', handleEnded);
    }, [currentVerseIndex, verses]);

    useEffect(() => {
        if (!audioRef.current || currentVerseIndex === -1 || !isPlaying) return;

        const verse = verses?.[currentVerseIndex];
        if (!verse) return;

        const recitation = verse?.recitations?.find(r => r.reciter_id === selectedReciter)
            || verse?.recitations?.[0];

        if (recitation) {
            // Use full_audio_url if available, otherwise construct it
            let audioUrl = recitation.full_audio_url || recitation.audio_url;

            // If it's a relative URL, prepend the base URL
            if (audioUrl && !audioUrl.startsWith('http://') && !audioUrl.startsWith('https://')) {
                audioUrl = `https://verses.quran.foundation/${audioUrl.replace(/^\//, '')}`;
            }

            if (audioUrl) {
                console.log('Playing audio:', audioUrl, 'for verse:', verse.verse_number);
                audioRef.current.src = audioUrl;
                audioRef.current.muted = isMuted;
                audioRef.current.play().catch((error) => {
                    console.error('Error playing audio:', error, 'URL:', audioUrl);
                    setIsPlaying(false);
                });

                // Auto-scroll to verse
                if (verseRefs.current[verse.id]) {
                    verseRefs.current[verse.id].scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            } else {
                console.warn('No audio URL found for verse:', verse.id, 'Recitation:', recitation);
                setIsPlaying(false);
            }
        } else {
            console.warn('No recitation found for verse:', verse.id, 'Selected reciter:', selectedReciter);
            setIsPlaying(false);
        }
    }, [currentVerseIndex, isPlaying, selectedReciter, isMuted, verses]);

    // Toggle play/pause
    const togglePlay = useCallback(() => {
        if (isPlaying) {
            audioRef.current?.pause();
            setIsPlaying(false);
        } else {
            if (currentVerseIndex === -1) {
                setCurrentVerseIndex(0);
            }
            setIsPlaying(true);
        }
    }, [isPlaying, currentVerseIndex]);

    // Fetch tafsir for a verse
    const fetchTafsir = async (verse) => {
        setSelectedVerse(verse);
        setIsTafsirLoading(true);
        try {
            const response = await axios.get(`/app/quran/verse/${verse.id}/tafsir`);
            setTafsirContent(response.data?.tafsir || 'لا يوجد تفسير متاح');
        } catch (error) {
            console.error('Error fetching tafsir:', error);
            setTafsirContent('حدث خطأ في جلب التفسير');
        } finally {
            setIsTafsirLoading(false);
        }
    };

    // Mark progress - Complete today's wird
    const markProgress = async () => {
        if (!planData.id || isMarkingProgress) return;

        setIsMarkingProgress(true);
        try {
            await axios.post(`/app/reading/api/plans/${planData.id}/progress`, {
                start_page: todayStartPage,
                end_page: todayEndPage,
                reading_mode: readingMode,
            });

            // Show success feedback (haptic if supported)
            if (navigator.vibrate) {
                navigator.vibrate([100, 50, 100]);
            }

            // Show success message
            alert('🎉 مبارك! تم إتمام ورد اليوم بنجاح');

            router.visit('/app/reading');
        } catch (error) {
            console.error('Failed to mark progress:', error);
            alert('حدث خطأ أثناء حفظ التقدم. يرجى المحاولة مرة أخرى.');
        } finally {
            setIsMarkingProgress(false);
        }
    };

    // Navigate pages (only within today's wird range)
    const goToPage = (newPage) => {
        const validPage = Math.max(todayStartPage, Math.min(todayEndPage, newPage));
        setPage(validPage);
        router.visit(`/app/reading/experience/${planData.id}?page=${validPage}`, {
            preserveState: true,
            preserveScroll: false,
        });
    };

    const openNoteModal = (verse) => {
        setNoteVerse(verse);
        setNoteText(verseNotes[verse.id] || '');
    };

    const handleSaveNote = async () => {
        if (!noteVerse) return;
        setIsSavingNote(true);
        try {
            await axios.post(`/app/quran/verse/${noteVerse.id}/note`, { note: noteText });
            setVerseNotes(prev => {
                const next = { ...prev, [noteVerse.id]: noteText };
                updateCache(next);
                return next;
            });
            setNoteVerse(null);
        } catch (error) {
            console.error('Error saving note:', error);
            alert('حدث خطأ أثناء حفظ الخاطرة');
        } finally {
            setIsSavingNote(false);
        }
    };

    const handleDeleteNote = async () => {
        if (!noteVerse) return;
        setIsDeletingNote(true);
        try {
            await axios.delete(`/app/quran/verse/${noteVerse.id}/note`);
            setVerseNotes(prev => {
                const next = { ...prev, [noteVerse.id]: '' };
                updateCache(next);
                return next;
            });
            setNoteVerse({ ...noteVerse, user_note: '' });
            setNoteText('');
            setNoteVerse(null);
        } catch (error) {
            console.error('Error deleting note:', error);
            alert('حدث خطأ أثناء حذف الخاطرة');
        } finally {
            setIsDeletingNote(false);
        }
    };

    const handleTouchStart = (e) => {
        const touch = e.touches?.[0];
        if (!touch) return;
        touchMovedRef.current = false;
        touchStartRef.current = { x: touch.clientX, y: touch.clientY, t: Date.now() };
    };

    const handleTouchMove = () => {
        touchMovedRef.current = true;
    };

    const handleTouchEnd = (e) => {
        const touch = e.changedTouches?.[0];
        if (!touch) return;

        const start = touchStartRef.current;
        const dx = touch.clientX - start.x;
        const dy = touch.clientY - start.y;
        const dt = Date.now() - start.t;

        // Ignore long presses / slow drags
        if (dt > 900) return;

        // Prefer vertical scroll if user moved mostly vertically
        if (Math.abs(dy) > Math.abs(dx) * 0.9) return;

        const width = window.innerWidth || 360;
        const minDistance = 60;
        const edgeRight = width * 0.65;
        const edgeLeft = width * 0.35;

        // Swipe from right edge -> next page
        if (start.x >= edgeRight && dx <= -minDistance) {
            goToPage(page + 1);
            return;
        }

        // Swipe from left edge -> previous page
        if (start.x <= edgeLeft && dx >= minDistance) {
            goToPage(page - 1);
        }
    };

    // Toggle fullscreen
    const toggleFullscreen = () => {
        if (!document.fullscreenElement) {
            containerRef.current?.requestFullscreen();
            setIsFullscreen(true);
        } else {
            document.exitFullscreen();
            setIsFullscreen(false);
        }
    };

    // Change theme
    const changeTheme = (themeId) => {
        setTheme(THEMES[themeId]);
        // Save preference
        axios.patch(`/app/reading/api/plans/${planData.id}/settings`, {
            theme: themeId,
        }).catch(console.error);
    };

    // Toggle reading mode
    const toggleReadingMode = () => {
        const newMode = readingMode === 'hadr' ? 'tadabbur' : 'hadr';
        setReadingMode(newMode);
        setShowTafsir(newMode === 'tadabbur');
    };

    return (
        <div
            ref={containerRef}
            className={`
                min-h-screen ${t.bg} ${t.text} transition-colors duration-300
                ${isFullscreen ? 'h-screen overflow-y-auto touch-pan-y' : ''}
            `}
            onTouchStart={handleTouchStart}
            onTouchMove={handleTouchMove}
            onTouchEnd={handleTouchEnd}
        >
            <Head title={`قراءة - صفحة ${page}`} />

            {/* Header (hidden until user toggles) */}
            <header className={`fixed top-0 left-0 right-0 z-50 ${t.headerBg} border-b ${t.border} ${controlsVisible ? '' : 'hidden'}`}>
                <div className="flex items-center justify-between px-4 py-3">
                    <div className="flex items-center gap-2">
                        <button
                            onClick={() => router.visit('/app/reading')}
                            className={`p-2 rounded-xl ${t.button} transition`}
                        >
                            <Home className="w-5 h-5" />
                        </button>
                        <button
                            onClick={toggleFullscreen}
                            className={`p-2 rounded-xl ${t.button} transition`}
                        >
                            {isFullscreen ? <Minimize2 className="w-5 h-5" /> : <Maximize2 className="w-5 h-5" />}
                        </button>
                    </div>

                    <div className="text-center flex-1">
                        <h1 className="font-bold">{chapter?.name_ar || 'القرآن الكريم'}</h1>
                        <p className={`text-sm ${t.accent}`}>
                            صفحة {page} من {todayEndPage}
                        </p>
                        {/* Progress Bar */}
                        <div className="mt-2 mx-auto max-w-xs">
                            <div className={`h-1.5 rounded-full ${t.verseBg} overflow-hidden`}>
                                <motion.div
                                    className="h-full bg-gradient-to-r from-primary-400 to-primary-600"
                                    initial={{ width: 0 }}
                                    animate={{ width: `${((page - todayStartPage + 1) / (todayEndPage - todayStartPage + 1)) * 100}%` }}
                                    transition={{ duration: 0.3 }}
                                />
                            </div>
                        </div>
                        {isLastPageOfToday && (
                            <p className={`text-xs mt-1 font-medium text-green-500 dark:text-green-400`}>
                                ✓ آخر صفحة من ورد اليوم
                            </p>
                        )}
                    </div>

                    <div className="flex items-center gap-2">
                        {/* View Mode Toggle */}
                        <button
                            onClick={() => {
                                const newMode = viewMode === 'page' ? 'verse' : 'page';
                                setViewMode(newMode);
                                axios.patch(`/app/reading/api/plans/${planData.id}/settings`, {
                                    view_mode: newMode,
                                }).catch(console.error);
                            }}
                            className={`p-2 rounded-xl ${t.button} transition`}
                            title={viewMode === 'page' ? 'عرض صفحة كاملة' : 'عرض آية آية'}
                        >
                            {viewMode === 'page' ? <Layout className="w-5 h-5" /> : <List className="w-5 h-5" />}
                        </button>
                        <button
                            onClick={() => setIsSettingsOpen(true)}
                            className={`p-2 rounded-xl ${t.button} transition`}
                        >
                            <Settings className="w-5 h-5" />
                        </button>
                    </div>
                </div>

                {/* Mode Toggle */}
                <div className="flex items-center justify-center gap-2 pb-3">
                    <button
                        onClick={toggleReadingMode}
                        className={`flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium transition ${readingMode === 'hadr'
                            ? 'bg-primary-500 text-white'
                            : t.button
                            }`}
                    >
                        <BookOpen className="w-4 h-4" />
                        قراءة سريعة
                    </button>
                    <button
                        onClick={toggleReadingMode}
                        className={`flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium transition ${readingMode === 'tadabbur'
                            ? 'bg-accent-500 text-white'
                            : t.button
                            }`}
                    >
                        <BookMarked className="w-4 h-4" />
                        تدبر
                    </button>
                </div>
            </header>

            {/* Main Content */}
            <main
                className={`
                    ${isFullscreen ? 'pt-6 pb-20 px-2 max-w-none' : 'pt-28 pb-40 px-4 max-w-3xl'}
                    mx-auto
                `}
            >
                {viewMode === 'page' ? (
                    // Page View - All verses in one continuous flow
                    <PageView
                        verses={verses}
                        theme={t}
                        fontSize={fontSize}
                        scriptType={scriptType}
                        isActive={currentVerseIndex}
                        readingMode={readingMode}
                        showTafsir={showTafsir}
                        onTafsirClick={fetchTafsir}
                        verseRefs={verseRefs}
                        verseNotes={verseNotes}
                        onOpenNote={openNoteModal}
                    />
                ) : (
                    // Verse View - Each verse in separate card
                    <div className="space-y-6">
                        {verses?.map((verse, index) => (
                            <VerseCard
                                key={verse.id}
                                verse={verse}
                                index={index}
                                theme={t}
                                fontSize={fontSize}
                                scriptType={scriptType}
                                isActive={currentVerseIndex === index}
                                readingMode={readingMode}
                                showTafsir={showTafsir}
                                onTafsirClick={() => fetchTafsir(verse)}
                                verseNotes={verseNotes}
                                onOpenNote={openNoteModal}
                                ref={(el) => verseRefs.current[verse.id] = el}
                            />
                        ))}
                    </div>
                )}
            </main>

            {/* Tafsir Panel (Tadabbur Mode) */}
            <AnimatePresence>
                {selectedVerse && showTafsir && (
                    <TafsirPanel
                        verse={selectedVerse}
                        tafsir={tafsirContent}
                        isLoading={isTafsirLoading}
                        theme={t}
                        onClose={() => setSelectedVerse(null)}
                    />
                )}
            </AnimatePresence>

            {/* Footer Controls (hidden until user toggles) */}
            <footer className={`fixed bottom-0 left-0 right-0 z-50 ${t.footerBg} border-t ${t.border} ${controlsVisible ? '' : 'hidden'}`}>
                {/* Audio Controls */}
                <div className="flex items-center justify-center gap-4 py-3 border-b border-opacity-50">
                    <button
                        onClick={() => setCurrentVerseIndex(Math.max(0, currentVerseIndex - 1))}
                        className={`p-2 rounded-full ${t.button} transition`}
                        disabled={currentVerseIndex <= 0}
                    >
                        <SkipForward className="w-5 h-5" />
                    </button>

                    <button
                        onClick={togglePlay}
                        className="p-4 rounded-full bg-primary-500 text-white shadow-lg hover:bg-primary-600 transition"
                    >
                        {isPlaying ? <Pause className="w-6 h-6" /> : <Play className="w-6 h-6 mr-0.5" />}
                    </button>

                    <button
                        onClick={() => setCurrentVerseIndex(Math.min(verses?.length - 1 || 0, currentVerseIndex + 1))}
                        className={`p-2 rounded-full ${t.button} transition`}
                        disabled={currentVerseIndex >= (verses?.length - 1 || 0)}
                    >
                        <SkipBack className="w-5 h-5" />
                    </button>

                    <button
                        onClick={() => setIsMuted(!isMuted)}
                        className={`p-2 rounded-full ${t.button} transition`}
                    >
                        {isMuted ? <VolumeX className="w-5 h-5" /> : <Volume2 className="w-5 h-5" />}
                    </button>
                </div>

                {/* Page Navigation & Complete Button */}
                <div className="flex flex-col gap-3 px-4 py-3">
                    {/* Page Navigation */}
                    <div className="flex items-center justify-between">
                        <button
                            onClick={() => goToPage(page - 1)}
                            className={`p-3 rounded-xl ${t.button} transition`}
                            disabled={page <= todayStartPage}
                        >
                            <ChevronLeft className="w-5 h-5" />
                        </button>

                        {/* Page Indicator */}
                        <div className="text-center">
                            <p className={`text-sm font-medium ${t.text}`}>
                                صفحة {page} من {todayEndPage}
                            </p>
                            <div className="flex items-center gap-2 mt-1">
                                <div className={`h-1.5 rounded-full ${t.verseBg}`} style={{ width: '100px' }}>
                                    <div
                                        className={`h-full rounded-full bg-primary-500 transition-all duration-300`}
                                        style={{ width: `${((page - todayStartPage + 1) / (todayEndPage - todayStartPage + 1)) * 100}%` }}
                                    />
                                </div>
                            </div>
                        </div>

                        <button
                            onClick={() => goToPage(page + 1)}
                            className={`p-3 rounded-xl ${t.button} transition`}
                            disabled={page >= todayEndPage}
                        >
                            <ChevronRight className="w-5 h-5" />
                        </button>
                    </div>

                    {/* Complete Button - Show prominently on last page */}
                    {isLastPageOfToday && (
                        <motion.div
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            className="w-full"
                        >
                            <motion.button
                                whileHover={{ scale: 1.02 }}
                                whileTap={{ scale: 0.98 }}
                                onClick={markProgress}
                                disabled={isMarkingProgress}
                                className="w-full flex items-center justify-center gap-3 px-6 py-4 rounded-xl bg-gradient-to-r from-green-500 to-green-600 text-white font-bold text-lg shadow-2xl"
                            >
                                <Check className="w-6 h-6" />
                                {isMarkingProgress ? 'جاري الحفظ...' : 'إتمام ورد اليوم 🎉'}
                            </motion.button>
                            <p className={`text-center text-xs mt-2 ${t.accent} opacity-75`}>
                                تم قراءة جميع صفحات ورد اليوم
                            </p>
                        </motion.div>
                    )}
                </div>
            </footer>

            {/* Settings Modal */}
            <AnimatePresence>
                {isSettingsOpen && (
                    <SettingsModal
                        theme={t}
                        themes={THEMES}
                        currentTheme={theme.id}
                        fontSize={fontSize}
                        scriptType={scriptType}
                        selectedReciter={selectedReciter}
                        reciters={reciters}
                        onThemeChange={changeTheme}
                        onFontSizeChange={setFontSize}
                        onScriptTypeChange={(type) => {
                            setScriptType(type);
                            axios.patch(`/app/reading/api/plans/${planData.id}/settings`, {
                                script_type: type,
                            }).catch(console.error);
                        }}
                        onReciterChange={setSelectedReciter}
                        onClose={() => setIsSettingsOpen(false)}
                    />
                )}
            </AnimatePresence>

            {/* Notes Modal */}
            <AnimatePresence>
                {noteVerse && (
                    <NoteModal
                        verse={noteVerse}
                        noteText={noteText}
                        onChange={setNoteText}
                        onSave={handleSaveNote}
                        onDelete={handleDeleteNote}
                        onClose={() => setNoteVerse(null)}
                        isSaving={isSavingNote}
                        isDeleting={isDeletingNote}
                        theme={t}
                    />
                )}
            </AnimatePresence>
            {/* Toggle Controls Button */}
            <button
                onClick={() => setControlsVisible(v => !v)}
                className="
                    fixed bottom-4 right-4 z-[60]
                    p-3 rounded-full shadow-lg bg-primary-500 text-white
                    hover:bg-primary-600 transition
                "
                title={controlsVisible ? 'إخفاء الأدوات' : 'إظهار الأدوات'}
            >
                {controlsVisible ? <X className="w-5 h-5" /> : <Settings className="w-5 h-5" />}
            </button>
        </div>
    );
}

// Page View Component - Continuous flow of verses
function PageView({ verses, theme: t, fontSize, scriptType, isActive, readingMode, showTafsir, onTafsirClick, verseRefs, verseNotes, onOpenNote }) {
    return (
        <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            className={`
                transition-all duration-300
                ${t.verseBg}
                rounded-none md:rounded-2xl
                px-3 md:px-8 py-4 md:py-8
            `}
        >
            <div
                className="font-amiri text-center rtl"
                style={{ fontSize: `${Math.max(22, fontSize)}px`, lineHeight: '2.8' }}
                dir="rtl"
            >
                {verses?.map((verse, index) => (
                    <span
                        key={verse.id}
                        ref={(el) => verseRefs.current[verse.id] = el}
                        className={`
                            inline transition-all duration-200
                            ${isActive === index ? 'ring-2 ring-primary-500 rounded-lg px-2 py-1' : ''}
                        `}
                        role="button"
                        tabIndex={0}
                        onClick={() => onOpenNote(verse)}
                    >
                        {/* Verse Number Badge - Inline with text */}
                        <span className={`
                            inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold
                            ml-2 mr-1 align-middle select-none
                            bg-primary-500 text-white shadow-sm
                            ring-1 ring-black/5 dark:ring-white/10
                        `}>
                            {verse.verse_number}
                        </span>
                        {verseNotes[verse.id] && (
                            <span
                                className="inline-flex items-center justify-center px-2 h-6 rounded-full bg-amber-100 text-amber-800 text-[11px] font-semibold mx-1 align-middle select-none"
                                title="لديك خاطرة على هذه الآية"
                            >
                                خاطرة
                            </span>
                        )}

                        {/* Verse Text - Continuous flow */}
                        <span className={`${t.text} inline`}>
                            {scriptType === 'imlaei' ? (verse.text_imlaei || verse.text_uthmani || verse.text) : (verse.text_uthmani || verse.text)}
                        </span>

                        {/* Small space after verse */}
                        <span className="inline-block w-2" />

                        {/* Tafsir Button for Tadabbur Mode */}
                        {readingMode === 'tadabbur' && showTafsir && (
                            <button
                                onClick={(e) => {
                                    e.stopPropagation();
                                    onTafsirClick(verse);
                                }}
                                className={`
                                    inline-flex items-center justify-center w-5 h-5 rounded-full text-xs mx-1 align-middle
                                    ${t.button} opacity-60 hover:opacity-100 transition
                                `}
                                title="عرض التفسير"
                            >
                                <MessageSquare className="w-3 h-3" />
                            </button>
                        )}
                    </span>
                ))}
            </div>
        </motion.div>
    );
}

// Verse Card Component
const VerseCard = React.forwardRef(({
    verse,
    index,
    theme: t,
    fontSize,
    scriptType,
    isActive,
    readingMode,
    showTafsir,
    onTafsirClick,
    verseNotes,
    onOpenNote,
}, ref) => {
    return (
        <motion.div
            ref={ref}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: index * 0.02 }}
            className={`
                transition-all duration-300
                ${t.verseBg} ${t.verseHover}
                ${isActive ? t.activeVerse + ' ring-2 ring-primary-500' : ''}
                px-3 md:px-6 py-4 md:py-6
                rounded-none md:rounded-2xl
            `}
            role="button"
            tabIndex={0}
            onClick={() => onOpenNote(verse)}
        >
            {/* Verse Number */}
            <div className="flex items-start justify-between mb-4">
                <div className="flex items-center gap-2">
                    <span className={`
                        inline-flex items-center justify-center w-11 h-11 rounded-full 
                        text-base font-bold select-none
                        bg-primary-500 text-white shadow-sm
                        ring-1 ring-black/5 dark:ring-white/10
                    `}>
                        {verse.verse_number}
                    </span>
                    {verseNotes[verse.id] && (
                        <span
                            className="inline-flex items-center justify-center px-2 h-7 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold select-none"
                            title="لديك خاطرة على هذه الآية"
                        >
                            خاطرة
                        </span>
                    )}
                </div>

                {readingMode === 'tadabbur' && (
                    <button
                        onClick={(e) => {
                            e.stopPropagation();
                            onTafsirClick();
                        }}
                        className={`p-2 rounded-lg ${t.button} transition`}
                    >
                        <MessageSquare className="w-4 h-4" />
                    </button>
                )}
            </div>

            {/* Verse Text */}
            <p
                className="font-amiri text-center rtl"
                style={{ fontSize: `${Math.max(22, fontSize)}px`, lineHeight: '2.5' }}
                dir="rtl"
            >
                {scriptType === 'imlaei' ? (verse.text_imlaei || verse.text_uthmani || verse.text) : (verse.text_uthmani || verse.text)}
            </p>
        </motion.div>
    );
});

VerseCard.displayName = 'VerseCard';

// Note Modal
function NoteModal({ verse, noteText, onChange, onSave, onDelete, onClose, isSaving, isDeleting, theme: t }) {
    return (
        <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-[80] flex items-center justify-center px-4"
        >
            <div className="absolute inset-0 bg-black/40" onClick={onClose} />
            <div className={`relative w-full max-w-lg rounded-2xl shadow-2xl ${t.bg} ${t.text} p-6 z-[81]`}>
                <div className="flex items-center justify-between mb-4">
                    <div>
                        <h3 className="font-bold flex items-center gap-2">
                            <Edit3 className="w-4 h-4" />
                            خاطرتي على الآية {verse?.verse_number}
                        </h3>
                        <p className={`text-sm ${t.accent}`}>{verse?.text_uthmani || verse?.text}</p>
                    </div>
                    <button onClick={onClose} className={`p-2 rounded-lg ${t.button}`}>
                        <X className="w-5 h-5" />
                    </button>
                </div>

                <textarea
                    value={noteText}
                    onChange={(e) => onChange(e.target.value)}
                    placeholder="اكتب تأملاتك وخواطرك هنا..."
                    className={`w-full h-32 p-4 rounded-xl border-2 ${t.border} ${t.bg} focus:border-primary-500 outline-none transition`}
                    dir="rtl"
                />

                <div className="flex gap-3 mt-4">
                    <button
                        onClick={onSave}
                        disabled={isSaving || !noteText.trim()}
                        className="
                            flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg
                            bg-primary-500 text-white font-bold hover:bg-primary-600 transition disabled:opacity-60
                        "
                    >
                        {isSaving ? 'جاري الحفظ...' : <><Save className="w-4 h-4" /> حفظ الخاطرة</>}
                    </button>
                    {verse?.user_note || noteText ? (
                        <button
                            onClick={onDelete}
                            disabled={isDeleting}
                            className="
                                px-4 py-3 rounded-lg border border-red-200 text-red-600 hover:bg-red-50
                                disabled:opacity-60
                            "
                        >
                            {isDeleting ? '...' : (
                                <span className="inline-flex items-center gap-2">
                                    <Trash2 className="w-4 h-4" /> حذف
                                </span>
                            )}
                        </button>
                    ) : null}
                </div>
            </div>
        </motion.div>
    );
}

// Tafsir Panel Component
function TafsirPanel({ verse, tafsir, isLoading, theme: t, onClose }) {
    return (
        <motion.div
            initial={{ y: '100%' }}
            animate={{ y: 0 }}
            exit={{ y: '100%' }}
            transition={{ type: 'spring', damping: 25 }}
            className={`
                fixed bottom-0 left-0 right-0 z-60 
                ${t.bg} rounded-t-3xl shadow-2xl
                max-h-[60vh] overflow-hidden
            `}
        >
            <div className="flex flex-col h-full">
                {/* Handle */}
                <div className="flex justify-center pt-3 pb-2">
                    <div className={`w-12 h-1.5 rounded-full ${t.border} bg-current opacity-30`} />
                </div>

                {/* Header */}
                <div className={`flex items-center justify-between px-6 py-3 border-b ${t.border}`}>
                    <h3 className="font-bold">تفسير الآية {verse.verse_number}</h3>
                    <button onClick={onClose} className={`p-2 rounded-lg ${t.button}`}>
                        <X className="w-5 h-5" />
                    </button>
                </div>

                {/* Content */}
                <div className="flex-1 overflow-y-auto p-6">
                    {isLoading ? (
                        <div className="flex items-center justify-center py-12">
                            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500" />
                        </div>
                    ) : (
                        <p className={`text-lg leading-relaxed ${t.accent}`}>
                            {tafsir}
                        </p>
                    )}
                </div>
            </div>
        </motion.div>
    );
}

// Settings Modal Component
function SettingsModal({
    theme: t,
    themes,
    currentTheme,
    fontSize,
    scriptType,
    selectedReciter,
    reciters,
    onThemeChange,
    onFontSizeChange,
    onScriptTypeChange,
    onReciterChange,
    onClose
}) {
    return (
        <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-70 bg-black/50 flex items-end justify-center"
            onClick={onClose}
        >
            <motion.div
                initial={{ y: '100%' }}
                animate={{ y: 0 }}
                exit={{ y: '100%' }}
                transition={{ type: 'spring', damping: 25 }}
                className={`
                    w-full max-w-lg ${t.bg} rounded-t-3xl p-6
                    max-h-[80vh] overflow-y-auto
                `}
                onClick={(e) => e.stopPropagation()}
            >
                {/* Handle */}
                <div className="flex justify-center mb-4">
                    <div className={`w-12 h-1.5 rounded-full ${t.border} bg-current opacity-30`} />
                </div>

                <h2 className="text-xl font-bold mb-6">الإعدادات</h2>

                {/* Theme Selection */}
                <div className="mb-6">
                    <h3 className={`text-sm font-medium ${t.accent} mb-3`}>المظهر</h3>
                    <div className="grid grid-cols-4 gap-2">
                        {Object.values(themes).map((themeOption) => (
                            <button
                                key={themeOption.id}
                                onClick={() => onThemeChange(themeOption.id)}
                                className={`
                                    p-3 rounded-xl border-2 transition
                                    ${currentTheme === themeOption.id
                                        ? 'border-primary-500'
                                        : `border-transparent ${t.verseBg}`
                                    }
                                `}
                            >
                                <div className={`w-full h-8 rounded-lg ${themeOption.bg} mb-2`} />
                                <span className="text-xs">{themeOption.name}</span>
                            </button>
                        ))}
                    </div>
                </div>

                {/* Font Size */}
                <div className="mb-6">
                    <h3 className={`text-sm font-medium ${t.accent} mb-3`}>حجم الخط</h3>
                    <div className="flex items-center gap-4">
                        <button
                            onClick={() => onFontSizeChange(Math.max(18, fontSize - 2))}
                            className={`p-3 rounded-xl ${t.button}`}
                        >
                            <Minus className="w-5 h-5" />
                        </button>
                        <div className="flex-1 text-center">
                            <span className="text-2xl font-bold">{fontSize}</span>
                            <p className={`text-sm ${t.accent}`}>نقطة</p>
                        </div>
                        <button
                            onClick={() => onFontSizeChange(Math.min(48, fontSize + 2))}
                            className={`p-3 rounded-xl ${t.button}`}
                        >
                            <Plus className="w-5 h-5" />
                        </button>
                    </div>
                    {/* Preview */}
                    <div className={`mt-4 p-4 rounded-xl ${t.verseBg} text-center`}>
                        <p style={{ fontSize: `${fontSize}px` }} className="font-quran">
                            بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                        </p>
                    </div>
                </div>

                {/* Script Type Selection */}
                <div className="mb-6">
                    <h3 className={`text-sm font-medium ${t.accent} mb-3`}>نوع الخط</h3>
                    <div className="grid grid-cols-2 gap-3">
                        <button
                            onClick={() => onScriptTypeChange('uthmani')}
                            className={`p-4 rounded-xl border-2 flex items-center gap-3 transition ${scriptType === 'uthmani'
                                ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                : `border-surface-200 dark:border-dark-300 ${t.verseBg}`
                                }`}
                        >
                            <div className="text-right flex-1">
                                <p className="font-medium text-text-primary dark:text-text-dark-primary">
                                    عثماني
                                </p>
                                <p className={`text-xs ${t.accent}`}>خط المصحف</p>
                            </div>
                            {scriptType === 'uthmani' && (
                                <Check className="w-5 h-5 text-primary-500" />
                            )}
                        </button>
                        <button
                            onClick={() => onScriptTypeChange('imlaei')}
                            className={`p-4 rounded-xl border-2 flex items-center gap-3 transition ${scriptType === 'imlaei'
                                ? 'border-accent-500 bg-accent-50 dark:bg-accent-900/20'
                                : `border-surface-200 dark:border-dark-300 ${t.verseBg}`
                                }`}
                        >
                            <div className="text-right flex-1">
                                <p className="font-medium text-text-primary dark:text-text-dark-primary">
                                    إملائي
                                </p>
                                <p className={`text-xs ${t.accent}`}>خط مبسط</p>
                            </div>
                            {scriptType === 'imlaei' && (
                                <Check className="w-5 h-5 text-accent-500" />
                            )}
                        </button>
                    </div>
                </div>

                {/* Reciter Selection */}
                {reciters?.length > 0 && (
                    <div className="mb-6">
                        <h3 className={`text-sm font-medium ${t.accent} mb-3`}>القارئ</h3>
                        <select
                            value={selectedReciter}
                            onChange={(e) => onReciterChange(parseInt(e.target.value))}
                            className={`w-full p-3 rounded-xl ${t.verseBg} ${t.text} border ${t.border}`}
                        >
                            {reciters.map((reciter) => (
                                <option key={reciter.id} value={reciter.id}>
                                    {reciter.reciter_name || reciter.name}
                                </option>
                            ))}
                        </select>
                    </div>
                )}

                {/* Close Button */}
                <button
                    onClick={onClose}
                    className="w-full py-3 rounded-xl bg-primary-500 text-white font-bold"
                >
                    تم
                </button>
            </motion.div>
        </motion.div>
    );
}
