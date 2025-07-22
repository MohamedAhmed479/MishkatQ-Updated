<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewRecord extends Model
{
    /**
     * الحقول القابلة للتعبئة الجماعية
     *
     * @var array
     */
    protected $fillable = [
        'spaced_repetition_id',
        'review_date',
        'performance_rating',
        'successful',
        'notes',
    ];

    /**
     * القواعد التي يجب تحويلها
     *
     * @var array
     */
    protected $casts = [
        'review_date' => 'datetime',
        'successful' => 'boolean',
    ];

    /**
     * علاقة مع جدول المراجعات المتباعدة
     */
    public function spacedRepetition(): BelongsTo
    {
        return $this->belongsTo(SpacedRepetition::class, 'spaced_repetition_id');
    }

    /**
     * الحصول على وصف أداء المراجعة
     */
    public function getPerformanceDescription(): string
    {
        $ratings = [
            0 => 'ضعيف جداً',
            1 => 'ضعيف',
            2 => 'متوسط',
            3 => 'جيد',
            4 => 'جيد جداً',
            5 => 'ممتاز'
        ];

        return $ratings[$this->performance_rating] ?? 'غير معروف';
    }
}
