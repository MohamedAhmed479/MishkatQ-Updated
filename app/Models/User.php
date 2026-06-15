<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\UserVerifyEmailNotification;
use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, AuditableTrait;

    protected $guard_name = 'user';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'gender',
        'password',
        'provider',
        'provider_id',
        'provider_token',
        'can_use_smart_recitation',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'provider_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'provider_token' => 'encrypted',
            'can_use_smart_recitation' => 'boolean',
        ];
    }

    /**
     * Check if user can use smart recitation feature
     */
    public function canUseSmartRecitation(): bool
    {
        return $this->can_use_smart_recitation ?? false;
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'user_id');
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function preference(): HasOne
    {
        return $this->hasOne(UserPreference::class, 'user_id');
    }

    public function memorizationPlans()
    {
        return $this->hasMany(MemorizationPlan::class);
    }

    public function readingPlans(): HasMany
    {
        return $this->hasMany(ReadingPlan::class);
    }

    public function activeReadingPlan()
    {
        return $this->readingPlans()
            ->where('status', ReadingPlan::STATUS_ACTIVE)
            ->first();
    }

    public function readingProgress(): HasMany
    {
        return $this->hasMany(ReadingProgress::class);
    }

    public function memorizationProgress()
    {
        return $this->hasMany(MemorizationProgress::class);
    }

    public function getTotalMemorizedVerses(): int
    {
        return $this->memorizationProgress()->sum('verses_memorized');
    }

    public function getMemorizationProgress(): array
    {
        $progress = $this->memorizationProgress()
            ->with('chapter')
            ->get()
            ->map(function ($item) {
                return [
                    'chapter_id' => $item->chapter_id,
                    'chapter_name' => $item->chapter->name_ar,
                    'verses_memorized' => $item->verses_memorized,
                    'total_verses' => $item->total_verses,
                    'progress_percentage' => number_format($item->getProgressPercentage(), 2, '.', ''),
                    'status' => $item->status,
                    'last_reviewed_at' => $item->last_reviewed_at,
                ];
            });

        return [
            'total_verses_memorized' => $this->getTotalMemorizedVerses(),
            'chapters_progress' => $progress,
        ];
    }

    public function isActive(): bool
    {
        if (!$this->last_active_at) {
            return false;
        }
        return $this->last_active_at->diffInMinutes(now()) < 5;
    }

    public function activePlan()
    {
        return $this->memorizationPlans()
            ->where('status', 'active')
            ->first();
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('awarded_at')
            ->withTimestamps();
    }

    public function pointsTransactions()
    {
        return $this->hasMany(PointsTransaction::class);
    }

    public function leaderboards()
    {
        return $this->hasMany(Leaderboard::class);
    }

    public function getTotalPoints(): int
    {
        // Try to get from profile first
        if ($this->profile && $this->profile->total_points) {
            return (int) $this->profile->total_points;
        }

        // Fallback: calculate from points transactions
        return (int) $this->pointsTransactions()->sum('points');
    }

    public function getCurrentRank(string $periodType = 'monthly'): ?int
    {
        $now = now();
        $start = match ($periodType) {
            'daily' => (clone $now)->startOfDay(),
            'weekly' => (clone $now)->startOfWeek(),
            'monthly' => (clone $now)->startOfMonth(),
            'yearly' => (clone $now)->startOfYear(),
            default => (clone $now)->startOfMonth(),
        };
        $end = match ($periodType) {
            'daily' => (clone $now)->endOfDay(),
            'weekly' => (clone $now)->endOfWeek(),
            'monthly' => (clone $now)->endOfMonth(),
            'yearly' => (clone $now)->endOfYear(),
            default => (clone $now)->endOfMonth(),
        };

        return $this->leaderboards()
            ->where('period_type', $periodType)
            ->where('period_start', $start)
            ->where('period_end', $end)
            ->value('rank');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new UserVerifyEmailNotification());
    }


    public function sendPasswordResetNotification($token)
    {
        $code = (string) random_int(100000, 999999);

        \Illuminate\Support\Facades\Cache::put(
            "password_reset_code_{$this->email}",
            $code,
            now()->addMinutes(config('auth.passwords.users.expire'))
        );

        $this->notify(new CustomResetPassword($code));
    }

    /**
     * Update the global streak for all user activities (memorization, revision, reading).
     * This method should be called whenever the user performs any tracked activity.
     *
     * @return array Returns the updated streak information
     */
    public function updateGlobalStreak(): array
    {
        $profile = $this->profile;

        if (!$profile) {
            return [
                'current_streak' => 0,
                'best_streak' => 0,
                'streak_increased' => false,
            ];
        }

        $today = now()->startOfDay();
        $lastActivityDate = $profile->last_activity_date;

        $streakIncreased = false;
        $currentStreak = $profile->current_streak ?? 0;
        $bestStreak = $profile->best_streak ?? 0;

        if ($lastActivityDate) {
            $lastActivity = \Carbon\Carbon::parse($lastActivityDate)->startOfDay();
            $daysDiff = $today->diffInDays($lastActivity);

            if ($daysDiff === 0) {
                // Already active today, no change needed
            } elseif ($daysDiff === 1) {
                // Consecutive day - increase streak
                $currentStreak++;
                $streakIncreased = true;
            } else {
                // Streak broken - reset to 1
                $currentStreak = 1;
                $streakIncreased = true;
            }
        } else {
            // First activity ever
            $currentStreak = 1;
            $streakIncreased = true;
        }

        // Update best streak if current exceeds it
        if ($currentStreak > $bestStreak) {
            $bestStreak = $currentStreak;
        }

        // Update profile
        $profile->update([
            'current_streak' => $currentStreak,
            'best_streak' => $bestStreak,
            'last_activity_date' => $today,
        ]);

        return [
            'current_streak' => $currentStreak,
            'best_streak' => $bestStreak,
            'streak_increased' => $streakIncreased,
        ];
    }

    /**
     * Get current streak info without updating
     */
    public function getStreakInfo(): array
    {
        if (!$this->profile) {
            return [
                'current_streak' => 0,
                'best_streak' => 0,
                'is_active_today' => false,
            ];
        }

        return $this->profile->getStreakInfo();
    }
}
