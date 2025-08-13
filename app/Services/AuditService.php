<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AuditService
{
    /**
     * Log an audit entry
     *
     * @param string $action
     * @param string|null $description
     * @param Model|null $model
     * @param array|null $oldValues
     * @param array|null $newValues
     * @param string $severity
     * @param string $category
     * @param bool $isSensitive
     * @param array|null $metadata
     * @param string $status
     * @return AuditLog
     */
    public function log(
        string $action,
        ?string $description = null,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        string $severity = 'medium',
        string $category = 'data',
        bool $isSensitive = false,
        ?array $metadata = null,
        string $status = 'success'
    ): AuditLog {
        $request = request();
        $user = Auth::user();

        return AuditLog::create([
            'user_id' => $user?->id,
            'user_type' => $user ? get_class($user) : null,
            'user_name' => $user?->name,
            'user_email' => $user?->email,
            'action' => $action,
            'description' => $description ?? $this->generateDescription($action, $model),
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id ?? $model?->getKey(),
            'model_name' => $this->getModelName($model),
            'old_values' => $this->sanitizeValues($oldValues),
            'new_values' => $this->sanitizeValues($newValues),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'request_data' => $this->sanitizeRequestData($request),
            'severity' => $severity,
            'is_sensitive' => $isSensitive,
            'session_id' => $request->session()?->getId(),
            'device_info' => $this->getDeviceInfo($request),
            'status' => $status,
            'category' => $category,
            'metadata' => $metadata,
            'performed_at' => Carbon::now(),
        ]);
    }

    /**
     * Log authentication events
     */
    public function logAuth(string $action, ?Model $user = null, string $status = 'success', ?string $description = null): AuditLog
    {
        return $this->log(
            action: $action,
            description: $description,
            model: $user,
            severity: $status === 'failed' ? 'high' : 'medium',
            category: 'auth',
            isSensitive: true,
            status: $status
        );
    }

    /**
     * Log data operations (CRUD)
     */
    public function logDataOperation(
        string $action,
        Model $model,
        ?array $oldValues = null,
        ?array $newValues = null,
        string $status = 'success'
    ): AuditLog {
        return $this->log(
            action: $action,
            model: $model,
            oldValues: $oldValues,
            newValues: $newValues,
            severity: $action === 'deleted' ? 'high' : 'medium',
            category: 'data',
            isSensitive: $this->isModelSensitive($model),
            status: $status
        );
    }

    /**
     * Log security events
     */
    public function logSecurity(string $action, string $description, string $severity = 'high'): AuditLog
    {
        return $this->log(
            action: $action,
            description: $description,
            severity: $severity,
            category: 'security',
            isSensitive: true
        );
    }

    /**
     * Log admin operations
     */
    public function logAdmin(string $action, ?Model $model = null, ?string $description = null): AuditLog
    {
        return $this->log(
            action: $action,
            description: $description,
            model: $model,
            severity: 'high',
            category: 'admin',
            isSensitive: true
        );
    }

    /**
     * Log API requests automatically
     */
    public function logApiRequest(Request $request, $response = null, string $status = 'success'): AuditLog
    {
        $action = $this->getActionFromRequest($request);
        
        return $this->log(
            action: $action,
            description: "API Request: {$request->method()} {$request->path()}",
            severity: $status === 'failed' ? 'medium' : 'low',
            category: 'api',
            metadata: [
                'response_status' => $response?->getStatusCode(),
                'execution_time' => microtime(true) - LARAVEL_START,
            ],
            status: $status
        );
    }

    /**
     * Generate description based on action and model
     */
    private function generateDescription(string $action, ?Model $model): string
    {
        if (!$model) {
            return ucfirst($action) . ' action performed';
        }

        $modelName = class_basename($model);
        $identifier = $model->name ?? $model->title ?? $model->email ?? $model->getKey();

        return ucfirst($action) . " {$modelName}: {$identifier}";
    }

    /**
     * Get model display name
     */
    private function getModelName(?Model $model): ?string
    {
        if (!$model) return null;

        return $model->name ?? 
               $model->title ?? 
               $model->email ?? 
               $model->username ?? 
               class_basename($model) . ' #' . $model->getKey();
    }

    /**
     * Sanitize values to remove sensitive data
     */
    private function sanitizeValues(?array $values): ?array
    {
        if (!$values) return null;

        $sensitiveFields = [
            'password', 'password_confirmation', 'token', 'secret', 
            'api_key', 'private_key', 'credit_card', 'ssn'
        ];

        foreach ($sensitiveFields as $field) {
            if (isset($values[$field])) {
                $values[$field] = '[REDACTED]';
            }
        }

        return $values;
    }

    /**
     * Sanitize request data
     */
    private function sanitizeRequestData(Request $request): array
    {
        $data = $request->except(['password', 'password_confirmation', 'token']);
        
        // Remove file data to avoid large payloads
        foreach ($data as $key => $value) {
            if ($request->hasFile($key)) {
                $data[$key] = '[FILE_UPLOAD]';
            }
        }

        return $data;
    }

    /**
     * Get device information from request
     */
    private function getDeviceInfo(Request $request): ?string
    {
        $userAgent = $request->userAgent();
        if (!$userAgent) return null;

        // Simple device detection
        if (str_contains($userAgent, 'Mobile')) {
            return 'Mobile Device';
        } elseif (str_contains($userAgent, 'Tablet')) {
            return 'Tablet Device';
        } else {
            return 'Desktop Device';
        }
    }

    /**
     * Determine if model contains sensitive data
     */
    private function isModelSensitive(Model $model): bool
    {
        $sensitiveModels = [
            'User', 'Admin', 'PersonalAccessToken', 'Device'
        ];

        return in_array(class_basename($model), $sensitiveModels);
    }

    /**
     * Get action from HTTP request
     */
    private function getActionFromRequest(Request $request): string
    {
        $method = $request->method();
        $route = $request->route();

        if ($route) {
            $action = $route->getAction('uses');
            if (is_string($action) && str_contains($action, '@')) {
                [, $methodName] = explode('@', $action);
                return $methodName;
            }
        }

        return match($method) {
            'GET' => 'viewed',
            'POST' => 'created',
            'PUT', 'PATCH' => 'updated',
            'DELETE' => 'deleted',
            default => 'accessed'
        };
    }

    /**
     * Log model changes automatically
     */
    public function logModelChanges(Model $model, string $action, ?array $oldValues = null): AuditLog
    {
        $newValues = null;
        
        if ($action === 'updated' && $model->wasChanged()) {
            $newValues = $model->getChanges();
            $oldValues = $oldValues ?? array_intersect_key($model->getOriginal(), $newValues);
        } elseif ($action === 'created') {
            $newValues = $model->getAttributes();
        }

        return $this->logDataOperation($action, $model, $oldValues, $newValues);
    }

    /**
     * Bulk log multiple operations
     */
    public function bulkLog(array $operations): void
    {
        $logs = [];
        $timestamp = Carbon::now();

        foreach ($operations as $operation) {
            $logs[] = array_merge($operation, [
                'performed_at' => $timestamp,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        AuditLog::insert($logs);
    }

    /**
     * Get audit statistics
     */
    public function getStats(int $days = 30): array
    {
        $startDate = Carbon::now()->subDays($days);

        return [
            'total_operations' => AuditLog::where('performed_at', '>=', $startDate)->count(),
            'failed_operations' => AuditLog::where('performed_at', '>=', $startDate)->where('status', 'failed')->count(),
            'sensitive_operations' => AuditLog::where('performed_at', '>=', $startDate)->where('is_sensitive', true)->count(),
            'top_users' => AuditLog::where('performed_at', '>=', $startDate)
                ->selectRaw('user_name, COUNT(*) as operation_count')
                ->groupBy('user_name')
                ->orderByDesc('operation_count')
                ->limit(10)
                ->get(),
            'operations_by_category' => AuditLog::where('performed_at', '>=', $startDate)
                ->selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->get(),
        ];
    }
}
