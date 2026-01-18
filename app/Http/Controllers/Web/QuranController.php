<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Verse;
use App\Models\Juz;
use App\Models\Reciter;
use App\Models\UserVerseNote;
use App\Traits\AyaTafsirTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class QuranController extends Controller
{
    use AyaTafsirTrait;

    public function index(Request $request)
    {
        $chapters = Chapter::orderBy('id')
            ->get(['id', 'name_ar', 'name_en', 'verses_count', 'revelation_place']);

        $userProgress = \App\Models\MemorizationProgress::where('user_id', Auth::id())
            ->get()
            ->keyBy('chapter_id');

        $stats = [
            'total_memorized' => $userProgress->sum('verses_memorized'),
            'total_verses' => 6236,
            'memorized_chapters' => $userProgress->where('status', 'completed')->count(),
        ];

        return Inertia::render('Quran/Index', [
            'chapters' => $chapters->map(fn ($chapter) => [
                'id' => $chapter->id,
                'name_arabic' => $chapter->name_ar,
                'name_english' => $chapter->name_en,
                'verses_count' => $chapter->verses_count,
                'revelation_place' => $chapter->revelation_place,
                'progress' => $userProgress[$chapter->id]->verses_memorized ?? 0,
            ]),
            'stats' => $stats,
        ]);
    }

    public function chapter(Request $request, Chapter $chapter)
    {
        $verses = Verse::where('chapter_id', $chapter->id)
            ->with('recitations')
            ->orderBy('verse_number')
            ->get(['id', 'verse_number', 'text_uthmani', 'text_imlaei', 'page_number', 'juz_number']);

        return $this->renderChapterView($chapter, $verses);
    }

    public function juz(Request $request, Juz $juz)
    {
        $verses = Verse::where('juz_number', $juz->juz_number)
            ->with('recitations')
            ->orderBy('chapter_id')
            ->orderBy('verse_number')
            ->get(['id', 'verse_number', 'text_uthmani', 'text_imlaei', 'page_number', 'juz_number', 'chapter_id']);

        // For Juz view, we might want to group by chapter or just show as is
        // Let's use the first chapter as the "main" one for the header
        $firstChapterId = $verses->first()->chapter_id;
        $chapter = Chapter::find($firstChapterId);

        return $this->renderChapterView($chapter, $verses, "الجزء {$juz->juz_number}");
    }

    public function tafsir(Request $request, Verse $verse)
    {
        $tafsirId = $request->query('tafsir_id', 1); // Default to Al-Maysar
        $tafsir = $this->getAyaTafsir($tafsirId, $verse->chapter_id, $verse->verse_number);

        return response()->json([
            'tafsir' => $tafsir,
            'verse_id' => $verse->id,
        ]);
    }

    public function saveNote(Request $request, Verse $verse)
    {
        $request->validate([
            'note' => 'required|string',
        ]);

        $note = UserVerseNote::updateOrCreate(
            ['user_id' => Auth::id(), 'verse_id' => $verse->id],
            ['note' => $request->note]
        );

        return response()->json([
            'message' => 'تم حفظ الخاطرة بنجاح',
            'note' => $note,
        ]);
    }

    public function deleteNote(Request $request, Verse $verse)
    {
        UserVerseNote::where('user_id', Auth::id())
            ->where('verse_id', $verse->id)
            ->delete();

        return response()->json([
            'message' => 'تم حذف الخاطرة بنجاح',
        ]);
    }

    protected function renderChapterView($chapter, $verses, $title = null)
    {
        $chapters = Chapter::orderBy('id')->get(['id', 'name_ar']);
        $juzs = Juz::orderBy('juz_number')->get(['id', 'juz_number']);
        $reciters = Reciter::all(['id', 'reciter_name']);
        
        $userNotes = UserVerseNote::where('user_id', Auth::id())
            ->whereIn('verse_id', $verses->pluck('id'))
            ->get()
            ->keyBy('verse_id');

        // Fetch user's memorization progress for this chapter
        $memorizationProgress = \App\Models\MemorizationProgress::where('user_id', Auth::id())
            ->where('chapter_id', $chapter->id)
            ->first();

        // Fetch user's memorization plan items for these verses
        $planItems = \App\Models\PlanItem::whereHas('memorizationPlan', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->where('quran_surah_id', $chapter->id)
            ->with('spacedRepetitions')
            ->get();

        // Load words for verses
        $verses->load('words');

        return Inertia::render('Quran/Chapter', [
            'chapter' => [
                'id' => $chapter->id,
                'name_arabic' => $chapter->name_ar,
                'name_english' => $chapter->name_en,
                'verses_count' => $chapter->verses_count,
                'revelation_place' => $chapter->revelation_place,
                'display_title' => $title ?? "سورة {$chapter->name_ar}",
            ],
            'verses' => $verses->map(function ($verse) use ($userNotes, $planItems) {
                // Determine hifz status
                $hifzStatus = 'none'; // 'none', 'memorized', 'review'
                $planItem = $planItems->first(function($item) use ($verse) {
                    return $verse->id >= $item->verse_start_id && $verse->id <= $item->verse_end_id;
                });

                if ($planItem) {
                    $repetition = $planItem->spacedRepetitions->first();
                    if ($repetition) {
                        $hifzStatus = ($repetition->stability >= 90) ? 'memorized' : 'review';
                    } else {
                        $hifzStatus = 'review'; // In plan but not yet mastered
                    }
                }

                return [
                    'id' => $verse->id,
                    'verse_number' => $verse->verse_number,
                    'text_uthmani' => $verse->text_uthmani,
                    'text_imlaei' => $verse->text_imlaei,
                    'page_number' => $verse->page_number,
                    'juz_number' => $verse->juz_number,
                    'chapter_id' => $verse->chapter_id,
                    'recitations' => $verse->recitations->map(fn ($r) => [
                        'reciter_id' => $r->reciter_id,
                        'audio_url' => $r->full_audio_url,
                    ]),
                    'words' => $verse->words->map(fn ($word) => [
                        'id' => $word->id,
                        'text' => $word->text,
                        'position' => $word->position,
                        'translation' => $word->translation,
                    ]),
                    'user_note' => $userNotes[$verse->id]->note ?? null,
                    'hifz_status' => $hifzStatus,
                ];
            }),
            'chapters_list' => $chapters->map(fn ($c) => [
                'id' => $c->id,
                'name_arabic' => $c->name_ar,
            ]),
            'juzs_list' => $juzs->map(fn ($j) => [
                'id' => $j->id,
                'juz_number' => $j->juz_number,
            ]),
            'reciters_list' => $reciters,
            'memorization_stats' => [
                'memorized_count' => $memorizationProgress ? $memorizationProgress->verses_memorized : 0,
                'review_count' => $planItems->sum(function($item) {
                    $repetition = $item->spacedRepetitions->first();
                    if ($repetition && $repetition->stability < 90) {
                        return ($item->verse_end_id - $item->verse_start_id) + 1;
                    }
                    return 0;
                }),
            ],
        ]);
    }
}
