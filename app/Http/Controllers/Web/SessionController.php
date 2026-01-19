<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PlanItem;
use App\Models\Reciter;
use App\Models\SessionTest;
use App\Models\Tafsir;
use App\Models\Verse;
use App\Services\PlanItemService;
use App\Services\SessionTestService;
use App\Traits\AyaTafsirTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SessionController extends Controller
{
    use AyaTafsirTrait;

    protected PlanItemService $planItemService;
    protected SessionTestService $sessionTestService;

    public function __construct(
        PlanItemService $planItemService,
        SessionTestService $sessionTestService
    ) {
        $this->planItemService = $planItemService;
        $this->sessionTestService = $sessionTestService;
    }

    public function memorize(Request $request, PlanItem $planItem)
    {
        $planItem->load(['memorizationPlan', 'quranSurah', 'verseStart', 'verseEnd']);
        
        // Check if the plan belongs to the authenticated user
        $user = $request->user();
        if (!$user || $planItem->memorizationPlan->user_id !== $user->id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الخطة');
        }

        // Get user's tafsir preference (default to التفسير الميسر id=1)
        $tafsirId = $user->preference?->tafsir_id ?? 1;

        // Get the verses for this plan item with recitations
        $verses = Verse::where('chapter_id', $planItem->quran_surah_id)
            ->whereBetween('id', [$planItem->verse_start_id, $planItem->verse_end_id])
            ->with(['recitations.reciter'])
            ->orderBy('verse_number')
            ->get();

        // Calculate word count
        $wordCount = $verses->sum(function ($verse) {
            return str_word_count($verse->text_imlaei ?? $verse->text_uthmani, 0, 'ا-ي');
        });

        // Get all available reciters
        $reciters = Reciter::orderBy('reciter_name')->get(['id', 'reciter_name']);

        // Get all available tafsirs
        $tafsirs = Tafsir::orderBy('id')->get(['id', 'name']);

        // Map verses with tafsir and recitations
        $chapterId = $planItem->quran_surah_id;
        $versesData = $verses->map(function ($verse) use ($tafsirId, $chapterId) {
            return [
                'id' => $verse->id,
                'verse_number' => $verse->verse_number,
                'text' => $verse->text_imlaei ?? $verse->text_uthmani,
                'text_uthmani' => $verse->text_uthmani,
                'page_number' => $verse->page_number,
                'tafsir' => $this->getAyaTafsir($tafsirId, $chapterId, $verse->verse_number),
                'recitations' => $verse->recitations->map(fn ($recitation) => [
                    'id' => $recitation->id,
                    'reciter_id' => $recitation->reciter_id,
                    'reciter_name' => $recitation->reciter?->reciter_name,
                    'audio_url' => "https://verses.quran.foundation/" . $recitation->audio_url,
                ]),
            ];
        });

        // Get test requirements and status
        $testStatus = $this->sessionTestService->canMarkAsCompleted($user->id, $planItem->id);
        $testResults = $this->sessionTestService->getTestResults($user->id, $planItem->id);

        return Inertia::render('Session/Memorize', [
            'planItem' => [
                'id' => $planItem->id,
                'chapter_name' => $planItem->quranSurah?->name_ar,
                'chapter_id' => $planItem->quran_surah_id,
                'start_verse' => $planItem->verseStart?->verse_number,
                'end_verse' => $planItem->verseEnd?->verse_number,
                'word_count' => $wordCount,
            ],
            'verses' => $versesData,
            'reciters' => $reciters->map(fn ($reciter) => [
                'id' => $reciter->id,
                'name' => $reciter->reciter_name,
            ]),
            'tafsirs' => $tafsirs->map(fn ($tafsir) => [
                'id' => $tafsir->id,
                'name' => $tafsir->name,
            ]),
            'preferredTafsirId' => $tafsirId,
            'testConfig' => [
                'require_tests' => $user->preference?->require_test_before_completion ?? true,
                'minimum_score' => $user->preference?->minimum_test_score ?? 70,
                'can_complete' => $testStatus['can_complete'],
                'missing_tests' => $testStatus['missing_tests'],
                'can_use_smart_recitation' => $user->canUseSmartRecitation(),
            ],
            'testResults' => $testResults,
        ]);
    }

    public function complete(Request $request, PlanItem $planItem)
    {
        $planItem->load('memorizationPlan');
        
        // Check if the plan belongs to the authenticated user
        $user = $request->user();
        if (!$user || $planItem->memorizationPlan->user_id !== $user->id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الخطة');
        }

        $validated = $request->validate([
            'quality_rating' => 'nullable|integer|min:1|max:5',
        ]);

        // Check if user has passed all required tests
        $testStatus = $this->sessionTestService->canMarkAsCompleted($user->id, $planItem->id);
        if (!$testStatus['can_complete']) {
            return redirect()->back()->with('error', $testStatus['message']);
        }

        try {
            $this->planItemService->handleMarkAsCompleted($planItem->id);

            return redirect()->route('user.dashboard')->with('success', 'تم إتمام الجلسة بنجاح! أحسنت 🎉');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء إتمام الجلسة');
        }
    }

    /**
     * Submit test results for a plan item
     */
    public function submitTest(Request $request, PlanItem $planItem)
    {
        $planItem->load('memorizationPlan');
        
        // Check if the plan belongs to the authenticated user
        $user = $request->user();
        if (!$user || $planItem->memorizationPlan->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بالوصول إلى هذه الخطة',
            ], 403);
        }

        $validated = $request->validate([
            'test_type' => 'required|in:recitation,gap_filling,verse_ordering,verse_beginning',
            'score' => 'required|numeric|min:0|max:100',
            'details' => 'nullable|array',
            'details.duration_seconds' => 'nullable|integer|min:0',
        ]);

        try {
            $testResult = $this->sessionTestService->storeTestResult(
                $user->id,
                $planItem->id,
                $validated['test_type'],
                $validated['score'],
                $validated['details'] ?? []
            );

            // Check if user can now complete the plan item
            $testStatus = $this->sessionTestService->canMarkAsCompleted($user->id, $planItem->id);

            return response()->json([
                'success' => true,
                'message' => $testResult->passed ? 'تم اجتياز الاختبار بنجاح!' : 'لم يتم اجتياز الاختبار. حاول مرة أخرى.',
                'test_result' => [
                    'id' => $testResult->id,
                    'score' => $testResult->score,
                    'passed' => $testResult->passed,
                    'attempt_number' => $testResult->attempt_number,
                ],
                'can_complete' => $testStatus['can_complete'],
                'missing_tests' => $testStatus['missing_tests'],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ نتيجة الاختبار',
            ], 500);
        }
    }

    /**
     * Submit multiple test results at once
     */
    public function submitTests(Request $request, PlanItem $planItem)
    {
        $planItem->load('memorizationPlan');
        
        // Check if the plan belongs to the authenticated user
        $user = $request->user();
        if (!$user || $planItem->memorizationPlan->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بالوصول إلى هذه الخطة',
            ], 403);
        }

        $validated = $request->validate([
            'tests' => 'required|array',
            'tests.*.test_type' => 'required|in:recitation,gap_filling,verse_ordering,verse_beginning',
            'tests.*.score' => 'required|numeric|min:0|max:100',
            'tests.*.details' => 'nullable|array',
        ]);

        try {
            $results = $this->sessionTestService->storeMultipleTestResults(
                $user->id,
                $planItem->id,
                $validated['tests']
            );

            // Check if user can now complete the plan item
            $testStatus = $this->sessionTestService->canMarkAsCompleted($user->id, $planItem->id);

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ نتائج الاختبارات بنجاح',
                'results' => collect($results)->map(fn ($r) => [
                    'id' => $r->id,
                    'test_type' => $r->test_type,
                    'score' => $r->score,
                    'passed' => $r->passed,
                ]),
                'can_complete' => $testStatus['can_complete'],
                'missing_tests' => $testStatus['missing_tests'],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ نتائج الاختبارات',
            ], 500);
        }
    }

    /**
     * Get test status for a plan item
     */
    public function getTestStatus(Request $request, PlanItem $planItem)
    {
        $planItem->load('memorizationPlan');
        
        // Check if the plan belongs to the authenticated user
        $user = $request->user();
        if (!$user || $planItem->memorizationPlan->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بالوصول إلى هذه الخطة',
            ], 403);
        }

        $testStatus = $this->sessionTestService->canMarkAsCompleted($user->id, $planItem->id);
        $testResults = $this->sessionTestService->getTestResults($user->id, $planItem->id);
        $testStats = $this->sessionTestService->getTestStats($user->id, $planItem->id);

        return response()->json([
            'success' => true,
            'can_complete' => $testStatus['can_complete'],
            'message' => $testStatus['message'],
            'missing_tests' => $testStatus['missing_tests'],
            'results' => $testResults,
            'stats' => $testStats,
        ]);
    }
}
