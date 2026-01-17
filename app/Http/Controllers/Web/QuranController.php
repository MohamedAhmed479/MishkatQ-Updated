<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Verse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuranController extends Controller
{
    public function index(Request $request)
    {
        $chapters = Chapter::orderBy('id')
            ->get(['id', 'name_ar', 'name_en', 'verses_count', 'revelation_place']);

        return Inertia::render('Quran/Index', [
            'chapters' => $chapters->map(fn ($chapter) => [
                'id' => $chapter->id,
                'name_arabic' => $chapter->name_ar,
                'name_english' => $chapter->name_en,
                'verses_count' => $chapter->verses_count,
                'revelation_place' => $chapter->revelation_place,
            ]),
        ]);
    }

    public function chapter(Request $request, Chapter $chapter)
    {
        $verses = Verse::where('chapter_id', $chapter->id)
            ->orderBy('verse_number')
            ->get(['id', 'verse_number', 'text_uthmani', 'text_imlaei', 'page_number']);

        return Inertia::render('Quran/Chapter', [
            'chapter' => [
                'id' => $chapter->id,
                'name_arabic' => $chapter->name_ar,
                'name_english' => $chapter->name_en,
                'verses_count' => $chapter->verses_count,
                'revelation_place' => $chapter->revelation_place,
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
