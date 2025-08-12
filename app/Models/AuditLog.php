<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_type',
        'user_name',
        'user_email',
        'action',
        'description',
        'model_type',
        'model_id',
        'model_name',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'method',
        'url',
        'request_data',
        'severity',
        'is_sensitive',
        'session_id',
        'device_info',
        'status',
        'category',
        'metadata',
        'performed_at'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'request_data' => 'array',
        'metadata' => 'array',
        'is_sensitive' => 'boolean',
        'performed_at' => 'datetime',
    ];

    /**
     * Relationship to the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by action
     */
    public function scopeByAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by model type
     */
    public function scopeByModelType(Builder $query, string $modelType): Builder
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope to filter by severity
     */
    public function scopeBySeverity(Builder $query, string $severity): Builder
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->whereBetween('performed_at', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by IP address
     */
    public function scopeByIpAddress(Builder $query, string $ipAddress): Builder
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Scope to get sensitive operations
     */
    public function scopeSensitive(Builder $query): Builder
    {
        return $query->where('is_sensitive', true);
    }

    /**
     * Scope to get failed operations
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get recent logs
     */
    public function scopeRecent(Builder $query, int $hours = 24): Builder
    {
        return $query->where('performed_at', '>=', Carbon::now()->subHours($hours));
    }

    /**
     * Get formatted changes for display
     */
    public function getFormattedChangesAttribute(): array
    {
        $changes = [];

        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $field => $newValue) {
                $oldValue = $this->old_values[$field] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[$field] = [
                        'old' => $oldValue,
                        'new' => $newValue
                    ];
                }
            }
        }

        return $changes;
    }

    /**
     * Get risk level based on action and severity
     */
    public function getRiskLevelAttribute(): string
    {
        $criticalActions = ['deleted', 'role_changed', 'permission_changed', 'failed_login'];

        if (in_array($this->action, $criticalActions) || $this->severity === 'critical') {
            return 'high';
        }

        if ($this->severity === 'high' || $this->is_sensitive) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Get display name for the action
     */
    public function getActionDisplayNameAttribute(): string
    {
        $actionNames = [
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'login' => 'Logged In',
            'logout' => 'Logged Out',
            'failed_login' => 'Failed Login',
            'role_assigned' => 'Role Assigned',
            'role_removed' => 'Role Removed',
            'permission_granted' => 'Permission Granted',
            'permission_revoked' => 'Permission Revoked',
            'password_changed' => 'Password Changed',
            'email_verified' => 'Email Verified',
            'account_locked' => 'Account Locked',
            'account_unlocked' => 'Account Unlocked',
        ];

        return $actionNames[$this->action] ?? ucfirst(str_replace('_', ' ', $this->action));
    }
}
