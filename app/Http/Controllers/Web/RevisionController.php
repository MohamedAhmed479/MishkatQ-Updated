<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Reciter;
use App\Models\SpacedRepetition;
use App\Models\Verse;
use App\Repositories\Interfaces\SpacedRepetitionInterface;
use App\Services\FSRSService;
use App\Services\MemorizationReviewService;
use App\Services\SpacedRepetitionService;
use App\Traits\AyaTafsirTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RevisionController extends Controller
{
    use AyaTafsirTrait;

    public function __construct(
        private SpacedRepetitionInterface $spacedRepetitionRepository,
        private FSRSService $fsrsService,
        private SpacedRepetitionService $spacedRepetitionService,
        private MemorizationReviewService $memorizationReviewService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        
        $todayRevisions = $this->spacedRepetitionRepository->getTodayRevisionsForUser($user->id) ?? collect();
        $overdueRevisions = $this->spacedRepetitionRepository->getLastUncompletedRevisionsForUser($user->id) ?? collect();

        // Eager load relationships to avoid N+1 queries
        $todayRevisions->load(['planItem.quranSurah', 'planItem.verseStart', 'planItem.verseEnd']);
        $overdueRevisions->load(['planItem.quranSurah', 'planItem.verseStart', 'planItem.verseEnd']);

        // Get memory statistics and heatmap data
        $memoryStats = $this->spacedRepetitionService->getMemoryStatistics($user->id);
        $memoryHeatmap = $this->spacedRepetitionService->getMemoryHeatmap($user->id);
        $leeches = $this->spacedRepetitionService->getLeeches($user->id);

        return Inertia::render('Revisions/Index', [
            'todayRevisions' => $todayRevisions->map(fn ($revision) => $this->formatRevision($revision)),
            'overdueRevisions' => $overdueRevisions->map(fn ($revision) => $this->formatRevision($revision, true)),
            'memoryStats' => $memoryStats,
            'memoryHeatmap' => $memoryHeatmap,
            'leeches' => $leeches->map(fn ($leech) => [
                'id' => $leech->id,
                'chapter_name' => $leech->planItem?->quranSurah?->name_ar,
                'start_verse' => $leech->planItem?->verseStart?->verse_number,
                'end_verse' => $leech->planItem?->verseEnd?->verse_number,
                // reviewRecord is already eager loaded in getLeeches(), and it's HasOne
                'fail_count' => ($leech->reviewRecord && !$leech->reviewRecord->successful) 
                    ? $leech->repetition_count 
                    : 0,
            ]),
        ]);
    }

    /**
     * Format a revision with FSRS data
     */
    private function formatRevision(SpacedRepetition $revision, bool $isOverdue = false): array
    {
        $stability = $revision->stability ?? 1.0;
        $daysElapsed = $revision->last_reviewed_at 
            ? Carbon::now()->diffInDays($revision->last_reviewed_at) 
            : 0;
        
        $retrievability = $this->fsrsService->calculateRetrievability($stability, $daysElapsed);
        $priorityScore = $this->fsrsService->calculatePriorityScore(
            $retrievability, 
            $stability, 
            $isOverdue ? Carbon::today()->diffInDays($revision->scheduled_date, false) : 0
        );

        return [
            'id' => $revision->id,
            'chapter_name' => $revision->planItem?->quranSurah?->name_ar,
            'start_verse' => $revision->planItem?->verseStart?->verse_number,
            'end_verse' => $revision->planItem?->verseEnd?->verse_number,
            'status' => $revision->last_reviewed_at ? 'completed' : 'pending',
            'scheduled_date' => $revision->scheduled_date?->format('Y-m-d'),
            'repetition_number' => $revision->interval_index,
            // FSRS Data
            'stability' => round($stability, 1),
            'difficulty' => round($revision->difficulty ?? 5.0, 1),
            'retrievability' => round($retrievability * 100, 1),
            'memory_state' => $this->fsrsService->getMemoryState($stability),
            'memory_state_ar' => $this->fsrsService->getMemoryStateArabic($stability),
            'priority_score' => round($priorityScore, 1),
        ];
    }

    public function show(Request $request, SpacedRepetition $revision)
    {
        // Eager load all needed relationships
        $revision->load(['planItem.quranSurah', 'planItem.verseStart', 'planItem.verseEnd']);
        
        $planItem = $revision->planItem;
        $user = $request->user();
        
        // Get user's preferred tafsir and reciter (default tafsir: التفسير الميسر = 1)
        $preferredTafsirId = $user->preference?->tafsir_id ?? 1;
        $preferredReciterId = $user->preference?->reciter_id ?? 1; // Default to first reciter
        
        // Get the verses for this revision with words and recitations
        $verses = Verse::where('chapter_id', $planItem->quran_surah_id)
            ->whereBetween('verse_number', [$planItem->verseStart->verse_number, $planItem->verseEnd->verse_number])
            ->orderBy('verse_number')
            ->with(['words', 'recitations.reciter'])
            ->get(['id', 'verse_number', 'text_uthmani', 'text_imlaei', 'page_number', 'chapter_id']);

        // Calculate current FSRS state
        $stability = $revision->stability ?? 1.0;
        $daysElapsed = $revision->last_reviewed_at 
            ? Carbon::now()->diffInDays($revision->last_reviewed_at) 
            : 0;
        $retrievability = $this->fsrsService->calculateRetrievability($stability, $daysElapsed);

        // Get available reciters
        $reciters = Reciter::all(['id', 'reciter_name']);

        return Inertia::render('Revisions/Show', [
            'revision' => [
                'id' => $revision->id,
                'chapter_name' => $planItem->quranSurah?->name_ar,
                'chapter_id' => $planItem->quran_surah_id,
                'start_verse' => $planItem->verseStart?->verse_number,
                'end_verse' => $planItem->verseEnd?->verse_number,
                'repetition_number' => $revision->interval_index,
                // FSRS State
                'stability' => round($stability, 1),
                'difficulty' => round($revision->difficulty ?? 5.0, 1),
                'retrievability' => round($retrievability * 100, 1),
                'memory_state' => $this->fsrsService->getMemoryState($stability),
                'memory_state_ar' => $this->fsrsService->getMemoryStateArabic($stability),
            ],
            'verses' => $verses->map(function ($verse) use ($preferredTafsirId, $preferredReciterId, $planItem) {
                // Base URL for audio files
                $audioBaseUrl = 'https://verses.quran.foundation/';
                
                // Get tafsir for this verse
                $tafsir = $preferredTafsirId 
                    ? $this->getAyaTafsir($preferredTafsirId, $planItem->quran_surah_id, $verse->verse_number)
                    : null;

                // Get audio URL for preferred reciter
                $recitation = $verse->recitations->firstWhere('reciter_id', $preferredReciterId);
                $audioUrl = $recitation?->audio_url 
                    ? $audioBaseUrl . ltrim($recitation->audio_url, '/') 
                    : null;

                // Get all available recitations for this verse
                $availableRecitations = $verse->recitations->map(fn ($rec) => [
                    'reciter_id' => $rec->reciter_id,
                    'reciter_name' => $rec->reciter?->reciter_name,
                    'audio_url' => $rec->audio_url ? $audioBaseUrl . ltrim($rec->audio_url, '/') : null,
                ]);

                return [
                    'id' => $verse->id,
                    'verse_number' => $verse->verse_number,
                    'text' => $verse->text_imlaei ?? $verse->text_uthmani,
                    'text_uthmani' => $verse->text_uthmani,
                    'page_number' => $verse->page_number,
                    'tafsir' => $tafsir,
                    'audio_url' => $audioUrl,
                    'recitations' => $availableRecitations,
                    'words' => $verse->words->map(fn ($word) => [
                        'id' => $word->id,
                        'text' => $word->text_uthmani ?? $word->text,
                        'position' => $word->position,
                    ]),
                ];
            }),
            'reciters' => $reciters,
            'preferredReciterId' => $preferredReciterId,
        ]);
    }

    /**
     * Record revision performance
     */
    public function record(Request $request, SpacedRepetition $revision)
    {
        $request->validate([
            'performance_rating' => 'required|integer|min:1|max:5',
        ]);

        try {
            DB::beginTransaction();

            // Process the review using FSRS
            $performanceRating = (int) $request->input('performance_rating');
            
            $fsrsResult = $this->fsrsService->processReview(
                $revision->stability,
                $revision->difficulty,
                $performanceRating,
                $revision->last_reviewed_at ? Carbon::parse($revision->last_reviewed_at) : null
            );

            // Create review record
            $successful = $performanceRating >= 3;
            \App\Models\ReviewRecord::create([
                'spaced_repetition_id' => $revision->id,
                'performance_rating' => $performanceRating,
                'review_date' => Carbon::now(),
                'successful' => $successful,
            ]);

            // Update the spaced repetition with FSRS results
            $revision->update([
                'stability' => $fsrsResult['stability'],
                'difficulty' => $fsrsResult['difficulty'],
                'last_reviewed_at' => Carbon::now(),
                'scheduled_date' => $fsrsResult['scheduled_date'],
                'interval_index' => $revision->interval_index + 1,
            ]);

            DB::commit();

            return redirect()->route('user.revisions')->with('success', 'تم حفظ التقييم بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error recording revision performance: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'حدث خطأ أثناء حفظ التقييم: ' . $e->getMessage()]);
        }
    }
}
