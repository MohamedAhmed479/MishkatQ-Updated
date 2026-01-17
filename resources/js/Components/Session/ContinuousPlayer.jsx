import { useState, useRef, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Play, Pause, SkipForward, SkipBack, Volume2, VolumeX, Loader2 } from 'lucide-react';

export default function ContinuousPlayer({
    verses = [],
    selectedReciterId,
    onReciterChange,
    reciters = [],
    onVerseChange,
}) {
    const audioRef = useRef(null);
    const [isPlaying, setIsPlaying] = useState(false);
    const [isLoading, setIsLoading] = useState(false);
    const [isMuted, setIsMuted] = useState(false);
    const [currentVerseIndex, setCurrentVerseIndex] = useState(0);
    const [progress, setProgress] = useState(0);
    const shouldAutoPlay = useRef(false);

    const currentVerse = verses[currentVerseIndex];
    const currentRecitation = currentVerse?.recitations?.find(r => r.reciter_id === selectedReciterId)
        || currentVerse?.recitations?.[0];
    const audioUrl = currentRecitation?.audio_url;

    // Notify parent when verse changes
    useEffect(() => {
        if (onVerseChange) {
            onVerseChange(currentVerseIndex);
        }
    }, [currentVerseIndex, onVerseChange]);

    // Reset when reciter changes
    useEffect(() => {
        setIsPlaying(false);
        setProgress(0);
        shouldAutoPlay.current = false;
        if (audioRef.current) {
            audioRef.current.pause();
            audioRef.current.currentTime = 0;
        }
    }, [selectedReciterId]);

    // Auto-play when verse changes and shouldAutoPlay is true
    useEffect(() => {
        if (shouldAutoPlay.current && audioUrl && audioRef.current) {
            setIsLoading(true);
            audioRef.current.src = audioUrl;
            audioRef.current.play()
                .then(() => {
                    setIsPlaying(true);
                    setIsLoading(false);
                })
                .catch((err) => {
                    console.error('Error playing audio:', err);
                    setIsLoading(false);
                    shouldAutoPlay.current = false;
                });
        }
    }, [currentVerseIndex, audioUrl]);

    const handlePlayPause = () => {
        if (!audioRef.current || !audioUrl) return;

        if (isPlaying) {
            audioRef.current.pause();
            setIsPlaying(false);
            shouldAutoPlay.current = false;
        } else {
            setIsLoading(true);
            shouldAutoPlay.current = true;
            audioRef.current.src = audioUrl;
            audioRef.current.play()
                .then(() => {
                    setIsPlaying(true);
                    setIsLoading(false);
                })
                .catch((err) => {
                    console.error('Error playing audio:', err);
                    setIsLoading(false);
                    shouldAutoPlay.current = false;
                });
        }
    };

    const handleEnded = () => {
        // Move to next verse
        if (currentVerseIndex < verses.length - 1) {
            shouldAutoPlay.current = true;
            setCurrentVerseIndex(prev => prev + 1);
        } else {
            // All verses completed
            setIsPlaying(false);
            setCurrentVerseIndex(0);
            setProgress(0);
            shouldAutoPlay.current = false;
        }
    };

    const handleTimeUpdate = () => {
        if (audioRef.current) {
            const current = audioRef.current.currentTime;
            const total = audioRef.current.duration;
            setProgress(total ? (current / total) * 100 : 0);
        }
    };

    const handlePrevious = () => {
        if (currentVerseIndex > 0) {
            setCurrentVerseIndex(prev => prev - 1);
        }
    };

    const handleNext = () => {
        if (currentVerseIndex < verses.length - 1) {
            setCurrentVerseIndex(prev => prev + 1);
        }
    };

    const toggleMute = () => {
        if (audioRef.current) {
            audioRef.current.muted = !isMuted;
            setIsMuted(!isMuted);
        }
    };

    const totalProgress = ((currentVerseIndex + (progress / 100)) / verses.length) * 100;

    return (
        <div className="bg-white dark:bg-dark-400 rounded-2xl p-4 shadow-lg border border-surface-300 dark:border-dark-300">
            <audio
                ref={audioRef}
                onTimeUpdate={handleTimeUpdate}
                onEnded={handleEnded}
            />

            {/* Reciter Selector */}
            <div className="flex items-center gap-2 mb-4">
                <label className="text-sm font-medium text-text-secondary dark:text-text-dark-secondary whitespace-nowrap">
                    القارئ:
                </label>
                <select
                    value={selectedReciterId || ''}
                    onChange={(e) => onReciterChange(parseInt(e.target.value))}
                    className="flex-1 px-3 py-2 text-sm rounded-lg border border-surface-300 dark:border-dark-200 bg-surface-50 dark:bg-dark-500 text-text-primary dark:text-text-dark-primary focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                    dir="rtl"
                >
                    {reciters.map((reciter) => (
                        <option key={reciter.id} value={reciter.id}>
                            {reciter.name}
                        </option>
                    ))}
                </select>
            </div>

            {/* Now Playing Info */}
            <div className="text-center mb-3">
                <p className="text-sm text-text-muted dark:text-text-dark-muted">
                    الآية {currentVerseIndex + 1} من {verses.length}
                </p>
            </div>

            {/* Total Progress Bar */}
            <div className="mb-4">
                <div className="h-2 bg-surface-200 dark:bg-dark-300 rounded-full overflow-hidden">
                    <motion.div
                        className="h-full bg-gradient-to-r from-primary-400 to-primary-600 rounded-full"
                        style={{ width: `${totalProgress}%` }}
                        transition={{ duration: 0.1 }}
                    />
                </div>
            </div>

            {/* Controls */}
            <div className="flex items-center justify-center gap-4">
                {/* Previous */}
                <motion.button
                    whileHover={{ scale: 1.1 }}
                    whileTap={{ scale: 0.9 }}
                    onClick={handlePrevious}
                    disabled={currentVerseIndex === 0}
                    className="p-2 rounded-full hover:bg-surface-200 dark:hover:bg-dark-300 transition-colors disabled:opacity-30"
                >
                    <SkipForward className="w-6 h-6 text-text-primary dark:text-text-dark-primary" />
                </motion.button>

                {/* Play/Pause */}
                <motion.button
                    whileHover={{ scale: 1.05 }}
                    whileTap={{ scale: 0.95 }}
                    onClick={handlePlayPause}
                    className="w-14 h-14 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 text-white flex items-center justify-center shadow-lg hover:shadow-xl transition-shadow"
                >
                    {isLoading ? (
                        <Loader2 className="w-6 h-6 animate-spin" />
                    ) : isPlaying ? (
                        <Pause className="w-6 h-6" />
                    ) : (
                        <Play className="w-6 h-6 mr-0.5" />
                    )}
                </motion.button>

                {/* Next */}
                <motion.button
                    whileHover={{ scale: 1.1 }}
                    whileTap={{ scale: 0.9 }}
                    onClick={handleNext}
                    disabled={currentVerseIndex === verses.length - 1}
                    className="p-2 rounded-full hover:bg-surface-200 dark:hover:bg-dark-300 transition-colors disabled:opacity-30"
                >
                    <SkipBack className="w-6 h-6 text-text-primary dark:text-text-dark-primary" />
                </motion.button>

                {/* Mute */}
                <motion.button
                    whileHover={{ scale: 1.1 }}
                    whileTap={{ scale: 0.9 }}
                    onClick={toggleMute}
                    className="p-2 rounded-full hover:bg-surface-200 dark:hover:bg-dark-300 transition-colors"
                >
                    {isMuted ? (
                        <VolumeX className="w-5 h-5 text-text-muted dark:text-text-dark-muted" />
                    ) : (
                        <Volume2 className="w-5 h-5 text-text-primary dark:text-text-dark-primary" />
                    )}
                </motion.button>
            </div>

            {/* Play All Label */}
            <p className="text-center text-xs text-text-muted dark:text-text-dark-muted mt-3">
                تشغيل متواصل لجميع الآيات
            </p>
        </div>
    );
}
