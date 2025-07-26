<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemorizationProgress extends Model
{
    protected $fillable = [
        'user_id',
        'chapter_id',
        'verses_memorized',
        'total_verses',
        'status',
        'last_reviewed_at',
    ];

    protected $casts = [
        'last_reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function getProgressPercentage(): float
    {
        return ($this->verses_memorized / $this->total_verses) * 100;
    }

    public function updateProgress(int $versesMemorized): void
    {
        $this->verses_memorized = $versesMemorized;

        if ($versesMemorized === 0) {
            $this->status = 'not_started';
        } elseif ($versesMemorized === $this->total_verses) {
            $this->status = 'completed';
        } else {
            $this->status = 'in_progress';
        }

        $this->last_reviewed_at = now();
        $this->save();
    }
}
