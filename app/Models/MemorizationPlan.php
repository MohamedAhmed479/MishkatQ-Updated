<?php

namespace App\Models;

use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemorizationPlan extends Model
{
    use HasFactory, AuditableTrait;

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'recovery_started_at' => 'datetime',
    ];

    /**
     * Plan status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSED = 'paused';
    const STATUS_COMPLETED = 'completed';
    const STATUS_RECOVERY = 'recovery';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function planItems()
    {
        return $this->hasMany(PlanItem::class, "plan_id", "id");
    }

    /**
     * Check if plan is in recovery mode
     */
    public function isInRecoveryMode(): bool
    {
        return $this->status === self::STATUS_RECOVERY;
    }

    /**
     * Check if plan is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Get days in recovery mode
     */
    public function getDaysInRecovery(): int
    {
        if (!$this->recovery_started_at) {
            return 0;
        }

        return $this->recovery_started_at->diffInDays(now());
    }
}
