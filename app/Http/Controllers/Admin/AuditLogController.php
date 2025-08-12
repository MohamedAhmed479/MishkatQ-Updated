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
            new ControllerMiddleware('permission:audit-logs.delete', only: ['destroy']),
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


