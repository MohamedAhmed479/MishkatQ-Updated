import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import { motion } from 'framer-motion';
import { Search, BookOpen, ChevronLeft, MapPin } from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card from '@/Components/UI/Card';
import Input from '@/Components/UI/Input';

export default function QuranIndex({ chapters }) {
    const [searchTerm, setSearchTerm] = useState('');
    const [filter, setFilter] = useState('all');

    const filteredChapters = chapters.filter(chapter => {
        const matchesSearch = 
            chapter.name_arabic.includes(searchTerm) ||
            chapter.name_english.toLowerCase().includes(searchTerm.toLowerCase()) ||
            chapter.id.toString() === searchTerm;
        
        const matchesFilter = 
            filter === 'all' ||
            (filter === 'meccan' && chapter.revelation_place === 'makkah') ||
            (filter === 'medinan' && chapter.revelation_place === 'madinah');

        return matchesSearch && matchesFilter;
    });

    return (
        <MainLayout title="تصفح القرآن">
            <Head title="تصفح القرآن الكريم" />

            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                        القرآن الكريم
                    </h1>
                    <p className="text-text-muted dark:text-text-dark-muted mt-1">
                        تصفح سور القرآن الكريم
                    </p>
                </div>

                {/* Search & Filters */}
                <div className="flex flex-col md:flex-row gap-4">
                    <div className="flex-1">
                        <Input
                            placeholder="ابحث عن سورة..."
                            icon={Search}
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                        />
                    </div>
                    <div className="flex gap-2">
                        {[
                            { value: 'all', label: 'الكل' },
                            { value: 'meccan', label: 'مكية' },
                            { value: 'medinan', label: 'مدنية' },
                        ].map((f) => (
                            <button
                                key={f.value}
                                onClick={() => setFilter(f.value)}
                                className={`
                                    px-4 py-2 rounded-xl text-sm font-medium transition-all
                                    ${filter === f.value
                                        ? 'bg-primary-500 text-white'
                                        : 'bg-surface-200 dark:bg-dark-300 text-text-secondary dark:text-text-dark-secondary hover:bg-surface-300 dark:hover:bg-dark-200'
                                    }
                                `}
                            >
                                {f.label}
                            </button>
                        ))}
                    </div>
                </div>

                {/* Chapters Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {filteredChapters.map((chapter, index) => (
                        <motion.div
                            key={chapter.id}
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ delay: index * 0.02 }}
                        >
                            <Link href={`/app/quran/chapter/${chapter.id}`}>
                                <Card hover className="p-4">
                                    <div className="flex items-center gap-4">
                                        <div className="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                            <span className="text-lg font-bold text-primary-600 dark:text-primary-400">
                                                {chapter.id}
                                            </span>
                                        </div>
                                        <div className="flex-1">
                                            <h3 className="font-bold text-text-primary dark:text-text-dark-primary">
                                                {chapter.name_arabic}
                                            </h3>
                                            <div className="flex items-center gap-2 mt-1">
                                                <span className="text-xs text-text-muted dark:text-text-dark-muted">
                                                    {chapter.name_english}
                                                </span>
                                                <span className="text-xs text-text-light dark:text-text-dark-muted">
                                                    • {chapter.verses_count} آية
                                                </span>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-2">
                                            <span className={`
                                                text-xs px-2 py-1 rounded-lg
                                                ${chapter.revelation_place === 'makkah'
                                                    ? 'bg-accent-100 dark:bg-accent-900/30 text-accent-600 dark:text-accent-400'
                                                    : 'bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400'
                                                }
                                            `}>
                                                {chapter.revelation_place === 'makkah' ? 'مكية' : 'مدنية'}
                                            </span>
                                            <ChevronLeft className="w-5 h-5 text-text-muted" />
                                        </div>
                                    </div>
                                </Card>
                            </Link>
                        </motion.div>
                    ))}
                </div>

                {filteredChapters.length === 0 && (
                    <div className="text-center py-12">
                        <BookOpen className="w-12 h-12 mx-auto mb-4 text-text-muted" />
                        <p className="text-text-muted dark:text-text-dark-muted">
                            لم يتم العثور على نتائج
                        </p>
                    </div>
                )}
            </div>
        </MainLayout>
    );
}
