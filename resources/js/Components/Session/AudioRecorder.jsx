import { useState, useRef, useEffect, useCallback } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Mic, Square, Play, Pause, RotateCcw, Check, Loader2, AlertCircle } from 'lucide-react';

export default function AudioRecorder({
    onRecordingComplete,
    verses = [],
    isVerifying = false,
    verificationResult = null,
    onRetry,
    onContinue,
}) {
    const mediaRecorderRef = useRef(null);
    const audioRef = useRef(null);
    const chunksRef = useRef([]);
    const streamRef = useRef(null);

    const [isRecording, setIsRecording] = useState(false);
    const [isPaused, setIsPaused] = useState(false);
    const [recordedBlob, setRecordedBlob] = useState(null);
    const [recordedUrl, setRecordedUrl] = useState(null);
    const [isPlaying, setIsPlaying] = useState(false);
    const [duration, setDuration] = useState(0);
    const [currentTime, setCurrentTime] = useState(0);
    const [error, setError] = useState(null);
    const [recordingTime, setRecordingTime] = useState(0);

    // Recording timer
    useEffect(() => {
        let interval;
        if (isRecording && !isPaused) {
            interval = setInterval(() => {
                setRecordingTime((prev) => prev + 1);
            }, 1000);
        }
        return () => clearInterval(interval);
    }, [isRecording, isPaused]);

    // Cleanup on unmount
    useEffect(() => {
        return () => {
            if (streamRef.current) {
                streamRef.current.getTracks().forEach((track) => track.stop());
            }
            if (recordedUrl) {
                URL.revokeObjectURL(recordedUrl);
            }
        };
    }, [recordedUrl]);

    const startRecording = useCallback(async () => {
        try {
            setError(null);
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            streamRef.current = stream;

            const mediaRecorder = new MediaRecorder(stream, {
                mimeType: MediaRecorder.isTypeSupported('audio/webm') ? 'audio/webm' : 'audio/mp4',
            });

            mediaRecorderRef.current = mediaRecorder;
            chunksRef.current = [];

            mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    chunksRef.current.push(event.data);
                }
            };

            mediaRecorder.onstop = () => {
                const blob = new Blob(chunksRef.current, { type: mediaRecorder.mimeType });
                setRecordedBlob(blob);
                const url = URL.createObjectURL(blob);
                setRecordedUrl(url);

                // Stop all tracks
                if (streamRef.current) {
                    streamRef.current.getTracks().forEach((track) => track.stop());
                }
            };

            mediaRecorder.start(1000); // Collect data every second
            setIsRecording(true);
            setRecordingTime(0);
        } catch (err) {
            console.error('Error starting recording:', err);
            setError('لا يمكن الوصول إلى الميكروفون. يرجى السماح بالوصول والمحاولة مرة أخرى.');
        }
    }, []);

    const stopRecording = useCallback(() => {
        if (mediaRecorderRef.current && isRecording) {
            mediaRecorderRef.current.stop();
            setIsRecording(false);
            setIsPaused(false);
        }
    }, [isRecording]);

    const pauseRecording = useCallback(() => {
        if (mediaRecorderRef.current && isRecording) {
            if (isPaused) {
                mediaRecorderRef.current.resume();
                setIsPaused(false);
            } else {
                mediaRecorderRef.current.pause();
                setIsPaused(true);
            }
        }
    }, [isRecording, isPaused]);

    const resetRecording = useCallback(() => {
        setRecordedBlob(null);
        if (recordedUrl) {
            URL.revokeObjectURL(recordedUrl);
        }
        setRecordedUrl(null);
        setRecordingTime(0);
        setCurrentTime(0);
        setDuration(0);
        setIsPlaying(false);
        if (onRetry) {
            onRetry();
        }
    }, [recordedUrl, onRetry]);

    const handlePlayPause = useCallback(() => {
        if (!audioRef.current) return;

        if (isPlaying) {
            audioRef.current.pause();
            setIsPlaying(false);
        } else {
            audioRef.current.play();
            setIsPlaying(true);
        }
    }, [isPlaying]);

    const handleSubmit = useCallback(() => {
        if (recordedBlob && onRecordingComplete) {
            onRecordingComplete(recordedBlob);
        }
    }, [recordedBlob, onRecordingComplete]);

    const formatTime = (seconds) => {
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    };

    // Render verification result with colored text
    const renderVerificationResult = () => {
        if (!verificationResult) return null;

        const { accuracy, errors, transcript } = verificationResult;

        return (
            <motion.div
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                className="mt-4 space-y-3"
            >
                {/* Accuracy Score */}
                <div className="flex items-center justify-between p-3 rounded-xl bg-surface-100 dark:bg-dark-300">
                    <span className="text-sm font-medium text-text-secondary dark:text-text-dark-secondary">
                        نسبة الدقة
                    </span>
                    <span
                        className={`text-lg font-bold ${accuracy >= 70
                                ? 'text-green-600 dark:text-green-400'
                                : accuracy >= 50
                                    ? 'text-yellow-600 dark:text-yellow-400'
                                    : 'text-red-600 dark:text-red-400'
                            }`}
                    >
                        {accuracy}%
                    </span>
                </div>

                {/* Colored Transcript */}
                {transcript && (
                    <div className="p-4 rounded-xl bg-surface-50 dark:bg-dark-400 border border-surface-200 dark:border-dark-300">
                        <p className="text-sm font-medium text-text-secondary dark:text-text-dark-secondary mb-2">
                            نتيجة التسميع:
                        </p>
                        <div className="font-amiri text-xl leading-loose text-right" dir="rtl">
                            {transcript}
                        </div>
                    </div>
                )}

                {/* Errors List */}
                {errors && errors.length > 0 && (
                    <div className="p-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                        <p className="text-sm font-medium text-red-700 dark:text-red-300 mb-2 flex items-center gap-2">
                            <AlertCircle className="w-4 h-4" />
                            الأخطاء المكتشفة ({errors.length})
                        </p>
                        <ul className="text-sm text-red-600 dark:text-red-400 space-y-1 list-disc list-inside">
                            {errors.slice(0, 5).map((error, idx) => (
                                <li key={idx}>{error}</li>
                            ))}
                            {errors.length > 5 && (
                                <li className="text-red-500">... و{errors.length - 5} أخطاء أخرى</li>
                            )}
                        </ul>
                    </div>
                )}

                {/* Retry or Continue Buttons */}
                <div className="flex gap-3">
                    <motion.button
                        whileHover={{ scale: 1.02 }}
                        whileTap={{ scale: 0.98 }}
                        onClick={resetRecording}
                        className="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-surface-300 dark:border-dark-200 text-text-primary dark:text-text-dark-primary hover:bg-surface-100 dark:hover:bg-dark-300 transition-colors"
                    >
                        <RotateCcw className="w-5 h-5" />
                        إعادة التسميع
                    </motion.button>
                    {accuracy >= 70 && onContinue && (
                        <motion.button
                            whileHover={{ scale: 1.02 }}
                            whileTap={{ scale: 0.98 }}
                            onClick={onContinue}
                            className="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-green-500 text-white hover:bg-green-600 transition-colors"
                        >
                            <Check className="w-5 h-5" />
                            متابعة
                        </motion.button>
                    )}
                </div>
            </motion.div>
        );
    };

    return (
        <div className="space-y-4">
            {/* Error Message */}
            <AnimatePresence>
                {error && (
                    <motion.div
                        initial={{ opacity: 0, y: -10 }}
                        animate={{ opacity: 1, y: 0 }}
                        exit={{ opacity: 0, y: -10 }}
                        className="p-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm flex items-center gap-2"
                    >
                        <AlertCircle className="w-4 h-4 flex-shrink-0" />
                        {error}
                    </motion.div>
                )}
            </AnimatePresence>

            {/* Recording Controls */}
            {!recordedBlob ? (
                <div className="flex flex-col items-center space-y-4">
                    {/* Recording Timer */}
                    {isRecording && (
                        <motion.div
                            initial={{ opacity: 0, scale: 0.9 }}
                            animate={{ opacity: 1, scale: 1 }}
                            className="text-2xl font-mono text-text-primary dark:text-text-dark-primary"
                        >
                            {formatTime(recordingTime)}
                        </motion.div>
                    )}

                    {/* Recording Indicator */}
                    {isRecording && (
                        <motion.div
                            animate={{ scale: [1, 1.2, 1] }}
                            transition={{ repeat: Infinity, duration: 1.5 }}
                            className="w-4 h-4 rounded-full bg-red-500"
                        />
                    )}

                    {/* Main Controls */}
                    <div className="flex items-center gap-4">
                        {!isRecording ? (
                            <motion.button
                                whileHover={{ scale: 1.05 }}
                                whileTap={{ scale: 0.95 }}
                                onClick={startRecording}
                                className="w-16 h-16 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 transition-colors shadow-lg"
                            >
                                <Mic className="w-7 h-7" />
                            </motion.button>
                        ) : (
                            <>
                                <motion.button
                                    whileHover={{ scale: 1.05 }}
                                    whileTap={{ scale: 0.95 }}
                                    onClick={pauseRecording}
                                    className="w-12 h-12 rounded-full bg-surface-200 dark:bg-dark-300 text-text-primary dark:text-text-dark-primary flex items-center justify-center hover:bg-surface-300 dark:hover:bg-dark-200 transition-colors"
                                >
                                    {isPaused ? <Play className="w-5 h-5" /> : <Pause className="w-5 h-5" />}
                                </motion.button>
                                <motion.button
                                    whileHover={{ scale: 1.05 }}
                                    whileTap={{ scale: 0.95 }}
                                    onClick={stopRecording}
                                    className="w-16 h-16 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 transition-colors shadow-lg"
                                >
                                    <Square className="w-6 h-6" />
                                </motion.button>
                            </>
                        )}
                    </div>

                    {/* Instructions */}
                    <p className="text-sm text-text-muted dark:text-text-dark-muted text-center">
                        {isRecording
                            ? isPaused
                                ? 'التسجيل متوقف مؤقتاً. اضغط للمتابعة.'
                                : 'قم بتلاوة الآيات بصوت واضح...'
                            : 'اضغط على الزر الأحمر لبدء التسميع'}
                    </p>
                </div>
            ) : (
                /* Playback Controls */
                <div className="space-y-4">
                    <audio
                        ref={audioRef}
                        src={recordedUrl}
                        onTimeUpdate={() => setCurrentTime(audioRef.current?.currentTime || 0)}
                        onLoadedMetadata={() => setDuration(audioRef.current?.duration || 0)}
                        onEnded={() => setIsPlaying(false)}
                    />

                    {/* Playback UI */}
                    <div className="flex items-center gap-3 p-4 rounded-xl bg-surface-100 dark:bg-dark-300">
                        <motion.button
                            whileHover={{ scale: 1.05 }}
                            whileTap={{ scale: 0.95 }}
                            onClick={handlePlayPause}
                            className="w-12 h-12 rounded-full bg-primary-500 text-white flex items-center justify-center hover:bg-primary-600 transition-colors shadow-lg"
                        >
                            {isPlaying ? <Pause className="w-5 h-5" /> : <Play className="w-5 h-5 mr-0.5" />}
                        </motion.button>

                        <div className="flex-1 space-y-1">
                            <div className="h-2 bg-surface-200 dark:bg-dark-200 rounded-full overflow-hidden">
                                <motion.div
                                    className="h-full bg-primary-500 rounded-full"
                                    style={{ width: `${duration ? (currentTime / duration) * 100 : 0}%` }}
                                />
                            </div>
                            <div className="flex justify-between text-xs text-text-muted dark:text-text-dark-muted">
                                <span>{formatTime(currentTime)}</span>
                                <span>{formatTime(duration)}</span>
                            </div>
                        </div>
                    </div>

                    {/* Action Buttons */}
                    {!verificationResult && (
                        <div className="flex gap-3">
                            <motion.button
                                whileHover={{ scale: 1.02 }}
                                whileTap={{ scale: 0.98 }}
                                onClick={resetRecording}
                                className="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-surface-300 dark:border-dark-200 text-text-primary dark:text-text-dark-primary hover:bg-surface-100 dark:hover:bg-dark-300 transition-colors"
                            >
                                <RotateCcw className="w-5 h-5" />
                                إعادة التسجيل
                            </motion.button>
                            <motion.button
                                whileHover={{ scale: 1.02 }}
                                whileTap={{ scale: 0.98 }}
                                onClick={handleSubmit}
                                disabled={isVerifying}
                                className="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-primary-500 text-white hover:bg-primary-600 transition-colors disabled:opacity-60"
                            >
                                {isVerifying ? (
                                    <>
                                        <Loader2 className="w-5 h-5 animate-spin" />
                                        جاري التحقق...
                                    </>
                                ) : (
                                    <>
                                        <Check className="w-5 h-5" />
                                        إرسال للتحقق
                                    </>
                                )}
                            </motion.button>
                        </div>
                    )}

                    {/* Verification Result */}
                    {renderVerificationResult()}
                </div>
            )}
        </div>
    );
}
