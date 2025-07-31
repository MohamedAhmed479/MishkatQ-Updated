<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SpacedRepetition extends Model
{
    use HasFactory;

    /**
     * اسم الجدول
     *
     * @var string
     */
    protected $table = 'spaced_repetitions';

    /**
     * الحقول القابلة للتعبئة الجماعية
     *
     * @var array
     */
    protected $fillable = [
        'plan_item_id',
        'interval_index',
        'scheduled_date',
        'ease_factor',
        'repetition_count',
        'last_reviewed_at',
    ];

    /**
     * القواعد التي يجب تحويلها
     *
     * @var array
     */
    protected $casts = [
        'scheduled_date' => 'date',
        'last_reviewed_at' => 'datetime',
        'ease_factor' => 'float',
    ];


    /**
     * علاقة مع المقطع المرتبط
     */
    public function planItem(): BelongsTo
    {
        return $this->belongsTo(PlanItem::class, 'plan_item_id', "id");
    }

    /**
     * علاقة مع سجلات المراجعة
     */
    public function reviewRecord(): HasOne
    {
        return $this->hasOne(ReviewRecord::class);
    }


    /**
     * تحديد ما إذا كانت المراجعة متأخرة
     */
    public function isOverdue()
    {
        return $this->scheduled_date->isPast() && is_null($this->last_reviewed_at);
    }

    /**
     * تحديد ما إذا كانت المراجعة مكتملة
     */
    public function isCompleted()
    {
        return !is_null($this->last_reviewed_at);
    }

    /**
     * نطاق للمراجعات المجدولة لليوم
     */
    public function scopeScheduledForToday($query)
    {
        return $query->whereDate('scheduled_date', now()->toDateString())
            ->whereNull('last_reviewed_at');
    }

    /**
     * نطاق للمراجعات المتأخرة
     */
    public function scopeOverdue($query)
    {
        return $query->whereDate('scheduled_date', '<', now()->toDateString())
            ->whereNull('last_reviewed_at');
    }
}
