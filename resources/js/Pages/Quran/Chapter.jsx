import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ArrowRight, BookOpen, MapPin, Plus } from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card, { CardContent, CardHeader } from '@/Components/UI/Card';
import Button from '@/Components/UI/Button';

export default function ChapterView({ chapter, verses }) {
    return (
        <MainLayout title={chapter.name_arabic}>
            <Head title={`سورة ${chapter.name_arabic}`} />

            <div className="space-y-6">
                {/* Back Button */}
                <Link
                    href="/app/quran"
                    className="inline-flex items-center gap-2 text-text-muted dark:text-text-dark-muted hover:text-text-primary dark:hover:text-text-dark-primary transition-colors"
                >
                    <ArrowRight className="w-4 h-4" />
                    <span>العودة للسور</span>
                </Link>

                {/* Chapter Header */}
                <Card gradient className="p-6 text-center">
                    <motion.div
                        initial={{ scale: 0 }}
                        animate={{ scale: 1 }}
                        className="w-20 h-20 mx-auto mb-4 bg-white/20 rounded-full flex items-center justify-center"
                    >
                        <BookOpen className="w-10 h-10" />
                    </motion.div>
                    <h1 className="text-3xl font-bold mb-2">
                        سورة {chapter.name_arabic}
                    </h1>
                    <p className="text-white/80 mb-4">
                        {chapter.name_english}
                    </p>
                    <div className="flex items-center justify-center gap-4 text-sm text-white/70">
                        <span>{chapter.verses_count} آية</span>
                        <span>•</span>
                        <span className="flex items-center gap-1">
                            <MapPin className="w-4 h-4" />
                            {chapter.revelation_place === 'makkah' ? 'مكية' : 'مدنية'}
                        </span>
                    </div>
                </Card>

                {/* Add to Plan Button */}
                <div className="flex justify-center">
                    <Link href={`/app/plans/create?chapter=${chapter.id}`}>
                        <Button icon={Plus} variant="outline">
                            إضافة للخطة
                        </Button>
                    </Link>
                </div>

                {/* Verses */}
                <Card>
                    <CardHeader>
                        <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                            الآيات
                        </h2>
                    </CardHeader>
                    <CardContent>
                        {/* Bismillah */}
                        {chapter.id !== 1 && chapter.id !== 9 && (
                            <div className="text-center py-6 mb-6 border-b border-surface-200 dark:border-dark-300">
                                <p className="font-amiri text-2xl text-text-primary dark:text-text-dark-primary">
                                    بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                                </p>
                            </div>
                        )}

                        {/* Verses List */}
                        <div className="space-y-4">
                            {verses.map((verse, index) => (
                                <motion.div
                                    key={verse.id}
                                    initial={{ opacity: 0, y: 10 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    transition={{ delay: index * 0.02 }}
                                    className="flex gap-4 p-4 rounded-2xl bg-surface-50 dark:bg-dark-300 hover:bg-surface-100 dark:hover:bg-dark-200 transition-colors"
                                >
                                    <div className="shrink-0">
                                        <span className="inline-flex items-center justify-center w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 font-bold">
                                            {verse.verse_number}
                                        </span>
                                    </div>
                                    <div className="flex-1">
                                        <p className="font-amiri text-xl md:text-2xl leading-loose text-text-primary dark:text-text-dark-primary text-right">
                                            {verse.text}
                                        </p>
                                    </div>
                                </motion.div>
                            ))}
                        </div>
                    </CardContent>
                </Card>
            </div>
        </MainLayout>
    );
}
