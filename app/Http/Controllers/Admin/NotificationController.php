<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification as Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * عرض قائمة التنبيهات
     */
    public function index(Request $request): View
    {
        $search = (string) $request->string('q');
        $status = (string) $request->string('status'); // read | unread | ''
        $notifiableType = (string) $request->string('notifiable_type');
        $sortOrder = (string) $request->string('sort_order', 'desc'); // asc | desc

        $notificationsQuery = Notification::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('type', 'like', "%{$search}%")
                      ->orWhere('data', 'like', "%{$search}%");
                });
            })
            ->when($status === 'read', function ($q) {
                $q->whereNotNull('read_at');
            })
            ->when($status === 'unread', function ($q) {
                $q->whereNull('read_at');
            })
            ->when($notifiableType !== '', function ($q) use ($notifiableType) {
                $q->where('notifiable_type', $notifiableType);
            })
            ->orderBy('created_at', $sortOrder);

        $notifications = $notificationsQuery->paginate(20)->withQueryString();

        // قيم الفلاتر المساعدة
        $notifiableTypes = Notification::query()
            ->select('notifiable_type')
            ->distinct()
            ->pluck('notifiable_type');

        $stats = [
            'total' => Notification::count(),
            'read' => Notification::whereNotNull('read_at')->count(),
            'unread' => Notification::whereNull('read_at')->count(),
        ];

        return view('admin.notifications.index', compact(
            'notifications', 'search', 'status', 'notifiableType', 'sortOrder', 'notifiableTypes', 'stats'
        ));
    }

    /**
     * عرض تفاصيل التنبيه
     */
    public function show(Notification $notification): View
    {
        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * حذف تنبيه
     */
    public function destroy(Notification $notification): RedirectResponse
    {
        $notification->delete();

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'تم حذف التنبيه بنجاح');
    }

    /**
     * تعليم التنبيه كمقروء
     */
    public function markAsRead(Notification $notification): RedirectResponse
    {
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return back()->with('success', 'تم تعليم التنبيه كمقروء');
    }

    /**
     * تعليم التنبيه كغير مقروء
     */
    public function markAsUnread(Notification $notification): RedirectResponse
    {
        if (!is_null($notification->read_at)) {
            $notification->forceFill(['read_at' => null])->save();
        }

        return back()->with('success', 'تم تعليم التنبيه كغير مقروء');
    }

    /**
     * حذف مجموعة من التنبيهات
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);

        if (is_array($ids) && !empty($ids)) {
            Notification::whereIn('id', $ids)->delete();
        }

        return back()->with('success', 'تم حذف التنبيهات المحددة');
    }

    /**
     * تعليم مجموعة كمقروء
     */
    public function bulkMarkAsRead(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);

        if (is_array($ids) && !empty($ids)) {
            Notification::whereIn('id', $ids)->update(['read_at' => now()]);
        }

        return back()->with('success', 'تم تعليم التنبيهات المحددة كمقروءة');
    }

    /**
     * تعليم مجموعة كغير مقروء
     */
    public function bulkMarkAsUnread(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);

        if (is_array($ids) && !empty($ids)) {
            Notification::whereIn('id', $ids)->update(['read_at' => null]);
        }

        return back()->with('success', 'تم تعليم التنبيهات المحددة كغير مقروءة');
    }
}


