import { useState, useRef, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Play, Pause, Volume2, VolumeX, Loader2 } from 'lucide-react';

export default function AudioPlayer({
    recitations = [],
    selectedReciterId,
    onReciterChange,
    isCompact = false,
}) {
    const audioRef = useRef(null);
    const [isPlaying, setIsPlaying] = useState(false);
    const [isLoading, setIsLoading] = useState(false);
    const [isMuted, setIsMuted] = useState(false);
    const [progress, setProgress] = useState(0);
    const [duration, setDuration] = useState(0);

    // Find current recitation based on selected reciter
    const currentRecitation = recitations.find(r => r.reciter_id === selectedReciterId) || recitations[0];
    const audioUrl = currentRecitation?.audio_url;

    useEffect(() => {
        // Reset when audio URL changes
        setIsPlaying(false);
        setProgress(0);
        if (audioRef.current) {
            audioRef.current.pause();
            audioRef.current.currentTime = 0;
        }
    }, [audioUrl]);

    const handlePlayPause = () => {
        if (!audioRef.current || !audioUrl) return;

        if (isPlaying) {
            audioRef.current.pause();
            setIsPlaying(false);
        } else {
            setIsLoading(true);
            audioRef.current.play()
                .then(() => {
                    setIsPlaying(true);
                    setIsLoading(false);
                })
                .catch((err) => {
                    console.error('Error playing audio:', err);
                    setIsLoading(false);
                });
        }
    };

    const handleTimeUpdate = () => {
        if (audioRef.current) {
            const current = audioRef.current.currentTime;
            const total = audioRef.current.duration;
            setProgress(total ? (current / total) * 100 : 0);
        }
    };

    const handleLoadedMetadata = () => {
        if (audioRef.current) {
            setDuration(audioRef.current.duration);
        }
    };

    const handleEnded = () => {
        setIsPlaying(false);
        setProgress(0);
    };

    const handleProgressClick = (e) => {
        if (!audioRef.current || !duration) return;
        const rect = e.currentTarget.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const newProgress = (clickX / rect.width) * 100;
        const newTime = (newProgress / 100) * duration;
        audioRef.current.currentTime = newTime;
        setProgress(newProgress);
    };

    const toggleMute = () => {
        if (audioRef.current) {
            audioRef.current.muted = !isMuted;
            setIsMuted(!isMuted);
        }
    };

    const formatTime = (seconds) => {
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    };

    if (recitations.length === 0) {
        return (
            <div className="text-sm text-text-muted dark:text-text-dark-muted text-center py-2">
                لا توجد تلاوات متاحة
            </div>
        );
    }

    if (isCompact) {
        return (
            <div className="flex items-center gap-2">
                <audio
                    ref={audioRef}
                    src={audioUrl}
                    onTimeUpdate={handleTimeUpdate}
                    onLoadedMetadata={handleLoadedMetadata}
                    onEnded={handleEnded}
                />
                <motion.button
                    whileHover={{ scale: 1.1 }}
                    whileTap={{ scale: 0.9 }}
                    onClick={handlePlayPause}
                    disabled={!audioUrl}
                    className="w-8 h-8 rounded-full bg-primary-500 text-white flex items-center justify-center hover:bg-primary-600 transition-colors disabled:opacity-50"
                >
                    {isLoading ? (
                        <Loader2 className="w-4 h-4 animate-spin" />
                    ) : isPlaying ? (
                        <Pause className="w-4 h-4" />
                    ) : (
                        <Play className="w-4 h-4 mr-0.5" />
                    )}
                </motion.button>
            </div>
        );
    }

    return (
        <div className="bg-surface-100 dark:bg-dark-300 rounded-xl p-4 space-y-3">
            <audio
                ref={audioRef}
                src={audioUrl}
                onTimeUpdate={handleTimeUpdate}
                onLoadedMetadata={handleLoadedMetadata}
                onEnded={handleEnded}
            />

            {/* Reciter Selector */}
            <div className="flex items-center gap-2">
                <label className="text-sm font-medium text-text-secondary dark:text-text-dark-secondary">
                    القارئ:
                </label>
                <select
                    value={selectedReciterId || recitations[0]?.reciter_id || ''}
                    onChange={(e) => onReciterChange(parseInt(e.target.value))}
                    className="flex-1 px-3 py-2 text-sm rounded-lg border border-surface-300 dark:border-dark-200 bg-white dark:bg-dark-400 text-text-primary dark:text-text-dark-primary focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                    dir="rtl"
                >
                    {recitations.map((recitation) => (
                        <option key={recitation.reciter_id} value={recitation.reciter_id}>
                            {recitation.reciter_name || 'قارئ غير معروف'}
                        </option>
                    ))}
                </select>
            </div>

            {/* Player Controls */}
            <div className="flex items-center gap-3">
                {/* Play/Pause Button */}
                <motion.button
                    whileHover={{ scale: 1.05 }}
                    whileTap={{ scale: 0.95 }}
                    onClick={handlePlayPause}
                    disabled={!audioUrl}
                    className="w-12 h-12 rounded-full bg-primary-500 text-white flex items-center justify-center hover:bg-primary-600 transition-colors disabled:opacity-50 shadow-lg"
                >
                    {isLoading ? (
                        <Loader2 className="w-5 h-5 animate-spin" />
                    ) : isPlaying ? (
                        <Pause className="w-5 h-5" />
                    ) : (
                        <Play className="w-5 h-5 mr-0.5" />
                    )}
                </motion.button>

                {/* Progress Bar */}
                <div className="flex-1 space-y-1">
                    <div
                        onClick={handleProgressClick}
                        className="h-2 bg-surface-200 dark:bg-dark-200 rounded-full cursor-pointer overflow-hidden"
                    >
                        <motion.div
                            className="h-full bg-primary-500 rounded-full"
                            style={{ width: `${progress}%` }}
                            transition={{ duration: 0.1 }}
                        />
                    </div>
                    <div className="flex justify-between text-xs text-text-muted dark:text-text-dark-muted">
                        <span>{formatTime((progress / 100) * duration)}</span>
                        <span>{formatTime(duration)}</span>
                    </div>
                </div>

                {/* Mute Button */}
                <motion.button
                    whileHover={{ scale: 1.1 }}
                    whileTap={{ scale: 0.9 }}
                    onClick={toggleMute}
                    className="p-2 rounded-lg hover:bg-surface-200 dark:hover:bg-dark-200 transition-colors text-text-secondary dark:text-text-dark-secondary"
                >
                    {isMuted ? (
                        <VolumeX className="w-5 h-5" />
                    ) : (
                        <Volume2 className="w-5 h-5" />
                    )}
                </motion.button>
            </div>
        </div>
    );
}
