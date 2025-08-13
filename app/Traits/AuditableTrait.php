<?php

namespace App\Traits;

use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;

trait AuditableTrait
{
    /**
     * Boot the auditable trait
     */
    protected static function bootAuditableTrait()
    {
        // Log model creation
        static::created(function (Model $model) {
            $model->auditCreated();
        });

        // Log model updates
        static::updated(function (Model $model) {
            $model->auditUpdated();
        });

        // Log model deletion
        static::deleted(function (Model $model) {
            $model->auditDeleted();
        });
    }

    /**
     * Log model creation
     */
    protected function auditCreated(): void
    {
        if ($this->shouldAudit('created')) {
            $this->getAuditService()->logModelChanges($this, 'created');
        }
    }

    /**
     * Log model updates
     */
    protected function auditUpdated(): void
    {
        if ($this->shouldAudit('updated') && $this->wasChanged()) {
            $oldValues = [];
            $newValues = $this->getChanges();
            
            // Get old values for changed attributes
            foreach (array_keys($newValues) as $key) {
                $oldValues[$key] = $this->getOriginal($key);
            }

            $this->getAuditService()->logModelChanges($this, 'updated', $oldValues);
        }
    }

    /**
     * Log model deletion
     */
    protected function auditDeleted(): void
    {
        if ($this->shouldAudit('deleted')) {
            $this->getAuditService()->logModelChanges($this, 'deleted', $this->getAttributes());
        }
    }

    /**
     * Determine if the model should be audited for the given action
     */
    protected function shouldAudit(string $action): bool
    {
        // Skip auditing if the model has the property set
        if (property_exists($this, 'disableAuditing') && $this->disableAuditing) {
            return false;
        }

        // Check if specific actions should be excluded
        if (property_exists($this, 'auditExclude') && in_array($action, $this->auditExclude)) {
            return false;
        }

        // Skip auditing for pivot models
        if (method_exists($this, 'getPivotClass')) {
            return false;
        }

        return true;
    }

    /**
     * Get the audit service instance
     */
    protected function getAuditService(): AuditService
    {
        return app(AuditService::class);
    }

    /**
     * Get attributes that should be excluded from auditing
     */
    protected function getAuditExcluded(): array
    {
        $defaultExcluded = [
            'created_at',
            'updated_at',
            'deleted_at',
            'remember_token',
            'password',
            'email_verified_at',
        ];

        if (property_exists($this, 'auditExcludedAttributes')) {
            return array_merge($defaultExcluded, $this->auditExcludedAttributes);
        }

        return $defaultExcluded;
    }

    /**
     * Get the model name for auditing
     */
    public function getAuditModelName(): string
    {
        return $this->name ?? 
               $this->title ?? 
               $this->email ?? 
               $this->username ?? 
               class_basename($this) . ' #' . $this->getKey();
    }

    /**
     * Manually log an action for this model
     */
    public function auditAction(string $action, ?string $description = null, array $metadata = []): void
    {
        $this->getAuditService()->log(
            action: $action,
            description: $description,
            model: $this,
            metadata: $metadata,
            category: 'model',
            isSensitive: $this->isAuditSensitive()
        );
    }

    /**
     * Determine if this model contains sensitive data
     */
    protected function isAuditSensitive(): bool
    {
        $sensitiveModels = [
            'User', 'Admin', 'PersonalAccessToken', 'Device', 'AuditLog'
        ];

        return in_array(class_basename($this), $sensitiveModels);
    }

    /**
     * Get changes formatted for auditing
     */
    protected function getAuditChanges(): array
    {
        if (!$this->wasChanged()) {
            return [];
        }

        $changes = [];
        $excluded = $this->getAuditExcluded();

        foreach ($this->getChanges() as $key => $newValue) {
            if (in_array($key, $excluded)) {
                continue;
            }

            $oldValue = $this->getOriginal($key);
            
            // Don't log if values are the same
            if ($oldValue === $newValue) {
                continue;
            }

            $changes[$key] = [
                'old' => $oldValue,
                'new' => $newValue,
            ];
        }

        return $changes;
    }

    /**
     * Get the audit logs for this model
     */
    public function auditLogs()
    {
        return $this->morphMany(\App\Models\AuditLog::class, 'model', 'model_type', 'model_id');
    }

    /**
     * Get recent audit logs for this model
     */
    public function recentAuditLogs(int $limit = 10)
    {
        return $this->auditLogs()
                    ->orderBy('performed_at', 'desc')
                    ->limit($limit)
                    ->get();
    }
}
