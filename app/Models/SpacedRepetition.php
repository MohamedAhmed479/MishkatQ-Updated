<?php

namespace App\Models;

use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SpacedRepetition extends Model
{
    use HasFactory, AuditableTrait;

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
        'stability',         // FSRS: Days until retrievability drops to 90%
        'difficulty',        // FSRS: Item difficulty on 1-10 scale
        'is_recovery_item',  // Whether this item is prioritized for recovery mode
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
        'stability' => 'float',
        'difficulty' => 'float',
        'is_recovery_item' => 'boolean',
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

    /**
     * نطاق للعناصر الضعيفة (استقرار منخفض)
     */
    public function scopeWeak($query, float $stabilityThreshold = 7.0)
    {
        return $query->where('stability', '<', $stabilityThreshold);
    }

    /**
     * نطاق للعناصر المتقنة (استقرار عالي)
     */
    public function scopeMastered($query, float $stabilityThreshold = 90.0)
    {
        return $query->where('stability', '>=', $stabilityThreshold);
    }

    /**
     * نطاق للعناصر التي تحتاج مراجعة عاجلة
     */
    public function scopeNeedsUrgentReview($query)
    {
        return $query->whereDate('scheduled_date', '<=', now()->toDateString())
            ->whereNull('last_reviewed_at')
            ->orderBy('stability', 'asc'); // Lowest stability first
    }

    /**
     * الحصول على حالة الذاكرة بناءً على الاستقرار
     */
    public function getMemoryState(): string
    {
        $stability = $this->stability ?? 1.0;

        if ($stability < 21) {
            return 'young';
        } elseif ($stability < 90) {
            return 'mature';
        } else {
            return 'mastered';
        }
    }

    /**
     * الحصول على حالة الذاكرة بالعربية
     */
    public function getMemoryStateArabic(): string
    {
        return match ($this->getMemoryState()) {
            'young' => 'جديد',
            'mature' => 'مستقر',
            'mastered' => 'متقن',
            default => 'غير معروف',
        };
    }

    /**
     * حساب احتمالية التذكر الحالية (Retrievability)
     */
    public function calculateRetrievability(): float
    {
        $stability = $this->stability ?? 1.0;
        $daysElapsed = $this->last_reviewed_at 
            ? now()->diffInDays($this->last_reviewed_at) 
            : 0;

        if ($stability <= 0) {
            return 0;
        }

        // FSRS power forgetting curve: R = (1 + t / (9 * S))^(-1)
        $factor = 9 * $stability;
        $retrievability = pow(1 + $daysElapsed / $factor, -1);

        return max(0, min(1, $retrievability));
    }

    /**
     * تحديد ما إذا كان هذا العنصر "عالق" (leech)
     * A leech is an item that has been reviewed many times but still has low stability
     */
    public function isLeech(): bool
    {
        $totalReviews = $this->repetition_count;
        $stability = $this->stability ?? 1.0;

        if ($totalReviews < 3) {
            return false;
        }

        // Consider an item a leech if:
        // 1. It has many reviews but still low stability
        // 2. Or the last review was a failure with multiple previous attempts
        if ($stability < 7 && $totalReviews >= 3) {
            return true;
        }

        // Check if last review was failed (reviewRecord is HasOne)
        if ($this->relationLoaded('reviewRecord') && $this->reviewRecord) {
            return !$this->reviewRecord->successful && $totalReviews >= 3;
        }

        return false;
    }
}
