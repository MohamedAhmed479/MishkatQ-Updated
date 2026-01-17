import { motion } from 'framer-motion';
import TafsirSection from './TafsirSection';
import AudioPlayer from './AudioPlayer';

export default function VerseCard({
    verse,
    index,
    isTafsirOpen,
    onToggleTafsir,
    selectedReciterId,
    onReciterChange,
    isCompleted = false,
}) {
    const hasRecitations = verse.recitations && verse.recitations.length > 0;

    return (
        <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: Math.min(index * 0.05, 0.5) }}
            className={`
                bg-white dark:bg-dark-400 rounded-2xl p-4 md:p-6 border shadow-sm
                ${isCompleted
                    ? 'border-primary-300 dark:border-primary-700 bg-primary-50/50 dark:bg-primary-900/10'
                    : 'border-surface-300 dark:border-dark-300'
                }
                hover:shadow-md transition-shadow duration-200
            `}
        >
            {/* Verse Header */}
            <div className="flex items-center justify-between mb-4">
                <span className="inline-flex items-center justify-center w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 font-bold text-sm">
                    {verse.verse_number}
                </span>
                
                {/* Compact Audio Player */}
                {hasRecitations && (
                    <AudioPlayer
                        recitations={verse.recitations}
                        selectedReciterId={selectedReciterId}
                        onReciterChange={onReciterChange}
                        isCompact={true}
                    />
                )}
            </div>

            {/* Verse Text */}
            <p
                className="font-amiri text-xl sm:text-2xl md:text-3xl leading-loose text-text-primary dark:text-text-dark-primary text-center px-2"
                dir="rtl"
            >
                {verse.text}
            </p>

            {/* Page Number */}
            {verse.page_number && (
                <div className="mt-3 text-center">
                    <span className="text-xs text-text-muted dark:text-text-dark-muted bg-surface-100 dark:bg-dark-300 px-2 py-1 rounded-full">
                        صفحة {verse.page_number}
                    </span>
                </div>
            )}

            {/* Tafsir Section */}
            <TafsirSection
                tafsir={verse.tafsir}
                isOpen={isTafsirOpen}
                onToggle={onToggleTafsir}
            />
        </motion.div>
    );
}
