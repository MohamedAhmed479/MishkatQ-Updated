<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\UserVerifyEmailNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomResetPassword;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $guard_name = 'user';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
        ];
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
        return $this->profile->total_points;
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
        $code = (string) random_int(100000, 999999);

        // Cache the code for 60 minutes associated with this user
        \Illuminate\Support\Facades\Cache::put(
            "email_verification_code_user_{$this->id}",
            $code,
            now()->addMinutes(60)
        );

        $this->notify(new UserVerifyEmailNotification($code));
    }


    public function sendPasswordResetNotification($token)
    {
        $code = (string) random_int(100000, 999999);

        \Illuminate\Support\Facades\Cache::put(
            "password_reset_code_{$this->email}",
            $code,
            now()->addMinutes(config('auth.passwords.users.expire'))
        );

        \Illuminate\Support\Facades\Cache::put(
            "password_reset_token_{$this->email}",
            $token,
            now()->addMinutes(config('auth.passwords.users.expire'))
        );

        $this->notify(new CustomResetPassword($code));
    }
}
