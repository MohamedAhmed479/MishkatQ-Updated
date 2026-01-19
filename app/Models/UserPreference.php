<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'require_test_before_completion' => 'boolean',
        'minimum_test_score' => 'integer',
    ];

    /**
     * Default values for new preferences
     */
    protected $attributes = [
        'require_test_before_completion' => true,
        'minimum_test_score' => 70,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tafsir()
    {
        return $this->belongsTo(Tafsir::class, 'tafsir_id');
    }

    /**
     * Check if user requires tests before marking completion
     */
    public function requiresTestBeforeCompletion(): bool
    {
        return $this->require_test_before_completion ?? true;
    }

    /**
     * Get the minimum score required to pass tests
     */
    public function getMinimumTestScore(): int
    {
        return $this->minimum_test_score ?? 70;
    }
}
