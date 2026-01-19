<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'username',
        'profile_image',
        'verses_memorized_count',
        'total_points',
        'current_streak',
        'best_streak',
        'last_activity_date',
    ];

    protected $casts = [
        'last_activity_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get streak status info
     */
    public function getStreakInfo(): array
    {
        return [
            'current_streak' => $this->current_streak ?? 0,
            'best_streak' => $this->best_streak ?? 0,
            'last_activity_date' => $this->last_activity_date?->format('Y-m-d'),
            'is_active_today' => $this->last_activity_date?->isToday() ?? false,
        ];
    }
}
