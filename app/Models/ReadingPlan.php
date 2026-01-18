<?php

namespace App\Models;

use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReadingPlan extends Model
{
    use HasFactory, AuditableTrait;

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'last_reading_date' => 'date',
        'settings' => 'array',
    ];

    /**
     * Plan type constants
     */
    const TYPE_SEQUENTIAL = 'sequential';
    const TYPE_CUSTOM = 'custom';

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSED = 'paused';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ABANDONED = 'abandoned';

    /**
     * Reading mode constants
     */
    const MODE_HADR = 'hadr';         // Quick reading
    const MODE_TADABBUR = 'tadabbur'; // Contemplation/reflection

    /**
     * Total pages in the Quran
     */
    const TOTAL_QURAN_PAGES = 604;

    /**
     * Default settings
     */
    const DEFAULT_SETTINGS = [
        'theme' => 'classic',        // classic, night, soft_blue, mint
        'font_size' => 24,
        'reciter_id' => null,
        'auto_scroll' => false,
        'show_translation' => false,
        'haptic_feedback' => true,
        'view_mode' => 'page',       // page (continuous) or verse (separate cards)
        'script_type' => 'uthmani',  // uthmani or imlaei
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function progressRecords(): HasMany
    {
        return $this->hasMany(ReadingProgress::class);
    }

    /**
     * Get today's progress record
     */
    public function todayProgress()
    {
        return $this->progressRecords()
            ->where('date', today())
            ->first();
    }

    /**
     * Check if plan is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if plan is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Get total pages to read in the plan
     */
    public function getTotalPages(): int
    {
        return $this->end_page - $this->start_page + 1;
    }

    /**
     * Get pages already read
     */
    public function getPagesRead(): int
    {
        return $this->current_page - $this->start_page;
    }

    /**
     * Get remaining pages
     */
    public function getRemainingPages(): int
    {
        return $this->end_page - $this->current_page + 1;
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentage(): float
    {
        $total = $this->getTotalPages();
        if ($total <= 0) return 0;

        $read = $this->getPagesRead();
        return round(($read / $total) * 100, 2);
    }

    /**
     * Get daily wird (pages to read today)
     */
    public function getDailyWird(): array
    {
        $startPage = $this->current_page;
        $endPage = min($startPage + $this->pages_per_day - 1, $this->end_page);

        return [
            'start_page' => $startPage,
            'end_page' => $endPage,
            'pages_count' => $endPage - $startPage + 1,
            'is_last_session' => $endPage >= $this->end_page,
        ];
    }

    /**
     * Get days remaining to complete
     */
    public function getDaysRemaining(): int
    {
        if ($this->pages_per_day <= 0) return 0;
        return (int) ceil($this->getRemainingPages() / $this->pages_per_day);
    }

    /**
     * Calculate estimated completion date
     */
    public function getEstimatedCompletionDate(): ?\Carbon\Carbon
    {
        $daysRemaining = $this->getDaysRemaining();
        return now()->addDays($daysRemaining);
    }

    /**
     * Check if user read today
     */
    public function hasReadToday(): bool
    {
        return $this->last_reading_date && $this->last_reading_date->isToday();
    }

    /**
     * Check if streak is broken (missed yesterday)
     */
    public function isStreakBroken(): bool
    {
        if (!$this->last_reading_date) {
            return false;
        }

        return $this->last_reading_date->lt(today()->subDay());
    }

    /**
     * Get setting value with default fallback
     */
    public function getSetting(string $key, $default = null)
    {
        $settings = $this->settings ?? [];
        return $settings[$key] ?? self::DEFAULT_SETTINGS[$key] ?? $default;
    }

    /**
     * Update a setting
     */
    public function updateSetting(string $key, $value): void
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
        $this->save();
    }

    /**
     * Scope for active plans
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for user's plans
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
