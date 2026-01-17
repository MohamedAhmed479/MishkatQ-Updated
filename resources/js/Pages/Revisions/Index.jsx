import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { RefreshCw, AlertTriangle, ChevronLeft, Clock, CheckCircle } from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card, { CardContent, CardHeader } from '@/Components/UI/Card';
import Button from '@/Components/UI/Button';

export default function RevisionsIndex({ todayRevisions, overdueRevisions }) {
    const hasRevisions = todayRevisions.length > 0 || overdueRevisions.length > 0;

    return (
        <MainLayout title="المراجعات">
            <Head title="المراجعات" />

            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                        المراجعات
                    </h1>
                    <p className="text-text-muted dark:text-text-dark-muted mt-1">
                        راجع ما حفظته لتثبيت الحفظ
                    </p>
                </div>

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
                                                {overdueRevisions.length} مراجعة تحتاج اهتمامك
                                            </p>
                                        </div>
                                    </div>
                                </CardHeader>
                                <CardContent className="divide-y divide-surface-200 dark:divide-dark-300">
                                    {overdueRevisions.map((revision) => (
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
                                    {todayRevisions.map((revision) => (
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

function RevisionItem({ revision, overdue = false }) {
    const isCompleted = revision.status === 'completed';

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

                <div className="text-left">
                    <span className={`
                        text-xs px-2 py-1 rounded-lg
                        ${overdue
                            ? 'bg-warning/10 text-warning'
                            : 'bg-surface-200 dark:bg-dark-300 text-text-muted dark:text-text-dark-muted'
                        }
                    `}>
                        المرة {revision.repetition_number}
                    </span>
                </div>

                <ChevronLeft className="w-5 h-5 text-text-muted opacity-0 group-hover:opacity-100 transition-opacity" />
            </motion.div>
        </Link>
    );
}
