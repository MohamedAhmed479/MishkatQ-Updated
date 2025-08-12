<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leaderboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_points',
        'rank',
        'period_start',
        'period_end',
        'period_type'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
