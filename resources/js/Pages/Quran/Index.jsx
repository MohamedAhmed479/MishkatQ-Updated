import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import { motion } from 'framer-motion';
import { Search, BookOpen, ChevronLeft, MapPin, Award, Star, Book } from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card from '@/Components/UI/Card';
import Input from '@/Components/UI/Input';

export default function QuranIndex({ chapters, stats }) {
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

    const progressPercentage = Math.round((stats.total_memorized / stats.total_verses) * 100);

    return (
        <MainLayout title="تصفح القرآن">
            <Head title="تصفح القرآن الكريم" />

            <div className="space-y-8">
                {/* Stats Summary */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <Card gradient className="p-6 text-white">
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                                <Star className="w-6 h-6" />
                            </div>
                            <div>
                                <p className="text-white/70 text-sm">إجمالي الحفظ</p>
                                <h2 className="text-2xl font-bold">{stats.total_memorized} آية</h2>
                            </div>
                        </div>
                        <div className="mt-4 h-2 bg-white/20 rounded-full overflow-hidden">
                            <div className="h-full bg-white" style={{ width: `${progressPercentage}%` }} />
                        </div>
                        <p className="mt-2 text-xs text-white/60 text-right">{progressPercentage}% من القرآن الكريم</p>
                    </Card>

                    <Card className="p-6 border-2 border-primary-100 dark:border-primary-900/30">
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 bg-primary-50 dark:bg-primary-900/20 rounded-2xl flex items-center justify-center">
                                <Award className="w-6 h-6 text-primary-600" />
                            </div>
                            <div>
                                <p className="text-text-muted text-sm font-sans">سور مكتملة</p>
                                <h2 className="text-2xl font-bold">{stats.memorized_chapters} سورة</h2>
                            </div>
                        </div>
                    </Card>

                    <Card className="p-6 border-2 border-accent-100 dark:border-accent-900/30">
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 bg-accent-50 dark:bg-accent-900/20 rounded-2xl flex items-center justify-center">
                                <Book className="w-6 h-6 text-accent-600" />
                            </div>
                            <div>
                                <p className="text-text-muted text-sm font-sans">آخر قراءة</p>
                                <h2 className="text-lg font-bold">سورة البقرة</h2>
                            </div>
                        </div>
                    </Card>
                </div>

                {/* Header & Search */}
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <h1 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary font-sans">
                            سور القرآن
                        </h1>
                        <p className="text-text-muted dark:text-text-dark-muted mt-1 font-sans">
                            {chapters.length} سورة في كتاب الله
                        </p>
                    </div>

                    <div className="flex flex-col md:flex-row gap-4 flex-1 max-w-2xl">
                        <div className="flex-1 font-sans">
                            <Input
                                placeholder="ابحث باسم السورة أو رقمها..."
                                icon={Search}
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                            />
                        </div>
                        <div className="flex bg-surface-100 dark:bg-dark-300 rounded-xl p-1 font-sans">
                            {[
                                { value: 'all', label: 'الكل' },
                                { value: 'meccan', label: 'مكية' },
                                { value: 'medinan', label: 'مدنية' },
                            ].map((f) => (
                                <button
                                    key={f.value}
                                    onClick={() => setFilter(f.value)}
                                    className={`
                                        px-4 py-2 rounded-lg text-xs font-bold transition-all
                                        ${filter === f.value
                                            ? 'bg-white dark:bg-dark-200 shadow-sm text-primary-600'
                                            : 'text-text-muted hover:text-text-primary'
                                        }
                                    `}
                                >
                                    {f.label}
                                </button>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Chapters Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 font-sans">
                    {filteredChapters.map((chapter, index) => (
                        <motion.div
                            key={chapter.id}
                            initial={{ opacity: 0, scale: 0.95 }}
                            animate={{ opacity: 1, scale: 1 }}
                            transition={{ delay: index * 0.01 }}
                        >
                            <Link href={`/app/quran/chapter/${chapter.id}`}>
                                <Card hover className="p-4 group relative overflow-hidden">
                                    {chapter.progress > 0 && (
                                        <div 
                                            className="absolute bottom-0 right-0 h-1 bg-primary-500 transition-all group-hover:h-2" 
                                            style={{ width: `${(chapter.progress / chapter.verses_count) * 100}%` }}
                                        />
                                    )}
                                    <div className="flex items-center gap-4">
                                        <div className="w-12 h-12 rounded-2xl bg-surface-100 dark:bg-dark-300 flex items-center justify-center group-hover:bg-primary-500 group-hover:text-white transition-all">
                                            <span className="text-lg font-bold">
                                                {chapter.id}
                                            </span>
                                        </div>
                                        <div className="flex-1">
                                            <div className="flex items-center justify-between">
                                                <h3 className="font-bold text-text-primary dark:text-text-dark-primary text-lg font-amiri">
                                                    {chapter.name_arabic}
                                                </h3>
                                                <span className={`
                                                    text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider
                                                    ${chapter.revelation_place === 'makkah'
                                                        ? 'bg-accent-100 dark:bg-accent-900/30 text-accent-600'
                                                        : 'bg-primary-100 dark:bg-primary-900/30 text-primary-600'
                                                    }
                                                `}>
                                                    {chapter.revelation_place === 'makkah' ? 'مكية' : 'مدنية'}
                                                </span>
                                            </div>
                                            <div className="flex items-center gap-2 mt-1">
                                                <span className="text-xs text-text-muted">
                                                    {chapter.name_english}
                                                </span>
                                                <span className="text-xs text-text-light">
                                                    • {chapter.verses_count} آية
                                                </span>
                                            </div>
                                        </div>
                                        <ChevronLeft className="w-5 h-5 text-text-muted group-hover:text-primary-500 transform group-hover:-translate-x-1 transition-all" />
                                    </div>
                                </Card>
                            </Link>
                        </motion.div>
                    ))}
                </div>

                {filteredChapters.length === 0 && (
                    <div className="text-center py-20 bg-surface-50 dark:bg-dark-300 rounded-[40px]">
                        <BookOpen className="w-16 h-16 mx-auto mb-4 text-text-muted opacity-20" />
                        <p className="text-text-muted dark:text-text-dark-muted font-bold">
                            لم نجد أي سورة بهذا الاسم
                        </p>
                    </div>
                )}
            </div>
        </MainLayout>
    );
}
