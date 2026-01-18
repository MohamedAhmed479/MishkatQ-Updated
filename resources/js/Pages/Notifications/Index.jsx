import { Head, router, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Bell, Check, X, Trash2 } from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card, { CardContent, CardHeader } from '@/Components/UI/Card';
import Button from '@/Components/UI/Button';
import { useState } from 'react';

export default function NotificationsIndex({ notifications }) {
    const [selectedNotifications, setSelectedNotifications] = useState([]);
    const [processing, setProcessing] = useState(false);

    const handleMarkAsRead = async (notificationId) => {
        if (processing) return;
        
        setProcessing(true);
        try {
            const response = await fetch(`/app/notifications/${notificationId}/read`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json',
                },
            });

            if (response.ok) {
                router.reload({ only: ['notifications'] });
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        } finally {
            setProcessing(false);
        }
    };

    const handleMarkAllAsRead = async () => {
        if (processing) return;
        
        setProcessing(true);
        const unreadIds = notifications.filter(n => !n.read_at).map(n => n.id);
        
        try {
            await Promise.all(
                unreadIds.map(id => 
                    fetch(`/app/notifications/${id}/read`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Accept': 'application/json',
                        },
                    })
                )
            );
            router.reload({ only: ['notifications'] });
        } catch (error) {
            console.error('Error marking all as read:', error);
        } finally {
            setProcessing(false);
        }
    };

    const unreadCount = notifications.filter(n => !n.read_at).length;
    const readNotifications = notifications.filter(n => n.read_at);
    const unreadNotifications = notifications.filter(n => !n.read_at);

    return (
        <MainLayout title="الإشعارات">
            <Head title="الإشعارات" />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                            الإشعارات
                        </h1>
                        <p className="text-text-muted dark:text-text-dark-muted mt-1">
                            {unreadCount > 0 ? `${unreadCount} إشعار غير مقروء` : 'جميع الإشعارات مقروءة'}
                        </p>
                    </div>
                    {unreadCount > 0 && (
                        <Button
                            onClick={handleMarkAllAsRead}
                            disabled={processing}
                            variant="outline"
                            size="sm"
                        >
                            قراءة الكل
                        </Button>
                    )}
                </div>

                {/* Notifications List */}
                {notifications.length > 0 ? (
                    <div className="space-y-4">
                        {/* Unread Notifications */}
                        {unreadNotifications.length > 0 && (
                            <div>
                                <h2 className="text-sm font-semibold text-text-secondary dark:text-text-dark-secondary mb-3">
                                    غير مقروء ({unreadNotifications.length})
                                </h2>
                                <div className="space-y-2">
                                    {unreadNotifications.map((notification) => (
                                        <NotificationCard
                                            key={notification.id}
                                            notification={notification}
                                            onMarkAsRead={handleMarkAsRead}
                                            processing={processing}
                                            unread={true}
                                        />
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* Read Notifications */}
                        {readNotifications.length > 0 && (
                            <div>
                                <h2 className="text-sm font-semibold text-text-secondary dark:text-text-dark-secondary mb-3">
                                    مقروء ({readNotifications.length})
                                </h2>
                                <div className="space-y-2">
                                    {readNotifications.map((notification) => (
                                        <NotificationCard
                                            key={notification.id}
                                            notification={notification}
                                            onMarkAsRead={handleMarkAsRead}
                                            processing={processing}
                                            unread={false}
                                        />
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                ) : (
                    <Card>
                        <CardContent className="text-center py-12">
                            <div className="w-16 h-16 mx-auto mb-4 bg-surface-200 dark:bg-dark-300 rounded-full flex items-center justify-center">
                                <Bell className="w-8 h-8 text-text-muted" />
                            </div>
                            <p className="text-text-muted dark:text-text-dark-muted">
                                لا توجد إشعارات
                            </p>
                        </CardContent>
                    </Card>
                )}
            </div>
        </MainLayout>
    );
}

function NotificationCard({ notification, onMarkAsRead, processing, unread }) {
    const formatDate = (dateString) => {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        if (diffInSeconds < 60) return 'الآن';
        if (diffInSeconds < 3600) return `منذ ${Math.floor(diffInSeconds / 60)} دقيقة`;
        if (diffInSeconds < 86400) return `منذ ${Math.floor(diffInSeconds / 3600)} ساعة`;
        if (diffInSeconds < 604800) return `منذ ${Math.floor(diffInSeconds / 86400)} يوم`;
        
        return date.toLocaleDateString('ar-EG', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    return (
        <motion.div
            initial={{ opacity: 0, y: 10 }}
            animate={{ opacity: 1, y: 0 }}
            className={`
                bg-white dark:bg-dark-400 rounded-xl p-4 border transition-colors
                ${unread 
                    ? 'border-primary-300 dark:border-primary-700 bg-primary-50/30 dark:bg-primary-900/10' 
                    : 'border-surface-200 dark:border-dark-300'
                }
            `}
        >
            <div className="flex items-start gap-3">
                <div className={`
                    w-2 h-2 rounded-full mt-2 flex-shrink-0
                    ${unread ? 'bg-primary-500' : 'bg-transparent'}
                `} />
                
                <div className="flex-1 min-w-0">
                    <h3 className="font-semibold text-text-primary dark:text-text-dark-primary mb-1">
                        {notification.data?.title || 'إشعار جديد'}
                    </h3>
                    <p className="text-sm text-text-muted dark:text-text-dark-muted mb-2">
                        {notification.data?.message || ''}
                    </p>
                    <p className="text-xs text-text-muted dark:text-text-dark-muted">
                        {formatDate(notification.created_at)}
                    </p>
                </div>

                {unread && (
                    <button
                        onClick={() => onMarkAsRead(notification.id)}
                        disabled={processing}
                        className="p-2 rounded-lg hover:bg-surface-200 dark:hover:bg-dark-300 transition-colors flex-shrink-0"
                        title="قراءة"
                    >
                        <Check className="w-4 h-4 text-text-muted" />
                    </button>
                )}
            </div>
        </motion.div>
    );
}