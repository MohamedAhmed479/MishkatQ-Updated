<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * FSRS (Free Spaced Repetition Scheduler) Service
 * 
 * A modern spaced repetition algorithm that predicts memory decay and schedules
 * reviews at optimal times based on cognitive science research.
 * 
 * Key concepts:
 * - Stability (S): Days for retrievability to drop to 90%
 * - Difficulty (D): How hard the item is for the user (1-10)
 * - Retrievability (R): Probability of recalling the item today
 */
class FSRSService
{
    /**
     * FSRS Algorithm Parameters (v4)
     * These are optimized defaults based on research
     */
    protected array $params = [
        'w' => [
            0.4,    // w0: Initial stability for "Again" (grade 1)
            0.6,    // w1: Initial stability for "Hard" (grade 2)
            2.4,    // w2: Initial stability for "Good" (grade 3)
            5.8,    // w3: Initial stability for "Easy" (grade 4-5)
            4.93,   // w4: Difficulty weight
            0.94,   // w5: Stability decay
            0.86,   // w6: Stability increase factor
            0.01,   // w7: Difficulty reversion factor
            1.49,   // w8: Stability power
            0.14,   // w9: Forgetting curve shape
            0.94,   // w10: Hard penalty
            2.18,   // w11: Easy bonus
            0.05,   // w12: Difficulty mean reversion
            0.34,   // w13: Stability increase dampening
            1.26,   // w14: Fail penalty
            0.29,   // w15: Hard interval modifier
            2.61,   // w16: Easy interval modifier
        ],
        'request_retention' => 0.9,  // Target retention rate (90%)
        'maximum_interval' => 365,   // Maximum interval in days
        'minimum_interval' => 1,     // Minimum interval in days
    ];

    /**
     * Grade mapping from performance rating (0-5) to FSRS grades
     */
    protected array $gradeMap = [
        0 => 1, // ضعيف جداً -> Again
        1 => 1, // ضعيف -> Again
        2 => 2, // متوسط -> Hard
        3 => 3, // جيد -> Good
        4 => 4, // جيد جداً -> Easy
        5 => 4, // ممتاز -> Easy
    ];

    /**
     * Memory state maturity thresholds (in days of stability)
     */
    const MATURITY_THRESHOLDS = [
        'young' => 21,      // Less than 21 days stability
        'mature' => 90,     // 21-90 days stability
        'mastered' => 365,  // More than 90 days stability
    ];

    /**
     * Calculate initial stability for a new item based on first review grade
     */
    public function calculateInitialStability(int $performanceRating): float
    {
        $grade = $this->gradeMap[$performanceRating] ?? 3;
        $w = $this->params['w'];

        // Initial stability is directly mapped from w0-w3 based on grade
        return match ($grade) {
            1 => $w[0],
            2 => $w[1],
            3 => $w[2],
            4 => $w[3],
            default => $w[2],
        };
    }

    /**
     * Calculate initial difficulty for a new item
     * D0 = w4 - (grade - 3) * w5
     */
    public function calculateInitialDifficulty(int $performanceRating): float
    {
        $grade = $this->gradeMap[$performanceRating] ?? 3;
        $w = $this->params['w'];

        // Initial difficulty calculation
        $d = $w[4] - ($grade - 3) * $w[5];

        // Clamp difficulty between 1 and 10
        return $this->clampDifficulty($d);
    }

    /**
     * Calculate retrievability (probability of recall) at a given time
     * R(t) = e^(-t/S * ln(0.9))  where t is days elapsed
     */
    public function calculateRetrievability(float $stability, int $daysElapsed): float
    {
        if ($stability <= 0) {
            return 0;
        }

        // R = (1 + t / (9 * S))^(-1)
        // This is the power forgetting curve used in FSRS
        $factor = 9 * $stability;
        $retrievability = pow(1 + $daysElapsed / $factor, -1);

        return max(0, min(1, $retrievability));
    }

    /**
     * Calculate new stability after a review
     */
    public function calculateNewStability(
        float $currentStability,
        float $difficulty,
        float $retrievability,
        int $performanceRating
    ): float {
        $grade = $this->gradeMap[$performanceRating] ?? 3;
        $w = $this->params['w'];

        if ($grade === 1) {
            // Failed review - stability decreases significantly
            $newStability = $w[11] * pow($difficulty, -$w[12]) * 
                           (pow($currentStability + 1, $w[13]) - 1) * 
                           exp((1 - $retrievability) * $w[14]);
        } else {
            // Successful review - stability increases
            $hardPenalty = ($grade === 2) ? $w[15] : 1;
            $easyBonus = ($grade === 4) ? $w[16] : 1;

            $newStability = $currentStability * (
                1 + exp($w[6]) *
                (11 - $difficulty) *
                pow($currentStability, -$w[7]) *
                (exp((1 - $retrievability) * $w[8]) - 1) *
                $hardPenalty *
                $easyBonus
            );
        }

        // Apply bounds
        $newStability = max($this->params['minimum_interval'], $newStability);
        $newStability = min($this->params['maximum_interval'], $newStability);

        return round($newStability, 2);
    }

    /**
     * Calculate new difficulty after a review
     * D' = w7 * D0(3) + (1 - w7) * (D - w6 * (grade - 3))
     */
    public function calculateNewDifficulty(
        float $currentDifficulty,
        int $performanceRating
    ): float {
        $grade = $this->gradeMap[$performanceRating] ?? 3;
        $w = $this->params['w'];

        // Mean reversion towards D0(3)
        $d0 = $w[4]; // Initial difficulty for grade 3
        $newDifficulty = $w[7] * $d0 + (1 - $w[7]) * ($currentDifficulty - $w[6] * ($grade - 3));

        return $this->clampDifficulty($newDifficulty);
    }

    /**
     * Calculate the optimal interval until next review
     * Based on desired retention rate
     */
    public function calculateNextInterval(float $stability): int
    {
        $requestRetention = $this->params['request_retention'];
        
        // Interval = S * (R^(-1) - 1) * 9
        // For R = 0.9: Interval ≈ S
        $interval = $stability * (pow($requestRetention, -1) - 1) * 9;
        
        // Apply bounds
        $interval = max($this->params['minimum_interval'], $interval);
        $interval = min($this->params['maximum_interval'], $interval);

        return (int) ceil($interval);
    }

    /**
     * Calculate the next scheduled date for a review
     */
    public function calculateNextScheduledDate(float $stability): Carbon
    {
        $interval = $this->calculateNextInterval($stability);
        return Carbon::now()->addDays($interval)->startOfDay();
    }

    /**
     * Get memory state category based on stability
     */
    public function getMemoryState(float $stability): string
    {
        if ($stability < self::MATURITY_THRESHOLDS['young']) {
            return 'young';
        } elseif ($stability < self::MATURITY_THRESHOLDS['mature']) {
            return 'mature';
        } else {
            return 'mastered';
        }
    }

    /**
     * Get memory state in Arabic
     */
    public function getMemoryStateArabic(float $stability): string
    {
        return match ($this->getMemoryState($stability)) {
            'young' => 'جديد',
            'mature' => 'مستقر',
            'mastered' => 'متقن',
            default => 'غير معروف',
        };
    }

    /**
     * Determine if an item is a "leech" (consistently failed)
     * A leech is an item that has been failed many times
     */
    public function isLeech(int $failCount, int $totalReviews): bool
    {
        if ($totalReviews < 3) {
            return false;
        }

        $failRate = $failCount / $totalReviews;
        return $failRate > 0.5 && $failCount >= 3;
    }

    /**
     * Calculate priority score for review scheduling
     * Higher score = more urgent review needed
     */
    public function calculatePriorityScore(float $retrievability, float $stability, int $daysOverdue = 0): float
    {
        // Base priority is inverse of retrievability
        $basePriority = (1 - $retrievability) * 100;

        // Add urgency for overdue items
        $overdueBonus = $daysOverdue * 5;

        // Items with low stability need more attention
        $stabilityFactor = $stability < 7 ? (7 - $stability) * 2 : 0;

        return $basePriority + $overdueBonus + $stabilityFactor;
    }

    /**
     * Generate a full FSRS state update after a review
     */
    public function processReview(
        ?float $currentStability,
        ?float $currentDifficulty,
        int $performanceRating,
        ?Carbon $lastReviewedAt = null
    ): array {
        $isFirstReview = is_null($currentStability) || is_null($currentDifficulty);

        if ($isFirstReview) {
            // First review - initialize state
            $newStability = $this->calculateInitialStability($performanceRating);
            $newDifficulty = $this->calculateInitialDifficulty($performanceRating);
            $retrievability = 1.0; // First review, assumed perfect recall opportunity
        } else {
            // Calculate days since last review
            $daysElapsed = $lastReviewedAt 
                ? Carbon::now()->diffInDays($lastReviewedAt) 
                : 0;

            // Calculate current retrievability
            $retrievability = $this->calculateRetrievability($currentStability, $daysElapsed);

            // Calculate new stability and difficulty
            $newStability = $this->calculateNewStability(
                $currentStability,
                $currentDifficulty,
                $retrievability,
                $performanceRating
            );

            $newDifficulty = $this->calculateNewDifficulty(
                $currentDifficulty,
                $performanceRating
            );
        }

        // Calculate next review interval and date
        $nextInterval = $this->calculateNextInterval($newStability);
        $nextScheduledDate = Carbon::now()->addDays($nextInterval)->startOfDay();

        return [
            'stability' => round($newStability, 2),
            'difficulty' => round($newDifficulty, 2),
            'retrievability' => round($retrievability, 4),
            'next_interval' => $nextInterval,
            'scheduled_date' => $nextScheduledDate,
            'memory_state' => $this->getMemoryState($newStability),
            'memory_state_ar' => $this->getMemoryStateArabic($newStability),
        ];
    }

    /**
     * Estimate when retrievability will drop to target threshold
     */
    public function estimateOptimalReviewDate(float $stability, Carbon $lastReviewedAt): Carbon
    {
        $interval = $this->calculateNextInterval($stability);
        return $lastReviewedAt->copy()->addDays($interval);
    }

    /**
     * Get items sorted by urgency (lowest retrievability first)
     */
    public function sortByUrgency(array $items): array
    {
        usort($items, function ($a, $b) {
            return $a['retrievability'] <=> $b['retrievability'];
        });

        return $items;
    }

    /**
     * Calculate bulk statistics for a set of items
     */
    public function calculateBulkStatistics(array $items): array
    {
        if (empty($items)) {
            return [
                'total_items' => 0,
                'young_count' => 0,
                'mature_count' => 0,
                'mastered_count' => 0,
                'average_stability' => 0,
                'average_retrievability' => 0,
                'leeches_count' => 0,
            ];
        }

        $youngCount = 0;
        $matureCount = 0;
        $masteredCount = 0;
        $totalStability = 0;
        $totalRetrievability = 0;
        $leechesCount = 0;

        foreach ($items as $item) {
            $stability = $item['stability'] ?? 0;
            $retrievability = $item['retrievability'] ?? 0;

            $totalStability += $stability;
            $totalRetrievability += $retrievability;

            $state = $this->getMemoryState($stability);
            match ($state) {
                'young' => $youngCount++,
                'mature' => $matureCount++,
                'mastered' => $masteredCount++,
            };

            if (isset($item['fail_count'], $item['total_reviews'])) {
                if ($this->isLeech($item['fail_count'], $item['total_reviews'])) {
                    $leechesCount++;
                }
            }
        }

        $count = count($items);

        return [
            'total_items' => $count,
            'young_count' => $youngCount,
            'mature_count' => $matureCount,
            'mastered_count' => $masteredCount,
            'average_stability' => round($totalStability / $count, 2),
            'average_retrievability' => round($totalRetrievability / $count, 4),
            'leeches_count' => $leechesCount,
        ];
    }

    /**
     * Clamp difficulty value between 1 and 10
     */
    protected function clampDifficulty(float $difficulty): float
    {
        return max(1, min(10, round($difficulty, 2)));
    }

    /**
     * Get the target retention rate
     */
    public function getTargetRetention(): float
    {
        return $this->params['request_retention'];
    }

    /**
     * Set custom retention rate (0.7 to 0.97)
     */
    public function setTargetRetention(float $retention): self
    {
        $this->params['request_retention'] = max(0.7, min(0.97, $retention));
        return $this;
    }
}
