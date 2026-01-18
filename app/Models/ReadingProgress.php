<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingProgress extends Model
{
    use HasFactory;

    protected $table = 'reading_progress';

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'daily_target_met' => 'boolean',
    ];

    /**
     * Reading mode constants
     */
    const MODE_HADR = 'hadr';
    const MODE_TADABBUR = 'tadabbur';

    public function readingPlan(): BelongsTo
    {
        return $this->belongsTo(ReadingPlan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get pages read count
     */
    public function getPagesCount(): int
    {
        return $this->end_page - $this->start_page + 1;
    }

    /**
     * Check if this was a tadabbur (contemplation) session
     */
    public function isTadabburSession(): bool
    {
        return $this->reading_mode === self::MODE_TADABBUR;
    }

    /**
     * Scope for specific date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope for date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope for user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for completed daily targets
     */
    public function scopeTargetMet($query)
    {
        return $query->where('daily_target_met', true);
    }
}
