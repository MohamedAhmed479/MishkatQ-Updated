<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class AuditLogController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            new ControllerMiddleware('permission:audit-logs.view', only: ['index','show']),
            new ControllerMiddleware('permission:audit-logs.create', only: ['create','store']),
            new ControllerMiddleware('permission:audit-logs.edit', only: ['edit','update']),
            new ControllerMiddleware('permission:audit-logs.delete', only: ['destroy', 'bulkDelete', 'bulkDeleteByDate']),
        ];
    }

    public function index(Request $request): View
    {
        $search = (string) $request->string('q');
        $severity = (string) $request->string('severity');
        $status = (string) $request->string('status');
        $category = (string) $request->string('category');
        $userId = (string) $request->string('user_id');
        $method = (string) $request->string('method');
        $isSensitive = $request->filled('is_sensitive') ? (bool) $request->boolean('is_sensitive') : null;
        $startDate = (string) $request->string('start_date');
        $endDate = (string) $request->string('end_date');

        $logs = AuditLog::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('action', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('user_name', 'like', "%{$search}%")
                      ->orWhere('user_email', 'like', "%{$search}%")
                      ->orWhere('model_type', 'like', "%{$search}%")
                      ->orWhere('model_name', 'like', "%{$search}%")
                      ->orWhere('ip_address', 'like', "%{$search}%")
                      ->orWhere('url', 'like', "%{$search}%");
                });
            })
            ->when($severity !== '', fn($q) => $q->where('severity', $severity))
            ->when($status !== '', fn($q) => $q->where('status', $status))
            ->when($category !== '', fn($q) => $q->where('category', $category))
            ->when($userId !== '', fn($q) => $q->where('user_id', $userId))
            ->when($method !== '', fn($q) => $q->where('method', strtoupper($method)))
            ->when(!is_null($isSensitive), fn($q) => $q->where('is_sensitive', $isSensitive))
            ->when($startDate !== '' && $endDate !== '', function ($q) use ($startDate, $endDate) {
                try {
                    $start = Carbon::parse($startDate)->startOfDay();
                    $end = Carbon::parse($endDate)->endOfDay();
                    $q->whereBetween('performed_at', [$start, $end]);
                } catch (\Throwable $e) {
                    // ignore invalid date filters
                }
            })
            ->latest('performed_at')
            ->paginate(20)
            ->withQueryString();

        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        $severities = ['low', 'medium', 'high', 'critical'];
        $statuses = ['success', 'failed', 'warning', 'info'];
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

        return view('admin.audit-logs.index', compact(
            'logs', 'search', 'severity', 'status', 'category', 'userId', 'isSensitive', 'startDate', 'endDate',
            'users', 'severities', 'statuses', 'methods', 'method'
        ));
    }

    public function create(): View
    {
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        $severities = ['low', 'medium', 'high', 'critical'];
        $statuses = ['success', 'failed', 'warning', 'info'];
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
        return view('admin.audit-logs.create', compact('users', 'severities', 'statuses', 'methods'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        // Decode JSON fields if provided as text
        $data['old_values'] = $this->decodeJsonField($request->input('old_values'));
        $data['new_values'] = $this->decodeJsonField($request->input('new_values'));
        $data['request_data'] = $this->decodeJsonField($request->input('request_data'));
        $data['metadata'] = $this->decodeJsonField($request->input('metadata'));

        // performed_at default
        if (empty($data['performed_at'])) {
            $data['performed_at'] = now();
        }

        AuditLog::create($data);

        return redirect()->route('admin.audit-logs.index')
            ->with('success', 'تم إنشاء السجل التدقيقي بنجاح');
    }

    public function show(AuditLog $audit_log): View
    {
        return view('admin.audit-logs.show', [
            'log' => $audit_log,
        ]);
    }

    public function edit(AuditLog $audit_log): View
    {
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        $severities = ['low', 'medium', 'high', 'critical'];
        $statuses = ['success', 'failed', 'warning', 'info'];
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
        return view('admin.audit-logs.edit', [
            'log' => $audit_log,
            'users' => $users,
            'severities' => $severities,
            'statuses' => $statuses,
            'methods' => $methods,
        ]);
    }

    public function update(Request $request, AuditLog $audit_log): RedirectResponse
    {
        $data = $this->validateData($request, updating: true);

        $data['old_values'] = $this->decodeJsonField($request->input('old_values'));
        $data['new_values'] = $this->decodeJsonField($request->input('new_values'));
        $data['request_data'] = $this->decodeJsonField($request->input('request_data'));
        $data['metadata'] = $this->decodeJsonField($request->input('metadata'));

        $audit_log->update($data);

        return redirect()->route('admin.audit-logs.index')
            ->with('success', 'تم تحديث السجل التدقيقي بنجاح');
    }

    public function destroy(AuditLog $audit_log): RedirectResponse
    {
        $audit_log->delete();

        return redirect()->route('admin.audit-logs.index')
            ->with('success', 'تم حذف السجل التدقيقي بنجاح');
    }

    /**
     * Bulk delete selected audit logs
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'log_ids' => 'required|string',
        ]);

        // تحويل string معرفات السجلات إلى array
        $logIds = explode(',', $request->log_ids);
        $logIds = array_filter(array_map('intval', $logIds)); // تنظيف وتحويل إلى أرقام

        if (empty($logIds)) {
            return redirect()->route('admin.audit-logs.index')
                ->with('error', 'لم يتم تحديد أي سجلات للحذف');
        }

        try {
            // التحقق من الصلاحيات للحذف
            if (!auth('admin')->user()->can('audit-logs.delete')) {
                return redirect()->route('admin.audit-logs.index')
                    ->with('error', 'ليس لديك صلاحية حذف السجلات');
            }

            // حذف السجلات المحددة
            $deletedCount = AuditLog::whereIn('id', $logIds)->delete();

            if ($deletedCount > 0) {
                return redirect()->route('admin.audit-logs.index')
                    ->with('success', "تم حذف {$deletedCount} سجل بنجاح");
            } else {
                return redirect()->route('admin.audit-logs.index')
                    ->with('error', 'لم يتم العثور على السجلات المحددة');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.audit-logs.index')
                ->with('error', 'حدث خطأ أثناء حذف السجلات: ' . $e->getMessage());
        }
    }

    /**
     * حذف السجلات حسب نطاق التاريخ
     */
    public function bulkDeleteByDate(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'time_option' => 'required|in:full_day,custom_time',
            'from_time' => 'nullable|required_if:time_option,custom_time|date_format:H:i',
            'to_time' => 'nullable|required_if:time_option,custom_time|date_format:H:i|after:from_time',
        ], [
            'from_date.required' => 'تاريخ البداية مطلوب',
            'from_date.date' => 'تاريخ البداية يجب أن يكون تاريخاً صحيحاً',
            'to_date.required' => 'تاريخ النهاية مطلوب',
            'to_date.date' => 'تاريخ النهاية يجب أن يكون تاريخاً صحيحاً',
            'to_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون مساوياً أو بعد تاريخ البداية',
            'time_option.required' => 'خيار الوقت مطلوب',
            'time_option.in' => 'خيار الوقت غير صحيح',
            'from_time.required_if' => 'وقت البداية مطلوب عند اختيار وقت محدد',
            'from_time.date_format' => 'وقت البداية يجب أن يكون بصيغة صحيحة (HH:MM)',
            'to_time.required_if' => 'وقت النهاية مطلوب عند اختيار وقت محدد',
            'to_time.date_format' => 'وقت النهاية يجب أن يكون بصيغة صحيحة (HH:MM)',
            'to_time.after' => 'وقت النهاية يجب أن يكون بعد وقت البداية',
        ]);

        try {
            // التحقق من الصلاحيات للحذف
            if (!auth('admin')->user()->can('audit-logs.delete')) {
                return response()->json([
                    'success' => false,
                    'message' => 'ليس لديك صلاحية حذف السجلات'
                ], 403);
            }

            // إنشاء نطاق التاريخ والوقت
            $fromDate = $request->from_date;
            $toDate = $request->to_date;
            $timeOption = $request->time_option;

            if ($timeOption === 'custom_time') {
                $fromDateTime = $fromDate . ' ' . $request->from_time . ':00';
                $toDateTime = $toDate . ' ' . $request->to_time . ':59';
            } else {
                $fromDateTime = $fromDate . ' 00:00:00';
                $toDateTime = $toDate . ' 23:59:59';
            }

            // التحقق من أن التاريخ النهائي ليس في المستقبل
            if (strtotime($toDateTime) > time()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف السجلات من تاريخ في المستقبل'
                ], 422);
            }

            // عد السجلات قبل الحذف
            $recordsCount = AuditLog::whereBetween('created_at', [$fromDateTime, $toDateTime])->count();

            if ($recordsCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد سجلات في النطاق الزمني المحدد'
                ], 404);
            }

            // التحقق من عدم حذف سجلات حساسة (اختياري)
            $sensitiveCount = AuditLog::whereBetween('created_at', [$fromDateTime, $toDateTime])
                ->where('is_sensitive', true)
                ->count();

            if ($sensitiveCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "يحتوي النطاق على {$sensitiveCount} سجل حساس. لا يمكن حذف السجلات الحساسة"
                ], 422);
            }

            // حذف السجلات
            $deletedCount = AuditLog::whereBetween('created_at', [$fromDateTime, $toDateTime])->delete();

            // إنشاء سجل تدقيق للحذف المتعدد بالتاريخ
            AuditLog::create([
                'user_id' => auth('admin')->id(),
                'user_type' => 'admin',
                'action' => 'bulk_delete_by_date',
                'target_type' => 'audit_logs',
                'target_id' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'severity' => 'high',
                'status' => 'success',
                'http_method' => $request->method(),
                'category' => 'data_management',
                'description' => "حذف متعدد للسجلات بالتاريخ: {$deletedCount} سجل",
                'old_values' => null,
                'new_values' => null,
                'request_data' => json_encode([
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'time_option' => $timeOption,
                    'from_time' => $request->from_time,
                    'to_time' => $request->to_time,
                    'deleted_count' => $deletedCount,
                ]),
                'metadata' => json_encode([
                    'from_datetime' => $fromDateTime,
                    'to_datetime' => $toDateTime,
                    'records_found' => $recordsCount,
                    'records_deleted' => $deletedCount,
                    'execution_time' => microtime(true) - LARAVEL_START,
                ]),
                'is_sensitive' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => "تم حذف {$deletedCount} سجل بنجاح من الفترة المحددة",
                'deleted_count' => $deletedCount,
                'date_range' => [
                    'from' => $fromDateTime,
                    'to' => $toDateTime
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('خطأ في حذف السجلات بالتاريخ: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف السجلات: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function validateData(Request $request, bool $updating = false): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'user_type' => ['nullable', 'string', 'max:100'],
            'user_name' => ['nullable', 'string', 'max:255'],
            'user_email' => ['nullable', 'email', 'max:255'],
            'action' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'model_type' => ['nullable', 'string', 'max:255'],
            'model_id' => ['nullable', 'integer'],
            'model_name' => ['nullable', 'string', 'max:255'],
            'old_values' => ['nullable'], // JSON text
            'new_values' => ['nullable'], // JSON text
            'ip_address' => ['nullable', 'string', 'max:45'],
            'user_agent' => ['nullable', 'string', 'max:1024'],
            'method' => ['nullable', 'string', 'in:GET,POST,PUT,PATCH,DELETE'],
            'url' => ['nullable', 'string', 'max:2048'],
            'request_data' => ['nullable'], // JSON text
            'severity' => ['nullable', 'string', 'in:low,medium,high,critical'],
            'is_sensitive' => ['nullable', 'boolean'],
            'session_id' => ['nullable', 'string', 'max:255'],
            'device_info' => ['nullable', 'string', 'max:2000'],
            'status' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'metadata' => ['nullable'], // JSON text
            'performed_at' => ['nullable', 'date'],
        ]);
    }

    protected function decodeJsonField($value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        try {
            $decoded = json_decode((string) $value, true, 512, JSON_THROW_ON_ERROR);
            return is_array($decoded) ? $decoded : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}


