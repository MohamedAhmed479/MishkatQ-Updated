<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Get audit logs for the authenticated user
     */
    public function getUserAuditLogs(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $query = AuditLog::where('user_id', $user->id)
                ->orderBy('performed_at', 'desc');

            // Apply filters
            if ($request->has('action')) {
                $query->where('action', $request->action);
            }

            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            if ($request->has('severity')) {
                $query->where('severity', $request->severity);
            }

            if ($request->has('date_from')) {
                $query->whereDate('performed_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('performed_at', '<=', $request->date_to);
            }

            $logs = $query->paginate(20);

            // Log the audit view request
            $this->auditService->log(
                'view_audit_logs',
                'User viewed their audit logs',
                null,
                null,
                null,
                'low',
                'admin',
                false,
                [
                    'filters' => $request->only(['action', 'category', 'severity', 'date_from', 'date_to']),
                    'logs_count' => $logs->total()
                ]
            );

            return ApiResponse::withPagination($logs, 'logs', 'تم جلب سجلات المراجعة بنجاح');

        } catch (\Throwable $e) {
            return ApiResponse::error('حدث خطأ أثناء جلب سجلات المراجعة', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get audit statistics for the user
     */
    public function getUserAuditStats(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $days = $request->input('days', 30);

            $stats = [
                'total_activities' => AuditLog::where('user_id', $user->id)
                    ->where('performed_at', '>=', now()->subDays($days))
                    ->count(),
                    
                'successful_activities' => AuditLog::where('user_id', $user->id)
                    ->where('performed_at', '>=', now()->subDays($days))
                    ->where('status', 'success')
                    ->count(),
                    
                'failed_activities' => AuditLog::where('user_id', $user->id)
                    ->where('performed_at', '>=', now()->subDays($days))
                    ->where('status', 'failed')
                    ->count(),
                    
                'activities_by_category' => AuditLog::where('user_id', $user->id)
                    ->where('performed_at', '>=', now()->subDays($days))
                    ->selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->get(),
                    
                'recent_activities' => AuditLog::where('user_id', $user->id)
                    ->orderBy('performed_at', 'desc')
                    ->limit(10)
                    ->get(['action', 'description', 'performed_at', 'status']),
                    
                'most_common_actions' => AuditLog::where('user_id', $user->id)
                    ->where('performed_at', '>=', now()->subDays($days))
                    ->selectRaw('action, COUNT(*) as count')
                    ->groupBy('action')
                    ->orderByDesc('count')
                    ->limit(5)
                    ->get(),
            ];

            return ApiResponse::success($stats, 'تم جلب إحصائيات المراجعة بنجاح');

        } catch (\Throwable $e) {
            return ApiResponse::error('حدث خطأ أثناء جلب إحصائيات المراجعة', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get specific audit log details
     */
    public function getAuditLogDetails(int $logId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $log = AuditLog::where('id', $logId)
                ->where('user_id', $user->id)
                ->first();

            if (!$log) {
                return ApiResponse::notFound('سجل المراجعة غير موجود');
            }

            // Log the audit log view
            $this->auditService->log(
                'view_audit_log_details',
                "User viewed audit log details: {$logId}",
                null,
                null,
                null,
                'low',
                'admin',
                false,
                ['audit_log_id' => $logId]
            );

            return ApiResponse::success($log, 'تم جلب تفاصيل سجل المراجعة بنجاح');

        } catch (\Throwable $e) {
            return ApiResponse::error('حدث خطأ أثناء جلب تفاصيل سجل المراجعة', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get system-wide audit statistics (Admin only)
     */
    public function getSystemAuditStats(Request $request): JsonResponse
    {
        try {
            // Check if user is admin (you may need to implement this check)
            $user = Auth::user();
            // if (!$user->isAdmin()) {
            //     return ApiResponse::forbidden('غير مصرح لك بالوصول لهذه البيانات');
            // }

            $days = $request->input('days', 30);
            $stats = $this->auditService->getStats($days);

            // Log admin audit access
            $this->auditService->logAdmin(
                'view_system_audit_stats',
                null,
                'Admin viewed system audit statistics'
            );

            return ApiResponse::success($stats, 'تم جلب إحصائيات النظام بنجاح');

        } catch (\Throwable $e) {
            return ApiResponse::error('حدث خطأ أثناء جلب إحصائيات النظام', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Export audit logs for the user
     */
    public function exportUserAuditLogs(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $query = AuditLog::where('user_id', $user->id)
                ->orderBy('performed_at', 'desc');

            // Apply date filters for export
            if ($request->has('date_from')) {
                $query->whereDate('performed_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('performed_at', '<=', $request->date_to);
            }

            $logs = $query->get(['action', 'description', 'performed_at', 'status', 'category', 'severity']);

            // Log the export request
            $this->auditService->log(
                'export_audit_logs',
                'User exported their audit logs',
                null,
                null,
                null,
                'medium',
                'admin',
                true,
                [
                    'exported_count' => $logs->count(),
                    'date_range' => [
                        'from' => $request->date_from,
                        'to' => $request->date_to
                    ]
                ]
            );

            return ApiResponse::success([
                'logs' => $logs,
                'export_count' => $logs->count(),
                'exported_at' => now()->toISOString()
            ], 'تم تصدير سجلات المراجعة بنجاح');

        } catch (\Throwable $e) {
            return ApiResponse::error('حدث خطأ أثناء تصدير سجلات المراجعة', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
