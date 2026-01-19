import { useState, useEffect, useRef } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import axios from 'axios';
import { toPng } from 'html-to-image';
import {
    ArrowRight,
    BookOpen,
    MapPin,
    Plus,
    Settings,
    Maximize2,
    Minimize2,
    Menu,
    X,
    Type,
    Minus,
    Play,
    Pause,
    SkipForward,
    SkipBack,
    Volume2,
    MousePointer2,
    MessageSquare,
    Info,
    Edit3,
    Save,
    Share2,
    Download,
    Copy,
    Trash2,
} from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card, { CardContent, CardHeader } from '@/Components/UI/Card';
import Button from '@/Components/UI/Button';
import Modal from '@/Components/UI/Modal';

export default function ChapterView({ chapter, verses, chapters_list, juzs_list, reciters_list, memorization_stats }) {
    const [viewMode, setViewMode] = useState('surah');
    const [fontType, setFontType] = useState('uthmani');
    const [fontSize, setFontSize] = useState(24);
    const [isSettingsOpen, setIsSettingsOpen] = useState(false);
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);
    const [sidebarTab, setSidebarTab] = useState('chapters');
    const [isReciterDropdownOpen, setIsReciterDropdownOpen] = useState(false);

    // Audio States
    const [isPlaying, setIsPlaying] = useState(false);
    const [currentVerseIndex, setCurrentVerseIndex] = useState(-1);
    const [selectedReciterId, setSelectedReciterId] = useState(reciters_list?.[0]?.id || 1);
    const [autoScroll, setAutoScroll] = useState(true);
    const audioRef = useRef(null);

    // Tadabbur States
    const [selectedVerse, setSelectedVerse] = useState(null);
    const [tafsir, setTafsir] = useState(null);
    const [isTafsirLoading, setIsTafsirLoading] = useState(false);
    const [note, setNote] = useState('');
    const [isSavingNote, setIsSavingNote] = useState(false);
    const [isDeletingNote, setIsDeletingNote] = useState(false);
    const [activeWord, setActiveWord] = useState(null);
    const NOTES_STORAGE_KEY = 'mishkat-reading-notes';

    // Share States
    const [shareVerse, setShareVerse] = useState(null);
    const [isGeneratingImage, setIsGeneratingImage] = useState(false);
    const cardRef = useRef(null);

    // Get unique pages in current view
    const pagesInView = [...new Set(verses.map((v) => v.page_number))];

    // Initialize audio ref
    useEffect(() => {
        audioRef.current = new Audio();
        audioRef.current.preload = 'auto';
        audioRef.current.crossOrigin = 'anonymous';

        // Set up error handler
        audioRef.current.addEventListener('error', (e) => {
            console.error('Audio error:', e);
            console.error('Audio src:', audioRef.current?.src);
        });

        return () => {
            if (audioRef.current) {
                audioRef.current.pause();
                audioRef.current.src = '';
            }
        };
    }, []);

    // Set default reciter when reciters list loads
    useEffect(() => {
        if (reciters_list && reciters_list.length > 0 && !selectedReciterId) {
            setSelectedReciterId(reciters_list[0].id);
        }
    }, [reciters_list]);

    // Close reciter dropdown when clicking outside
    useEffect(() => {
        const handleClickOutside = (event) => {
            if (isReciterDropdownOpen && !event.target.closest('.reciter-dropdown-container')) {
                setIsReciterDropdownOpen(false);
            }
        };

        if (isReciterDropdownOpen) {
            document.addEventListener('mousedown', handleClickOutside);
        }

        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, [isReciterDropdownOpen]);

    // Toggle Focus Mode
    useEffect(() => {
        if (viewMode === 'focus') {
            document.body.classList.add('overflow-hidden');
        } else {
            document.body.classList.remove('overflow-hidden');
        }
    }, [viewMode]);

    // Handle Audio Playback
    useEffect(() => {
        if (!audioRef.current) return;

        const audio = audioRef.current;

        const handleEnded = () => {
            if (currentVerseIndex < verses.length - 1) {
                setCurrentVerseIndex((prev) => prev + 1);
            } else {
                setIsPlaying(false);
                setCurrentVerseIndex(-1);
            }
        };

        audio.addEventListener('ended', handleEnded);
        return () => audio.removeEventListener('ended', handleEnded);
    }, [currentVerseIndex, verses]);

    useEffect(() => {
        if (!audioRef.current) return;

        if (currentVerseIndex !== -1 && isPlaying) {
            const verse = verses[currentVerseIndex];
            // Try to find recitation for selected reciter, fallback to first available
            let recitation = verse.recitations?.find((r) => r.reciter_id === selectedReciterId);
            if (!recitation && verse.recitations && verse.recitations.length > 0) {
                recitation = verse.recitations[0];
                console.warn(`No recitation found for reciter ${selectedReciterId}, using first available:`, recitation);
            }

            console.log('Audio Debug:', {
                verseIndex: currentVerseIndex,
                verseId: verse?.id,
                verseNumber: verse?.verse_number,
                recitation,
                audioUrl: recitation?.audio_url,
                selectedReciterId,
                availableRecitations: verse.recitations?.map(r => ({ reciter_id: r.reciter_id, audio_url: r.audio_url }))
            });

            if (recitation?.audio_url) {
                const audio = audioRef.current;

                // Remove old event listeners
                const handleCanPlay = () => {
                    console.log('Audio can play, starting playback');
                    audio.play().catch((error) => {
                        console.error('Error playing audio after canplay:', error);
                        if (currentVerseIndex < verses.length - 1) {
                            setCurrentVerseIndex((prev) => prev + 1);
                        } else {
                            setIsPlaying(false);
                        }
                    });
                };

                const handleError = (e) => {
                    console.error('Audio load error:', e);
                    console.error('Failed URL:', audio.src);
                    if (currentVerseIndex < verses.length - 1) {
                        setCurrentVerseIndex((prev) => prev + 1);
                    } else {
                        setIsPlaying(false);
                    }
                };

                // Add event listeners
                audio.addEventListener('canplay', handleCanPlay, { once: true });
                audio.addEventListener('error', handleError, { once: true });

                // Set source and load
                audio.src = recitation.audio_url;
                audio.load();

                // Try to play immediately (some browsers allow this)
                const playPromise = audio.play();
                if (playPromise !== undefined) {
                    playPromise
                        .then(() => {
                            console.log('Audio playing successfully');
                        })
                        .catch((error) => {
                            console.log('Play promise rejected, waiting for canplay event:', error.message);
                            // Will be handled by canplay event listener
                        });
                }

                if (autoScroll) {
                    setTimeout(() => {
                        const element = document.getElementById(`verse-${verse.id}`);
                        if (element) {
                            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }, 100);
                }

                // Cleanup function
                return () => {
                    audio.removeEventListener('canplay', handleCanPlay);
                    audio.removeEventListener('error', handleError);
                };
            } else {
                console.warn('No recitation found for verse:', verse?.id, 'reciter:', selectedReciterId);
                if (currentVerseIndex < verses.length - 1) {
                    setCurrentVerseIndex((prev) => prev + 1);
                } else {
                    setIsPlaying(false);
                }
            }
        } else if (audioRef.current) {
            audioRef.current.pause();
        }
    }, [currentVerseIndex, isPlaying, selectedReciterId, autoScroll, verses]);

    const togglePlay = () => {
        if (currentVerseIndex === -1) {
            setCurrentVerseIndex(0);
        }
        setIsPlaying(!isPlaying);
    };

    const fetchTafsir = async (verse) => {
        setSelectedVerse(verse);
        setTafsir(null);
        setNote(verse.user_note || '');
        setIsTafsirLoading(true);
        try {
            const response = await axios.get(`/app/quran/verse/${verse.id}/tafsir`);
            setTafsir(response.data.tafsir);
        } catch (error) {
            console.error('Error fetching tafsir:', error);
        } finally {
            setIsTafsirLoading(false);
        }
    };

    const updateNotesCache = (verseId, noteText) => {
        try {
            const cached = JSON.parse(localStorage.getItem(NOTES_STORAGE_KEY) || '{}');
            const next = { ...cached, [verseId]: noteText || '' };
            localStorage.setItem(NOTES_STORAGE_KEY, JSON.stringify(next));
        } catch (e) {
            console.warn('Failed to update notes cache', e);
        }
    };

    const handleSaveNote = async () => {
        if (!selectedVerse) return;
        setIsSavingNote(true);
        try {
            await axios.post(`/app/quran/verse/${selectedVerse.id}/note`, { note });
            // Update local state
            const index = verses.findIndex(v => v.id === selectedVerse.id);
            if (index !== -1) {
                verses[index].user_note = note;
            }
            updateNotesCache(selectedVerse.id, note);
            // Show success - you could add a toast here
        } catch (error) {
            console.error('Error saving note:', error);
        } finally {
            setIsSavingNote(false);
        }
    };

    const handleDeleteNote = async () => {
        if (!selectedVerse) return;
        setIsDeletingNote(true);
        try {
            await axios.delete(`/app/quran/verse/${selectedVerse.id}/note`);
            // Update local state
            const index = verses.findIndex(v => v.id === selectedVerse.id);
            if (index !== -1) {
                verses[index].user_note = null;
            }
            setNote('');
            updateNotesCache(selectedVerse.id, '');
        } catch (error) {
            console.error('Error deleting note:', error);
        } finally {
            setIsDeletingNote(false);
        }
    };

    const handleDownloadCard = async () => {
        if (!cardRef.current || !shareVerse) return;
        setIsGeneratingImage(true);
        try {
            const dataUrl = await toPng(cardRef.current, { cacheBust: true });
            const link = document.createElement('a');
            link.download = `verse-${shareVerse.id}.png`;
            link.href = dataUrl;
            link.click();
        } catch (error) {
            console.error('Error generating image:', error);
        } finally {
            setIsGeneratingImage(false);
        }
    };

    const handleChapterChange = (id) => {
        router.visit(`/app/quran/chapter/${id}`);
        setIsSidebarOpen(false);
    };

    const handleJuzChange = (id) => {
        router.visit(`/app/quran/juz/${id}`);
        setIsSidebarOpen(false);
    };

    const scrollToPage = (pageNumber) => {
        const element = document.getElementById(`page-${pageNumber}`);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
            setIsSidebarOpen(false);
        }
    };

    const getVerseText = (verse) => {
        return fontType === 'uthmani' ? verse.text_uthmani : verse.text_imlaei;
    };

    const renderVerses = () => {
        if (viewMode === 'mushaf') {
            return (
                <div
                    className="font-amiri leading-[4.5rem] text-right p-6 md:p-10"
                    dir="rtl"
                    style={{ fontSize: `${fontSize}px` }}
                >
                    {verses.map((verse, index) => (
                        <span
                            key={verse.id}
                            id={`verse-${verse.id}`}
                            className={`inline group cursor-pointer transition-colors px-1 rounded-lg ${currentVerseIndex === index
                                ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300'
                                : verse.hifz_status === 'memorized'
                                    ? 'text-emerald-600 dark:text-emerald-400'
                                    : verse.hifz_status === 'review'
                                        ? 'text-amber-600 dark:text-amber-400'
                                        : 'hover:text-primary-600'
                                }`}
                        >
                            <span onClick={() => {
                                setCurrentVerseIndex(index);
                                setIsPlaying(true);
                            }}>
                                {getVerseText(verse)}
                            </span>
                            <span
                                onClick={() => fetchTafsir(verse)}
                                className={`inline-flex items-center justify-center w-8 h-8 mx-2 rounded-full border text-sm font-sans transition-colors hover:scale-110 ${currentVerseIndex === index
                                    ? 'bg-primary-500 text-white border-primary-500'
                                    : verse.hifz_status === 'memorized'
                                        ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 border-emerald-200'
                                        : verse.hifz_status === 'review'
                                            ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 border-amber-200'
                                            : 'border-primary-200 dark:border-primary-800 text-primary-600 dark:text-primary-400'
                                    }`}
                            >
                                {verse.verse_number}
                            </span>
                        </span>
                    ))}
                </div>
            );
        }

        // Surah mode - RTL layout with buttons on the right side
        return (
            <div className="space-y-4" dir="rtl">
                {verses.map((verse, index) => (
                    <motion.div
                        key={verse.id}
                        id={`verse-${verse.id}`}
                        initial={{ opacity: 0, y: 10 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: index * 0.01 }}
                        className={`flex flex-row gap-4 p-4 rounded-2xl transition-all border-2 ${currentVerseIndex === index
                            ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-200 dark:border-primary-800'
                            : verse.hifz_status === 'memorized'
                                ? 'bg-emerald-50 dark:bg-emerald-900/10 border-emerald-100 dark:border-emerald-900/30'
                                : verse.hifz_status === 'review'
                                    ? 'bg-amber-50 dark:bg-amber-900/10 border-amber-100 dark:border-amber-900/30'
                                    : 'bg-surface-50 dark:bg-dark-300 hover:bg-surface-100 dark:hover:bg-dark-200 border-transparent'
                            }`}
                    >
                        {/* Buttons on the right side (start in RTL) */}
                        <div className="shrink-0 flex flex-col gap-2">
                            <button
                                onClick={() => {
                                    setCurrentVerseIndex(index);
                                    setIsPlaying(true);
                                }}
                                className={`inline-flex items-center justify-center w-10 h-10 rounded-full font-bold transition-all ${currentVerseIndex === index
                                    ? 'bg-primary-500 text-white scale-110'
                                    : verse.hifz_status === 'memorized'
                                        ? 'bg-emerald-500 text-white'
                                        : verse.hifz_status === 'review'
                                            ? 'bg-amber-500 text-white'
                                            : 'bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 hover:scale-110'
                                    }`}
                            >
                                {verse.verse_number}
                            </button>
                            <button
                                onClick={() => fetchTafsir(verse)}
                                className="inline-flex items-center justify-center w-10 h-10 rounded-full bg-surface-200 dark:bg-dark-400 text-text-muted hover:text-primary-600 transition-colors"
                                title="التفسير والتدبر"
                            >
                                <MessageSquare className="w-5 h-5" />
                            </button>
                            <button
                                onClick={() => setShareVerse(verse)}
                                className="inline-flex items-center justify-center w-10 h-10 rounded-full bg-surface-200 dark:bg-dark-400 text-text-muted hover:text-emerald-600 transition-colors"
                                title="مشاركة الآية"
                            >
                                <Share2 className="w-5 h-5" />
                            </button>
                            {verse.user_note && (
                                <div className="inline-flex items-center justify-center w-10 h-10 rounded-full bg-amber-100 text-amber-600" title="لديك خاطرة">
                                    <Edit3 className="w-4 h-4" />
                                </div>
                            )}
                        </div>

                        {/* Verse text on the left side (end in RTL) */}
                        <div className="flex-1">
                            <p
                                className={`font-amiri leading-loose text-right transition-colors ${currentVerseIndex === index
                                    ? 'text-primary-900 dark:text-primary-100'
                                    : verse.hifz_status === 'memorized'
                                        ? 'text-emerald-900 dark:text-emerald-100'
                                        : verse.hifz_status === 'review'
                                            ? 'text-amber-900 dark:text-amber-100'
                                            : 'text-text-primary dark:text-text-dark-primary'
                                    }`}
                                style={{ fontSize: `${fontSize}px` }}
                            >
                                {getVerseText(verse)}
                            </p>
                        </div>
                    </motion.div>
                ))}
            </div>
        );
    };

    const memorizedCount = memorization_stats?.memorized_count || 0;
    const reviewCount = memorization_stats?.review_count || 0;

    return (
        <MainLayout title={chapter.display_title || chapter.name_arabic}>
            <Head title={chapter.display_title || `سورة ${chapter.name_arabic}`} />

            {/* Sidebar Navigation */}
            <AnimatePresence>
                {isSidebarOpen && (
                    <>
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            onClick={() => setIsSidebarOpen(false)}
                            className="fixed inset-0 bg-black/50 z-[60]"
                        />
                        <motion.div
                            initial={{ x: '100%' }}
                            animate={{ x: 0 }}
                            exit={{ x: '100%' }}
                            className="fixed right-0 top-0 bottom-0 w-80 bg-white dark:bg-dark-400 z-[70] shadow-2xl overflow-hidden flex flex-col"
                        >
                            <div className="p-6 border-b border-surface-200 dark:border-dark-300">
                                <div className="flex items-center justify-between mb-6">
                                    <h3 className="text-xl font-bold">التنقل السريع</h3>
                                    <button onClick={() => setIsSidebarOpen(false)}>
                                        <X className="w-6 h-6" />
                                    </button>
                                </div>

                                <div className="flex bg-surface-100 dark:bg-dark-300 rounded-xl p-1">
                                    <button
                                        onClick={() => setSidebarTab('chapters')}
                                        className={`flex-1 py-2 rounded-lg text-sm transition-colors ${sidebarTab === 'chapters' ? 'bg-white dark:bg-dark-200 shadow-sm font-bold' : ''
                                            }`}
                                    >
                                        السور
                                    </button>
                                    <button
                                        onClick={() => setSidebarTab('juzs')}
                                        className={`flex-1 py-2 rounded-lg text-sm transition-colors ${sidebarTab === 'juzs' ? 'bg-white dark:bg-dark-200 shadow-sm font-bold' : ''
                                            }`}
                                    >
                                        الأجزاء
                                    </button>
                                    <button
                                        onClick={() => setSidebarTab('pages')}
                                        className={`flex-1 py-2 rounded-lg text-sm transition-colors ${sidebarTab === 'pages' ? 'bg-white dark:bg-dark-200 shadow-sm font-bold' : ''
                                            }`}
                                    >
                                        الصفحات
                                    </button>
                                </div>
                            </div>

                            <div className="flex-1 overflow-y-auto p-6 pb-32">
                                {sidebarTab === 'chapters' && (
                                    <div className="grid grid-cols-1 gap-2">
                                        {chapters_list?.map((c) => (
                                            <button
                                                key={c.id}
                                                onClick={() => handleChapterChange(c.id)}
                                                className={`w-full text-right p-3 rounded-xl transition-colors ${c.id === chapter.id
                                                    ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 font-bold'
                                                    : 'hover:bg-surface-100 dark:hover:bg-dark-300'
                                                    }`}
                                            >
                                                <span className="ml-3 text-text-muted text-xs">{c.id}.</span>
                                                {c.name_arabic}
                                            </button>
                                        ))}
                                    </div>
                                )}

                                {sidebarTab === 'juzs' && (
                                    <div className="grid grid-cols-2 gap-2">
                                        {juzs_list?.map((j) => (
                                            <button
                                                key={j.id}
                                                onClick={() => handleJuzChange(j.id)}
                                                className={`text-center p-3 rounded-xl transition-colors ${verses[0]?.juz_number === j.juz_number
                                                    ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 font-bold border border-primary-200'
                                                    : 'bg-surface-50 dark:bg-dark-300 hover:bg-surface-100'
                                                    }`}
                                            >
                                                الجزء {j.juz_number}
                                            </button>
                                        ))}
                                    </div>
                                )}

                                {sidebarTab === 'pages' && (
                                    <div className="grid grid-cols-3 gap-2">
                                        {pagesInView.map((page) => (
                                            <button
                                                key={page}
                                                onClick={() => scrollToPage(page)}
                                                className="bg-surface-50 dark:bg-dark-300 p-3 rounded-xl hover:bg-surface-100 transition-colors text-center"
                                            >
                                                <span className="text-xs block text-text-muted">صفحة</span>
                                                <span className="font-bold">{page}</span>
                                            </button>
                                        ))}
                                    </div>
                                )}
                            </div>
                        </motion.div>
                    </>
                )}
            </AnimatePresence>

            <div className={`space-y-6 pb-32 ${viewMode === 'focus' ? 'fixed inset-0 z-50 bg-white dark:bg-dark-400 overflow-y-auto p-4 md:p-10' : ''}`}>
                {/* Top Controls */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-2">
                        {viewMode === 'focus' ? (
                            <Button
                                variant="ghost"
                                icon={Minimize2}
                                onClick={() => setViewMode('surah')}
                            >
                                إنهاء وضع التركيز
                            </Button>
                        ) : (
                            <Link
                                href="/app/quran"
                                className="inline-flex items-center gap-2 text-text-muted dark:text-text-dark-muted hover:text-text-primary dark:hover:text-text-dark-primary transition-colors"
                            >
                                <ArrowRight className="w-4 h-4" />
                                <span>العودة للمصحف</span>
                            </Link>
                        )}
                    </div>

                    <div className="flex items-center gap-2">
                        <Button
                            variant="outline"
                            size="sm"
                            icon={Menu}
                            onClick={() => setIsSidebarOpen(true)}
                        >
                            <span className="hidden sm:inline">التنقل</span>
                        </Button>
                        <Button
                            variant="outline"
                            size="sm"
                            icon={Settings}
                            onClick={() => setIsSettingsOpen(true)}
                        >
                            <span className="hidden sm:inline">الإعدادات</span>
                        </Button>
                    </div>
                </div>

                {viewMode !== 'focus' && (
                    <>
                        {/* Chapter Header */}
                        <Card gradient className="p-6 text-center relative overflow-hidden">
                            <div className="absolute top-0 right-0 p-4 opacity-10">
                                <BookOpen className="w-32 h-32 rotate-12" />
                            </div>
                            <motion.div
                                initial={{ scale: 0 }}
                                animate={{ scale: 1 }}
                                className="w-20 h-20 mx-auto mb-4 bg-white/20 rounded-full flex items-center justify-center"
                            >
                                <span className="text-2xl font-bold">{chapter.id}</span>
                            </motion.div>
                            <h1 className="text-3xl font-bold mb-2">
                                {chapter.display_title || `سورة ${chapter.name_arabic}`}
                            </h1>
                            <p className="text-white/80 mb-4">
                                {chapter.name_english}
                            </p>
                            <div className="flex items-center justify-center gap-4 text-sm text-white/70 font-sans">
                                <span>{chapter.verses_count} آية</span>
                                <span>•</span>
                                <span className="flex items-center gap-1">
                                    <MapPin className="w-4 h-4" />
                                    {chapter.revelation_place === 'makkah' ? 'مكية' : 'مدنية'}
                                </span>
                            </div>

                            {(memorizedCount > 0 || reviewCount > 0) && (
                                <div className="mt-6 flex items-center justify-center gap-4 font-sans">
                                    <div className="bg-emerald-500/20 backdrop-blur-sm px-4 py-2 rounded-2xl border border-emerald-500/30">
                                        <p className="text-xs text-emerald-100">تم حفظه</p>
                                        <p className="font-bold text-emerald-400">{memorizedCount} آية</p>
                                    </div>
                                    <div className="bg-amber-500/20 backdrop-blur-sm px-4 py-2 rounded-2xl border border-amber-500/30">
                                        <p className="text-xs text-amber-100">قيد المراجعة</p>
                                        <p className="font-bold text-amber-400">{reviewCount} آية</p>
                                    </div>
                                </div>
                            )}
                        </Card>

                        {/* Add to Plan Button */}
                        <div className="flex justify-center flex-wrap gap-4">
                            <Link href={`/app/plans/create?chapter=${chapter.id}`}>
                                <Button icon={Plus} variant="outline">
                                    إضافة للخطة
                                </Button>
                            </Link>
                            <div className="flex bg-surface-100 dark:bg-dark-300 rounded-xl p-1 font-sans">
                                <button
                                    onClick={() => setViewMode('surah')}
                                    className={`px-4 py-2 rounded-lg text-sm transition-colors ${viewMode === 'surah' ? 'bg-white dark:bg-dark-200 shadow-sm font-bold' : ''
                                        }`}
                                >
                                    وضع السورة
                                </button>
                                <button
                                    onClick={() => setViewMode('mushaf')}
                                    className={`px-4 py-2 rounded-lg text-sm transition-colors ${viewMode === 'mushaf' ? 'bg-white dark:bg-dark-200 shadow-sm font-bold' : ''
                                        }`}
                                >
                                    وضع المصحف
                                </button>
                                <button
                                    onClick={() => setViewMode('focus')}
                                    className={`px-4 py-2 rounded-lg text-sm transition-colors ${viewMode === 'focus' ? 'bg-white dark:bg-dark-200 shadow-sm font-bold' : ''
                                        }`}
                                >
                                    وضع التركيز
                                </button>
                            </div>
                        </div>
                    </>
                )}

                {/* Verses */}
                <Card className={viewMode === 'focus' ? 'border-none shadow-none' : ''}>
                    {viewMode !== 'focus' && (
                        <CardHeader className="flex flex-row items-center justify-between font-sans">
                            <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                الآيات
                            </h2>
                            <div className="flex items-center gap-2 text-sm text-text-muted">
                                <span>الجزء {verses[0]?.juz_number}</span>
                                <span>•</span>
                                <span>صفحة {verses[0]?.page_number}</span>
                            </div>
                        </CardHeader>
                    )}
                    <CardContent>
                        {/* Bismillah */}
                        {(chapter.id !== 1 && chapter.id !== 9 && !chapter.display_title?.includes("الجزء")) && (
                            <div className="text-center py-10 mb-6 border-b border-surface-200 dark:border-dark-300">
                                <p className="font-amiri text-3xl text-text-primary dark:text-text-dark-primary">
                                    بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                                </p>
                            </div>
                        )}

                        {renderVerses()}
                    </CardContent>
                </Card>
            </div>

            {/* Floating Audio Player */}
            <AnimatePresence>
                {currentVerseIndex !== -1 && (
                    <motion.div
                        initial={{ y: 100, opacity: 0 }}
                        animate={{ y: 0, opacity: 1 }}
                        exit={{ y: 100, opacity: 0 }}
                        className="fixed bottom-6 left-1/2 -translate-x-1/2 w-[90%] max-w-2xl z-[100]"
                    >
                        <div className="bg-white/80 dark:bg-dark-400/80 backdrop-blur-xl border border-white/20 dark:border-dark-300/50 shadow-2xl rounded-3xl p-4 md:p-6">
                            <div className="flex items-center gap-6">
                                {/* Reciter Info (Desktop) */}
                                <div className="hidden md:flex items-center gap-4 border-l border-surface-200 dark:border-dark-300 pl-6 relative reciter-dropdown-container">
                                    <div className="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-2xl flex items-center justify-center">
                                        <Volume2 className="w-6 h-6 text-primary-600" />
                                    </div>
                                    <div className="text-right relative">
                                        <p className="text-xs text-text-muted">القارئ الحالي</p>
                                        <button
                                            onClick={() => setIsReciterDropdownOpen(!isReciterDropdownOpen)}
                                            className="font-bold text-sm truncate w-32 hover:text-primary-600 transition-colors text-right"
                                        >
                                            {reciters_list?.find(r => r.id === selectedReciterId)?.reciter_name || 'غير محدد'}
                                        </button>

                                        {/* Reciter Dropdown */}
                                        <AnimatePresence>
                                            {isReciterDropdownOpen && (
                                                <motion.div
                                                    initial={{ opacity: 0, y: 10 }}
                                                    animate={{ opacity: 1, y: 0 }}
                                                    exit={{ opacity: 0, y: 10 }}
                                                    className="absolute bottom-full right-0 mb-2 w-64 bg-white dark:bg-dark-400 rounded-xl shadow-2xl border border-surface-200 dark:border-dark-300 max-h-64 overflow-y-auto z-50"
                                                >
                                                    <div className="p-2">
                                                        {reciters_list?.map((reciter) => (
                                                            <button
                                                                key={reciter.id}
                                                                onClick={() => {
                                                                    setSelectedReciterId(reciter.id);
                                                                    setIsReciterDropdownOpen(false);
                                                                }}
                                                                className={`w-full text-right p-3 rounded-lg transition-colors ${selectedReciterId === reciter.id
                                                                    ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 font-bold'
                                                                    : 'hover:bg-surface-100 dark:hover:bg-dark-300'
                                                                    }`}
                                                            >
                                                                {reciter.reciter_name}
                                                            </button>
                                                        ))}
                                                    </div>
                                                </motion.div>
                                            )}
                                        </AnimatePresence>
                                    </div>
                                </div>

                                {/* Reciter Selector (Mobile) */}
                                <div className="md:hidden relative reciter-dropdown-container">
                                    <button
                                        onClick={() => setIsReciterDropdownOpen(!isReciterDropdownOpen)}
                                        className="flex items-center gap-2 px-3 py-2 rounded-lg bg-surface-100 dark:bg-dark-300 hover:bg-surface-200 dark:hover:bg-dark-200 transition-colors"
                                    >
                                        <Volume2 className="w-4 h-4" />
                                        <span className="text-xs font-bold">
                                            {reciters_list?.find(r => r.id === selectedReciterId)?.reciter_name || 'غير محدد'}
                                        </span>
                                    </button>

                                    {/* Reciter Dropdown Mobile */}
                                    <AnimatePresence>
                                        {isReciterDropdownOpen && (
                                            <>
                                                <motion.div
                                                    initial={{ opacity: 0 }}
                                                    animate={{ opacity: 1 }}
                                                    exit={{ opacity: 0 }}
                                                    className="fixed inset-0 z-40"
                                                    onClick={() => setIsReciterDropdownOpen(false)}
                                                />
                                                <motion.div
                                                    initial={{ opacity: 0, y: 10 }}
                                                    animate={{ opacity: 1, y: 0 }}
                                                    exit={{ opacity: 0, y: 10 }}
                                                    className="absolute bottom-full right-0 mb-2 w-64 bg-white dark:bg-dark-400 rounded-xl shadow-2xl border border-surface-200 dark:border-dark-300 max-h-64 overflow-y-auto z-50"
                                                >
                                                    <div className="p-2">
                                                        {reciters_list?.map((reciter) => (
                                                            <button
                                                                key={reciter.id}
                                                                onClick={() => {
                                                                    setSelectedReciterId(reciter.id);
                                                                    setIsReciterDropdownOpen(false);
                                                                }}
                                                                className={`w-full text-right p-3 rounded-lg transition-colors ${selectedReciterId === reciter.id
                                                                    ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 font-bold'
                                                                    : 'hover:bg-surface-100 dark:hover:bg-dark-300'
                                                                    }`}
                                                            >
                                                                {reciter.reciter_name}
                                                            </button>
                                                        ))}
                                                    </div>
                                                </motion.div>
                                            </>
                                        )}
                                    </AnimatePresence>
                                </div>

                                {/* Main Controls */}
                                <div className="flex-1 flex flex-col gap-2">
                                    <div className="flex items-center justify-between mb-1">
                                        <span className="text-xs font-bold text-primary-600 dark:text-primary-400">
                                            آية {verses[currentVerseIndex]?.verse_number}
                                        </span>
                                        <div className="flex items-center gap-4">
                                            <button
                                                onClick={() => setAutoScroll(!autoScroll)}
                                                className={`transition-colors ${autoScroll ? 'text-primary-600' : 'text-text-muted'}`}
                                                title="التمرير التلقائي"
                                            >
                                                <MousePointer2 className="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>

                                    <div className="flex items-center justify-center gap-6">
                                        <button
                                            onClick={() => setCurrentVerseIndex(Math.max(0, currentVerseIndex - 1))}
                                            className="text-text-muted hover:text-text-primary transition-colors"
                                        >
                                            <SkipForward className="w-6 h-6" />
                                        </button>

                                        <button
                                            onClick={togglePlay}
                                            className="w-14 h-14 bg-primary-600 hover:bg-primary-700 text-white rounded-full flex items-center justify-center shadow-lg shadow-primary-500/30 transition-all hover:scale-110 active:scale-95"
                                        >
                                            {isPlaying ? <Pause className="w-8 h-8" /> : <Play className="w-8 h-8 ml-1" />}
                                        </button>

                                        <button
                                            onClick={() => {
                                                if (currentVerseIndex < verses.length - 1) {
                                                    setCurrentVerseIndex(currentVerseIndex + 1);
                                                }
                                            }}
                                            className="text-text-muted hover:text-text-primary transition-colors"
                                        >
                                            <SkipBack className="w-6 h-6" />
                                        </button>
                                    </div>
                                </div>

                                {/* Close Button */}
                                <button
                                    onClick={() => {
                                        setIsPlaying(false);
                                        setCurrentVerseIndex(-1);
                                    }}
                                    className="p-2 hover:bg-surface-100 dark:hover:bg-dark-300 rounded-xl transition-colors text-text-muted"
                                >
                                    <X className="w-6 h-6" />
                                </button>
                            </div>
                        </div>
                    </motion.div>
                )}
            </AnimatePresence>

            {/* Settings Modal */}
            <Modal
                isOpen={isSettingsOpen}
                onClose={() => setIsSettingsOpen(false)}
                title="إعدادات العرض والتلاوة"
            >
                <div className="p-6 space-y-8 max-h-[70vh] overflow-y-auto">
                    {/* Reciter Selection */}
                    <div className="space-y-4">
                        <label className="text-sm font-bold text-text-muted flex items-center gap-2">
                            <Volume2 className="w-4 h-4" />
                            القارئ المفضل
                        </label>
                        <div className="grid grid-cols-1 gap-2 max-h-40 overflow-y-auto">
                            {reciters_list?.map((r) => (
                                <button
                                    key={r.id}
                                    onClick={() => setSelectedReciterId(r.id)}
                                    className={`p-4 rounded-2xl border-2 text-right transition-all flex items-center justify-between ${selectedReciterId === r.id
                                        ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                        : 'border-surface-200 dark:border-dark-300 hover:border-primary-200'
                                        }`}
                                >
                                    <span className="font-bold">{r.reciter_name}</span>
                                    {selectedReciterId === r.id && (
                                        <div className="w-6 h-6 bg-primary-500 rounded-full flex items-center justify-center">
                                            <div className="w-2 h-2 bg-white rounded-full" />
                                        </div>
                                    )}
                                </button>
                            ))}
                        </div>
                    </div>

                    {/* Font Type */}
                    <div className="space-y-4">
                        <label className="text-sm font-bold text-text-muted flex items-center gap-2">
                            <Type className="w-4 h-4" />
                            نوع الخط
                        </label>
                        <div className="grid grid-cols-2 gap-4">
                            <button
                                onClick={() => setFontType('uthmani')}
                                className={`p-4 rounded-2xl border-2 transition-all ${fontType === 'uthmani'
                                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                    : 'border-surface-200 dark:border-dark-300'
                                    }`}
                            >
                                <p className="font-amiri text-lg mb-1">عثماني</p>
                                <p className="text-xs text-text-muted">الرسم العثماني</p>
                            </button>
                            <button
                                onClick={() => setFontType('imlaei')}
                                className={`p-4 rounded-2xl border-2 transition-all ${fontType === 'imlaei'
                                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                    : 'border-surface-200 dark:border-dark-300'
                                    }`}
                            >
                                <p className="font-amiri text-lg mb-1">إملائي</p>
                                <p className="text-xs text-text-muted">خط بسيط للمبتدئين</p>
                            </button>
                        </div>
                    </div>

                    {/* Font Size */}
                    <div className="space-y-4">
                        <label className="text-sm font-bold text-text-muted flex justify-between items-center">
                            <span className="flex items-center gap-2">
                                <Maximize2 className="w-4 h-4" />
                                حجم الخط
                            </span>
                            <span className="text-primary-600 font-bold">{fontSize}px</span>
                        </label>
                        <div className="flex items-center gap-4">
                            <Button
                                variant="outline"
                                size="sm"
                                icon={Minus}
                                onClick={() => setFontSize(Math.max(16, fontSize - 2))}
                            />
                            <div className="flex-1 h-2 bg-surface-200 dark:bg-dark-300 rounded-full overflow-hidden">
                                <div
                                    className="h-full bg-primary-500 transition-all"
                                    style={{ width: `${((fontSize - 16) / 48) * 100}%` }}
                                />
                            </div>
                            <Button
                                variant="outline"
                                size="sm"
                                icon={Plus}
                                onClick={() => setFontSize(Math.min(64, fontSize + 2))}
                            />
                        </div>
                    </div>

                    <div className="pt-4 sticky bottom-0 bg-white dark:bg-dark-400">
                        <Button
                            className="w-full"
                            onClick={() => setIsSettingsOpen(false)}
                        >
                            حفظ الإعدادات
                        </Button>
                    </div>
                </div>
            </Modal>

            {/* Share Verse Modal */}
            <Modal
                isOpen={!!shareVerse}
                onClose={() => setShareVerse(null)}
                title="بطاقة الآية"
            >
                <div className="p-6 space-y-8">
                    {/* Verse Card Preview */}
                    <div className="flex justify-center overflow-hidden">
                        <div
                            ref={cardRef}
                            className="w-[320px] md:w-[400px] aspect-[4/5] bg-emerald-950 rounded-[40px] p-8 md:p-10 relative overflow-hidden shadow-2xl flex flex-col items-center justify-center text-center"
                        >
                            {/* Background Elements */}
                            <div className="absolute top-0 right-0 w-40 h-40 bg-emerald-800/20 rounded-full -translate-y-1/2 translate-x-1/2" />
                            <div className="absolute bottom-0 left-0 w-60 h-60 bg-emerald-800/10 rounded-full translate-y-1/2 -translate-x-1/2" />
                            <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full border-[20px] border-white/5 rounded-[40px]" />

                            <BookOpen className="w-12 h-12 text-emerald-400/50 mb-8" />

                            <p className="font-amiri text-2xl md:text-3xl leading-loose text-white mb-8" dir="rtl">
                                {shareVerse && getVerseText(shareVerse)}
                            </p>

                            <div className="space-y-1">
                                <p className="text-emerald-400 font-bold">سورة {chapter.name_arabic}</p>
                                <p className="text-white/40 text-sm font-sans">آية {shareVerse?.verse_number}</p>
                            </div>

                            <div className="mt-12 pt-8 border-t border-white/10 w-full">
                                <p className="text-white/60 text-sm font-bold tracking-widest">مشكاة</p>
                            </div>
                        </div>
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                        <Button
                            className="w-full"
                            icon={isGeneratingImage ? null : Download}
                            onClick={handleDownloadCard}
                            disabled={isGeneratingImage}
                        >
                            {isGeneratingImage ? 'جاري التحميل...' : 'تحميل الصورة'}
                        </Button>
                        <Button
                            variant="outline"
                            className="w-full"
                            icon={Copy}
                            onClick={() => {
                                if (shareVerse) {
                                    navigator.clipboard.writeText(getVerseText(shareVerse));
                                }
                            }}
                        >
                            نسخ النص
                        </Button>
                    </div>
                </div>
            </Modal>

            {/* Tadabbur & Notes Modal */}
            <Modal
                isOpen={!!selectedVerse}
                onClose={() => setSelectedVerse(null)}
                title={`تدبر آية ${selectedVerse?.verse_number}`}
            >
                <div className="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                    {/* Verse Text */}
                    <div className="p-6 bg-surface-50 dark:bg-dark-300 rounded-3xl text-right" dir="rtl">
                        <p className="font-amiri text-2xl leading-loose text-primary-900 dark:text-primary-100">
                            {selectedVerse && getVerseText(selectedVerse)}
                        </p>
                    </div>

                    {/* Tafsir Content */}
                    <div className="space-y-4">
                        <div className="flex items-center justify-between">
                            <h4 className="font-bold flex items-center gap-2">
                                <Info className="w-4 h-4 text-primary-600" />
                                التفسير الميسر
                            </h4>
                        </div>

                        {isTafsirLoading ? (
                            <div className="space-y-2 animate-pulse">
                                <div className="h-4 bg-surface-200 dark:bg-dark-300 rounded w-full" />
                                <div className="h-4 bg-surface-200 dark:bg-dark-300 rounded w-full" />
                                <div className="h-4 bg-surface-200 dark:bg-dark-300 rounded w-3/4" />
                            </div>
                        ) : tafsir ? (
                            <p className="text-text-primary dark:text-text-dark-primary leading-relaxed text-right" dir="rtl">
                                {tafsir}
                            </p>
                        ) : (
                            <p className="text-text-muted text-center py-4">
                                لم يتم العثور على التفسير
                            </p>
                        )}
                    </div>

                    {/* Journaling Section */}
                    <div className="pt-6 border-t border-surface-200 dark:border-dark-300 space-y-4">
                        <h4 className="font-bold flex items-center gap-2">
                            <Edit3 className="w-4 h-4 text-amber-600" />
                            خواطري حول هذه الآية
                        </h4>
                        <textarea
                            value={note}
                            onChange={(e) => setNote(e.target.value)}
                            placeholder="اكتب تأملاتك وخواطرك هنا..."
                            className="w-full h-32 p-4 rounded-2xl border-2 border-surface-200 dark:border-dark-300 bg-transparent focus:border-primary-500 transition-all text-right resize-none"
                            dir="rtl"
                        />
                        <div className="flex gap-3">
                            <Button
                                className="flex-1"
                                variant="outline"
                                icon={isSavingNote ? null : Save}
                                onClick={handleSaveNote}
                                disabled={isSavingNote || !note.trim()}
                            >
                                {isSavingNote ? 'جاري الحفظ...' : 'حفظ الخاطرة'}
                            </Button>
                            {selectedVerse?.user_note && (
                                <Button
                                    variant="outline"
                                    icon={isDeletingNote ? null : Trash2}
                                    onClick={handleDeleteNote}
                                    disabled={isDeletingNote}
                                    className="text-red-600 border-red-200 hover:bg-red-50 dark:border-red-900 dark:hover:bg-red-900/20"
                                >
                                    {isDeletingNote ? '...' : 'حذف'}
                                </Button>
                            )}
                        </div>
                    </div>
                </div>
            </Modal>
        </MainLayout>
    );
}
