<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PlanItem;
use App\Models\Reciter;
use App\Models\Tafsir;
use App\Models\Verse;
use App\Services\PlanItemService;
use App\Traits\AyaTafsirTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SessionController extends Controller
{
    use AyaTafsirTrait;

    protected PlanItemService $planItemService;

    public function __construct(PlanItemService $planItemService)
    {
        $this->planItemService = $planItemService;
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

        try {
            $this->planItemService->handleMarkAsCompleted($planItem->id);

            return redirect()->route('user.dashboard')->with('success', 'تم إتمام الجلسة بنجاح! أحسنت 🎉');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء إتمام الجلسة');
        }
    }
}
