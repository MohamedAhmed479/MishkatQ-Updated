<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SpacedRepetition;
use App\Models\Verse;
use App\Repositories\Interfaces\SpacedRepetitionInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RevisionController extends Controller
{
    public function __construct(
        private SpacedRepetitionInterface $spacedRepetitionRepository
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        
        $todayRevisions = $this->spacedRepetitionRepository->getTodayRevisionsForUser($user->id) ?? collect();
        $overdueRevisions = $this->spacedRepetitionRepository->getLastUncompletedRevisionsForUser($user->id) ?? collect();

        // Eager load relationships to avoid N+1 queries
        $todayRevisions->load(['planItem.quranSurah', 'planItem.verseStart', 'planItem.verseEnd']);
        $overdueRevisions->load(['planItem.quranSurah', 'planItem.verseStart', 'planItem.verseEnd']);

        return Inertia::render('Revisions/Index', [
            'todayRevisions' => $todayRevisions->map(fn ($revision) => [
                'id' => $revision->id,
                'chapter_name' => $revision->planItem?->quranSurah?->name_ar,
                'start_verse' => $revision->planItem?->verseStart?->verse_number,
                'end_verse' => $revision->planItem?->verseEnd?->verse_number,
                'status' => $revision->status,
                'scheduled_date' => $revision->scheduled_date?->format('Y-m-d'),
                'repetition_number' => $revision->repetition_number,
            ]),
            'overdueRevisions' => $overdueRevisions->map(fn ($revision) => [
                'id' => $revision->id,
                'chapter_name' => $revision->planItem?->quranSurah?->name_ar,
                'start_verse' => $revision->planItem?->verseStart?->verse_number,
                'end_verse' => $revision->planItem?->verseEnd?->verse_number,
                'status' => $revision->status,
                'scheduled_date' => $revision->scheduled_date?->format('Y-m-d'),
            ]),
        ]);
    }

    public function show(Request $request, SpacedRepetition $revision)
    {
        $planItem = $revision->planItem;
        
        // Load relationships
        $planItem->load(['quranSurah', 'verseStart', 'verseEnd']);
        
        // Get the verses for this revision
        $verses = Verse::where('chapter_id', $planItem->quran_surah_id)
            ->whereBetween('verse_number', [$planItem->verseStart->verse_number, $planItem->verseEnd->verse_number])
            ->orderBy('verse_number')
            ->get(['id', 'verse_number', 'text_uthmani', 'text_imlaei', 'page_number']);

        return Inertia::render('Revisions/Show', [
            'revision' => [
                'id' => $revision->id,
                'chapter_name' => $planItem->quranSurah?->name_ar,
                'start_verse' => $planItem->verseStart?->verse_number,
                'end_verse' => $planItem->verseEnd?->verse_number,
                'repetition_number' => $revision->repetition_number,
            ],
            'verses' => $verses->map(fn ($verse) => [
                'id' => $verse->id,
                'verse_number' => $verse->verse_number,
                'text' => $verse->text_imlaei ?? $verse->text_uthmani,
                'page_number' => $verse->page_number,
            ]),
        ]);
    }
}
