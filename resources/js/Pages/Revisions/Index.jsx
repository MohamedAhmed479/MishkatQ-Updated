import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { 
    RefreshCw, 
    AlertTriangle, 
    ChevronLeft, 
    Clock, 
    CheckCircle,
    Brain,
    TrendingUp,
    Zap,
    Target,
    AlertCircle,
    BarChart3
} from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card, { CardContent, CardHeader } from '@/Components/UI/Card';
import Button from '@/Components/UI/Button';

export default function RevisionsIndex({ 
    todayRevisions, 
    overdueRevisions, 
    memoryStats, 
    memoryHeatmap,
    leeches 
}) {
    const hasRevisions = todayRevisions.length > 0 || overdueRevisions.length > 0;

    return (
        <MainLayout title="المراجعات الذكية">
            <Head title="المراجعات الذكية" />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary flex items-center gap-2">
                            <Brain className="w-7 h-7 text-primary-500" />
                            المراجعات الذكية
                        </h1>
                        <p className="text-text-muted dark:text-text-dark-muted mt-1">
                            نظام FSRS للتكرار المتباعد يساعدك على التثبيت الأمثل
                        </p>
                    </div>
                </div>

                {/* Memory Statistics Cards */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <StatCard
                        icon={Target}
                        label="إجمالي العناصر"
                        value={memoryStats?.total_items || 0}
                        color="primary"
                    />
                    <StatCard
                        icon={Zap}
                        label="جديدة"
                        value={memoryStats?.young_count || 0}
                        subtext="تحتاج مراجعة متكررة"
                        color="warning"
                    />
                    <StatCard
                        icon={TrendingUp}
                        label="مستقرة"
                        value={memoryStats?.mature_count || 0}
                        subtext="في طريقها للإتقان"
                        color="accent"
                    />
                    <StatCard
                        icon={CheckCircle}
                        label="متقنة"
                        value={memoryStats?.mastered_count || 0}
                        subtext="ثابتة في الذاكرة"
                        color="success"
                    />
                </div>

                {/* Memory Heatmap */}
                {memoryHeatmap && memoryHeatmap.length > 0 && (
                    <Card>
                        <CardHeader>
                            <div className="flex items-center gap-3">
                                <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center">
                                    <BarChart3 className="w-5 h-5 text-white" />
                                </div>
                                <div>
                                    <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                        خريطة قوة الذاكرة
                                    </h2>
                                    <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                        قوة حفظك لكل سورة
                                    </p>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                {memoryHeatmap.map((surah) => (
                                    <MemoryHeatmapItem key={surah.surah_id} surah={surah} />
                                ))}
                            </div>
                            <div className="flex items-center justify-center gap-4 mt-4 text-xs text-text-muted">
                                <div className="flex items-center gap-1">
                                    <div className="w-3 h-3 rounded bg-error/60" />
                                    <span>ضعيف</span>
                                </div>
                                <div className="flex items-center gap-1">
                                    <div className="w-3 h-3 rounded bg-warning/60" />
                                    <span>متوسط</span>
                                </div>
                                <div className="flex items-center gap-1">
                                    <div className="w-3 h-3 rounded bg-success/60" />
                                    <span>قوي</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                )}

                {/* Leeches Warning */}
                {leeches && leeches.length > 0 && (
                    <Card className="border-error/50">
                        <CardHeader className="bg-error/5">
                            <div className="flex items-center gap-3">
                                <div className="w-10 h-10 rounded-xl bg-error/10 flex items-center justify-center">
                                    <AlertCircle className="w-5 h-5 text-error" />
                                </div>
                                <div>
                                    <h2 className="font-bold text-error">عناصر تحتاج اهتمام خاص</h2>
                                    <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                        هذه الآيات تم نسيانها عدة مرات - ننصح بالتركيز على فهم معانيها
                                    </p>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent className="divide-y divide-surface-200 dark:divide-dark-300">
                            {leeches.slice(0, 3).map((leech) => (
                                <div key={leech.id} className="flex items-center gap-4 py-3">
                                    <div className="w-8 h-8 rounded-lg bg-error/10 flex items-center justify-center">
                                        <AlertCircle className="w-4 h-4 text-error" />
                                    </div>
                                    <div className="flex-1">
                                        <p className="font-medium text-text-primary dark:text-text-dark-primary">
                                            سورة {leech.chapter_name}
                                        </p>
                                        <p className="text-sm text-text-muted">
                                            الآيات {leech.start_verse} - {leech.end_verse}
                                        </p>
                                    </div>
                                    <span className="text-xs px-2 py-1 rounded-lg bg-error/10 text-error">
                                        {leech.fail_count} أخطاء
                                    </span>
                                </div>
                            ))}
                        </CardContent>
                    </Card>
                )}

                {hasRevisions ? (
                    <>
                        {/* Overdue Revisions */}
                        {overdueRevisions.length > 0 && (
                            <Card className="border-warning">
                                <CardHeader className="bg-warning/10">
                                    <div className="flex items-center gap-3">
                                        <div className="w-10 h-10 rounded-xl bg-warning/20 flex items-center justify-center">
                                            <AlertTriangle className="w-5 h-5 text-warning" />
                                        </div>
                                        <div>
                                            <h2 className="font-bold text-warning">مراجعات فائتة</h2>
                                            <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                                {overdueRevisions.length} مراجعة - مرتبة حسب الأولوية
                                            </p>
                                        </div>
                                    </div>
                                </CardHeader>
                                <CardContent className="divide-y divide-surface-200 dark:divide-dark-300">
                                    {overdueRevisions
                                        .sort((a, b) => b.priority_score - a.priority_score)
                                        .map((revision) => (
                                            <RevisionItem key={revision.id} revision={revision} overdue />
                                        ))}
                                </CardContent>
                            </Card>
                        )}

                        {/* Today's Revisions */}
                        {todayRevisions.length > 0 && (
                            <Card>
                                <CardHeader>
                                    <div className="flex items-center gap-3">
                                        <div className="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                            <Clock className="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                        </div>
                                        <div>
                                            <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                                مراجعات اليوم
                                            </h2>
                                            <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                                {todayRevisions.filter(r => r.status === 'pending').length} في الانتظار
                                            </p>
                                        </div>
                                    </div>
                                </CardHeader>
                                <CardContent className="divide-y divide-surface-200 dark:divide-dark-300">
                                    {todayRevisions
                                        .sort((a, b) => b.priority_score - a.priority_score)
                                        .map((revision) => (
                                            <RevisionItem key={revision.id} revision={revision} />
                                        ))}
                                </CardContent>
                            </Card>
                        )}
                    </>
                ) : (
                    <Card className="p-12 text-center">
                        <div className="w-20 h-20 mx-auto mb-6 bg-success/10 rounded-full flex items-center justify-center">
                            <CheckCircle className="w-10 h-10 text-success" />
                        </div>
                        <h3 className="text-xl font-bold text-text-primary dark:text-text-dark-primary mb-2">
                            لا توجد مراجعات
                        </h3>
                        <p className="text-text-muted dark:text-text-dark-muted mb-6">
                            أكملت جميع مراجعاتك. أحسنت!
                        </p>
                        <Link href="/app/dashboard">
                            <Button variant="outline">
                                العودة للرئيسية
                            </Button>
                        </Link>
                    </Card>
                )}
            </div>
        </MainLayout>
    );
}

function StatCard({ icon: Icon, label, value, subtext, color = 'primary' }) {
    const colorClasses = {
        primary: 'bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400',
        success: 'bg-success/10 text-success',
        warning: 'bg-warning/10 text-warning',
        accent: 'bg-accent-100 dark:bg-accent-900/30 text-accent-600 dark:text-accent-400',
        error: 'bg-error/10 text-error',
    };

    return (
        <Card className="p-4">
            <div className="flex items-start gap-3">
                <div className={`w-10 h-10 rounded-xl flex items-center justify-center ${colorClasses[color]}`}>
                    <Icon className="w-5 h-5" />
                </div>
                <div>
                    <p className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                        {value}
                    </p>
                    <p className="text-sm text-text-muted dark:text-text-dark-muted">{label}</p>
                    {subtext && (
                        <p className="text-xs text-text-muted dark:text-text-dark-muted mt-1">{subtext}</p>
                    )}
                </div>
            </div>
        </Card>
    );
}

function MemoryHeatmapItem({ surah }) {
    const strength = surah.strength_percentage || 0;
    
    // Determine color based on strength
    const getColor = () => {
        if (strength >= 80) return 'bg-success/60 border-success/30';
        if (strength >= 50) return 'bg-warning/60 border-warning/30';
        return 'bg-error/60 border-error/30';
    };

    return (
        <motion.div
            whileHover={{ scale: 1.05 }}
            className={`p-3 rounded-xl border-2 ${getColor()} cursor-pointer transition-all`}
        >
            <div className="text-center">
                <p className="font-bold text-sm text-text-primary dark:text-text-dark-primary truncate">
                    {surah.surah_name}
                </p>
                <p className="text-lg font-bold mt-1">
                    {strength}%
                </p>
                <div className="flex justify-center gap-1 mt-2">
                    <span className="text-xs px-1.5 py-0.5 rounded bg-warning/20 text-warning-700 dark:text-warning-300">
                        {surah.memory_states?.young || 0}
                    </span>
                    <span className="text-xs px-1.5 py-0.5 rounded bg-accent-100 dark:bg-accent-900/30 text-accent-700 dark:text-accent-300">
                        {surah.memory_states?.mature || 0}
                    </span>
                    <span className="text-xs px-1.5 py-0.5 rounded bg-success/20 text-success-700 dark:text-success-300">
                        {surah.memory_states?.mastered || 0}
                    </span>
                </div>
            </div>
        </motion.div>
    );
}

function RevisionItem({ revision, overdue = false }) {
    const isCompleted = revision.status === 'completed';
    
    // Get memory state badge color
    const getMemoryStateColor = () => {
        switch (revision.memory_state) {
            case 'young': return 'bg-warning/10 text-warning';
            case 'mature': return 'bg-accent-100 dark:bg-accent-900/30 text-accent-600 dark:text-accent-400';
            case 'mastered': return 'bg-success/10 text-success';
            default: return 'bg-surface-200 dark:bg-dark-300 text-text-muted';
        }
    };

    return (
        <Link href={`/app/revisions/${revision.id}`}>
            <motion.div
                whileHover={{ x: -4 }}
                className="flex items-center gap-4 py-4 group"
            >
                <div className={`
                    w-10 h-10 rounded-xl flex items-center justify-center
                    ${isCompleted
                        ? 'bg-success/10 text-success'
                        : overdue
                            ? 'bg-warning/10 text-warning'
                            : 'bg-accent-100 dark:bg-accent-900/30 text-accent-600 dark:text-accent-400'
                    }
                `}>
                    {isCompleted ? (
                        <CheckCircle className="w-5 h-5" />
                    ) : (
                        <RefreshCw className="w-5 h-5" />
                    )}
                </div>

                <div className="flex-1">
                    <h3 className={`font-bold ${isCompleted ? 'text-text-muted line-through' : 'text-text-primary dark:text-text-dark-primary'}`}>
                        سورة {revision.chapter_name}
                    </h3>
                    <p className="text-sm text-text-muted dark:text-text-dark-muted">
                        الآيات {revision.start_verse} - {revision.end_verse}
                    </p>
                </div>

                <div className="flex flex-col items-end gap-1">
                    {/* Memory State Badge */}
                    <span className={`text-xs px-2 py-1 rounded-lg ${getMemoryStateColor()}`}>
                        {revision.memory_state_ar}
                    </span>
                    
                    {/* Retrievability Indicator */}
                    <div className="flex items-center gap-1">
                        <div className="w-16 h-1.5 bg-surface-200 dark:bg-dark-300 rounded-full overflow-hidden">
                            <div 
                                className={`h-full rounded-full transition-all ${
                                    revision.retrievability >= 80 ? 'bg-success' :
                                    revision.retrievability >= 50 ? 'bg-warning' : 'bg-error'
                                }`}
                                style={{ width: `${revision.retrievability}%` }}
                            />
                        </div>
                        <span className="text-xs text-text-muted">{revision.retrievability}%</span>
                    </div>
                </div>

                <ChevronLeft className="w-5 h-5 text-text-muted opacity-0 group-hover:opacity-100 transition-opacity" />
            </motion.div>
        </Link>
    );
}
