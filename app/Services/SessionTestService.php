<?php

namespace App\Services;

use App\Models\PlanItem;
use App\Models\SessionTest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SessionTestService
{
    /**
     * Store a test result for a plan item
     */
    public function storeTestResult(
        int $userId,
        int $planItemId,
        string $testType,
        float $score,
        array $details = []
    ): SessionTest {
        $user = User::findOrFail($userId);
        $minimumScore = $user->preference?->minimum_test_score ?? SessionTest::DEFAULT_PASSING_SCORE;

        $attemptNumber = SessionTest::getNextAttemptNumber($userId, $planItemId, $testType);

        return SessionTest::create([
            'user_id' => $userId,
            'plan_item_id' => $planItemId,
            'test_type' => $testType,
            'score' => $score,
            'passed' => $score >= $minimumScore,
            'attempt_number' => $attemptNumber,
            'duration_seconds' => $details['duration_seconds'] ?? null,
            'details' => $details,
        ]);
    }

    /**
     * Check if a user has passed all required tests for a plan item
     */
    public function hasPassedAllRequiredTests(int $userId, int $planItemId): bool
    {
        $user = User::findOrFail($userId);
        
        // If user doesn't require tests, return true
        if (!$user->preference?->requiresTestBeforeCompletion()) {
            return true;
        }

        $minimumScore = $user->preference->getMinimumTestScore();

        // Check required tests: gap_filling and verse_ordering
        $requiredTests = [SessionTest::TYPE_GAP_FILLING, SessionTest::TYPE_VERSE_ORDERING];

        foreach ($requiredTests as $testType) {
            $latestTest = SessionTest::where('user_id', $userId)
                ->where('plan_item_id', $planItemId)
                ->where('test_type', $testType)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$latestTest || !$latestTest->hasPassed($minimumScore)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get test statistics for a plan item
     */
    public function getTestStats(int $userId, int $planItemId): array
    {
        return SessionTest::getTestStats($userId, $planItemId);
    }

    /**
     * Get all test results for a plan item
     */
    public function getTestResults(int $userId, int $planItemId): array
    {
        $tests = SessionTest::where('user_id', $userId)
            ->where('plan_item_id', $planItemId)
            ->orderBy('created_at', 'desc')
            ->get();

        $results = [];
        foreach ([SessionTest::TYPE_RECITATION, SessionTest::TYPE_GAP_FILLING, SessionTest::TYPE_VERSE_ORDERING, SessionTest::TYPE_VERSE_BEGINNING] as $type) {
            $latestTest = $tests->where('test_type', $type)->first();
            $results[$type] = $latestTest ? [
                'id' => $latestTest->id,
                'score' => $latestTest->score,
                'passed' => $latestTest->passed,
                'attempt_number' => $latestTest->attempt_number,
                'created_at' => $latestTest->created_at->toISOString(),
            ] : null;
        }

        return $results;
    }

    /**
     * Store multiple test results at once (for batch submission)
     */
    public function storeMultipleTestResults(int $userId, int $planItemId, array $testResults): array
    {
        $storedResults = [];

        DB::transaction(function () use ($userId, $planItemId, $testResults, &$storedResults) {
            foreach ($testResults as $result) {
                if (!isset($result['test_type']) || !isset($result['score'])) {
                    continue;
                }

                $storedResults[] = $this->storeTestResult(
                    $userId,
                    $planItemId,
                    $result['test_type'],
                    $result['score'],
                    $result['details'] ?? []
                );
            }
        });

        return $storedResults;
    }

    /**
     * Check if user can mark a plan item as completed
     */
    public function canMarkAsCompleted(int $userId, int $planItemId): array
    {
        $user = User::findOrFail($userId);
        $planItem = PlanItem::findOrFail($planItemId);

        // Check if tests are required
        $requireTests = $user->preference?->requiresTestBeforeCompletion() ?? true;

        if (!$requireTests) {
            return [
                'can_complete' => true,
                'message' => 'الاختبارات غير مطلوبة',
                'missing_tests' => [],
            ];
        }

        $minimumScore = $user->preference?->getMinimumTestScore() ?? 70;
        $requiredTests = [SessionTest::TYPE_GAP_FILLING, SessionTest::TYPE_VERSE_ORDERING];
        $missingTests = [];

        foreach ($requiredTests as $testType) {
            $latestTest = SessionTest::where('user_id', $userId)
                ->where('plan_item_id', $planItemId)
                ->where('test_type', $testType)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$latestTest) {
                $missingTests[] = [
                    'type' => $testType,
                    'reason' => 'لم يتم إجراء الاختبار',
                ];
            } elseif (!$latestTest->hasPassed($minimumScore)) {
                $missingTests[] = [
                    'type' => $testType,
                    'reason' => "لم يتم اجتياز الاختبار (النتيجة: {$latestTest->score}%، المطلوب: {$minimumScore}%)",
                    'score' => $latestTest->score,
                    'required_score' => $minimumScore,
                ];
            }
        }

        $canComplete = count($missingTests) === 0;

        return [
            'can_complete' => $canComplete,
            'message' => $canComplete 
                ? 'يمكنك إتمام الحفظ الآن' 
                : 'يجب اجتياز جميع الاختبارات المطلوبة أولاً',
            'missing_tests' => $missingTests,
            'minimum_score' => $minimumScore,
        ];
    }
}
