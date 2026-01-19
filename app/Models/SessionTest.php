<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionTest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'score' => 'decimal:2',
        'passed' => 'boolean',
        'details' => 'array',
    ];

    /**
     * Test type constants
     */
    const TYPE_RECITATION = 'recitation';
    const TYPE_GAP_FILLING = 'gap_filling';
    const TYPE_VERSE_ORDERING = 'verse_ordering';
    const TYPE_VERSE_BEGINNING = 'verse_beginning';

    /**
     * Default passing score threshold
     */
    const DEFAULT_PASSING_SCORE = 70;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function planItem(): BelongsTo
    {
        return $this->belongsTo(PlanItem::class);
    }

    /**
     * Check if this test result indicates a passing score
     */
    public function hasPassed(float $threshold = null): bool
    {
        $threshold = $threshold ?? self::DEFAULT_PASSING_SCORE;
        return $this->score >= $threshold;
    }

    /**
     * Get the next attempt number for a user's test on a plan item
     */
    public static function getNextAttemptNumber(int $userId, int $planItemId, string $testType): int
    {
        $lastAttempt = self::where('user_id', $userId)
            ->where('plan_item_id', $planItemId)
            ->where('test_type', $testType)
            ->max('attempt_number');

        return ($lastAttempt ?? 0) + 1;
    }

    /**
     * Check if user has passed all required tests for a plan item
     */
    public static function hasPassedAllTests(int $userId, int $planItemId, float $threshold = null): bool
    {
        $threshold = $threshold ?? self::DEFAULT_PASSING_SCORE;

        // Get the latest test result for each test type
        $testTypes = [self::TYPE_GAP_FILLING, self::TYPE_VERSE_ORDERING];

        foreach ($testTypes as $testType) {
            $latestTest = self::where('user_id', $userId)
                ->where('plan_item_id', $planItemId)
                ->where('test_type', $testType)
                ->orderBy('created_at', 'desc')
                ->first();

            // If no test exists or test didn't pass, return false
            if (!$latestTest || !$latestTest->hasPassed($threshold)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get test statistics for a plan item
     */
    public static function getTestStats(int $userId, int $planItemId): array
    {
        $tests = self::where('user_id', $userId)
            ->where('plan_item_id', $planItemId)
            ->get();

        $stats = [
            'total_attempts' => $tests->count(),
            'passed_count' => $tests->where('passed', true)->count(),
            'average_score' => $tests->avg('score') ?? 0,
            'best_score' => $tests->max('score') ?? 0,
            'by_type' => [],
        ];

        foreach ([self::TYPE_RECITATION, self::TYPE_GAP_FILLING, self::TYPE_VERSE_ORDERING, self::TYPE_VERSE_BEGINNING] as $type) {
            $typeTests = $tests->where('test_type', $type);
            $stats['by_type'][$type] = [
                'attempts' => $typeTests->count(),
                'passed' => $typeTests->where('passed', true)->isNotEmpty(),
                'best_score' => $typeTests->max('score') ?? 0,
            ];
        }

        return $stats;
    }
}
