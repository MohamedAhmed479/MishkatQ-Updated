<?php

namespace App\Repositories\Eloquent;

use App\Models\Chapter;
use App\Models\Verse;
use App\Models\Word;
use App\Models\Juz;
use App\Models\Tafsir;
use App\Models\Recitation;
use App\Models\Reciter;
use App\Repositories\Interfaces\QuranInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class QuranRepository implements QuranInterface
{
    /**
     * Get the list of surahs
     */
    public function getChapters(array $filters = [], array $sort = []): Collection
    {
        $query = Chapter::query();

        if (isset($filters['revelation_place'])) {
            $query->where('revelation_place', $filters['revelation_place']);
        }

        $sortBy = $sort['by'] ?? 'id';
        $sortOrder = $sort['order'] ?? 'asc';

        if (in_array($sortBy, ['id', 'name_ar', 'name_en', 'revelation_order', 'verses_count'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query->get();
    }

    /**
     * Get a specific Surah
     */
    public function getChapter(int $id): ?object
    {
        return Chapter::find($id);
    }

    /**
     * Get a surah with its verses
     */
    public function getChapterWithVerses(int $id): ?array
    {
        $chapter = Chapter::find($id);

        if (!$chapter) {
            return null;
        }

        $verses = Verse::where('chapter_id', $id)
            ->with(['words', 'recitations.reciter'])
            ->orderBy('verse_number')
            ->get();

        return [
            'chapter' => $chapter,
            'verses' => $verses,
            'total_verses' => $verses->count()
        ];
    }

    /**
     * Get verses from a specific surah
     */
    public function getChapterVerses(int $chapterId, array $filters = [], int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        $query = Verse::where('chapter_id', $chapterId)
            ->with(['words', 'recitations.reciter']);

        // تطبيق الفلاتر
        if (isset($filters['verse_number'])) {
            $query->where('verse_number', $filters['verse_number']);
        }

        if (isset($filters['page_number'])) {
            $query->where('page_number', $filters['page_number']);
        }

        if (isset($filters['juz_number'])) {
            $query->where('juz_number', $filters['juz_number']);
        }

        if (isset($filters['hizb_number'])) {
            $query->where('hizb_number', $filters['hizb_number']);
        }

        if (isset($filters['rub_number'])) {
            $query->where('rub_number', $filters['rub_number']);
        }

        if (isset($filters['sajda'])) {
            $query->where('sajda', $filters['sajda']);
        }

        $query->orderBy('verse_number');

        $verses =  $query->paginate($perPage, ['*'], 'page', $page);

        $verses->appends([
            'per_page' => $perPage,
        ]);

        return $verses;
    }

    /**
     * Get a specific verse
     */
    public function getVerse(int $id): ?object
    {
        return Verse::with(['chapter', 'words', 'recitations.reciter'])->find($id);
    }

    /**
     * Get juzs
     */
    public function getJuzs(array $options = []): LengthAwarePaginator
    {
        $perPage = $options['per_page'] ?? 30;
        $page = $options['page'] ?? 1;

        $juzs = Juz::with(['startVerse.chapter', 'endVerse.chapter'])
            ->orderBy('juz_number')
            ->paginate($perPage, ['*'], 'page', $page);

        $juzs->appends([
            'per_page' => $perPage,
        ]);

        return $juzs;
    }

    /**
     * Get a specific juz
     */
    public function getJuz(int $juzNumber): ?object
    {
        return Juz::with(['startVerse.chapter', 'endVerse.chapter'])
            ->where('juz_number', $juzNumber)
            ->first();
    }

    /**
     * Get verses from a specific juz
     */
    public function getJuzVerses(int $juzNumber, array $filters = [], int $perPage = 20, int $page = 1): LengthAwarePaginator
    {
        $query = Verse::where('juz_number', $juzNumber)
            ->with(['chapter', 'words']);

        if (isset($filters['chapter_id'])) {
            $query->where('chapter_id', $filters['chapter_id']);
        }

        $query->orderBy('verse_number');

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get verses from a specific page
     */
    public function getPageVerses(int $pageNumber): Collection
    {
        return Verse::where('page_number', $pageNumber)
            ->with(['chapter', 'words'])
            ->orderBy('verse_number')
            ->get();
    }

    /**
     * Get verses for a specific hizb
     */
    public function getHizbVerses(int $hizbNumber, int $perPage = 20, int $page = 1): LengthAwarePaginator
    {
        return Verse::where('hizb_number', $hizbNumber)
            ->with(['chapter', 'words'])
            ->orderBy('verse_number')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get verses for a specific rub
     */
    public function getRubVerses(int $rubNumber, int $perPage = 20, int $page = 1): LengthAwarePaginator
    {
        return Verse::where('rub_number', $rubNumber)
            ->with(['chapter', 'words'])
            ->orderBy('verse_number')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get the verses of Sajda
     */
    public function getSajdaVerses(): Collection
    {
        return Verse::where('sajda', true)
            ->with(['chapter', 'words'])
            ->orderBy('verse_number')
            ->get();
    }

    /**
     * Get words
     */
    public function getWords(array $filters = [], int $perPage = 50, int $page = 1): LengthAwarePaginator
    {
        $query = Word::with(['verse.chapter']);

        if (isset($filters['verse_id'])) {
            $query->where('verse_id', $filters['verse_id']);
        }

        if (isset($filters['chapter_id'])) {
            $query->whereHas('verse', function($q) use ($filters) {
                $q->where('chapter_id', $filters['chapter_id']);
            });
        }


        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get tafsirs
     */
    public function getTafsirs(): Collection
    {
        return Tafsir::all();
    }

    /**
     * Get readers
     */
    public function getReciters(): Collection
    {
        return Reciter::all();
    }

    /**
     * Get recitations
     */
    public function getRecitations(array $filters = [], int $perPage = 20, int $page = 1): LengthAwarePaginator
    {
        $query = Recitation::with(['verse.chapter', 'reciter']);

        if (isset($filters['verse_id'])) {
            $query->where('verse_id', $filters['verse_id']);
        }

        if (isset($filters['reciter_id'])) {
            $query->where('reciter_id', $filters['reciter_id']);
        }

        if (isset($filters['chapter_id'])) {
            $query->whereHas('verse', function($q) use ($filters) {
                $q->where('chapter_id', $filters['chapter_id']);
            });
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get Quran statistics
     */
    public function getStatistics(): array
    {
        return Cache::remember('quran_statistics', 3600, function () {
            return [
                'total_chapters' => Chapter::count(),
                'total_verses' => Verse::count(),
                'total_words' => Word::count(),
                'total_juzs' => Juz::count(),
                'total_pages' => Verse::distinct('page_number')->count(),
                'total_hizbs' => Verse::distinct('hizb_number')->count(),
                'total_rubs' => Verse::distinct('rub_number')->count(),
                'sajda_verses' => Verse::where('sajda', true)->count(),
                'makkah_chapters' => Chapter::where('revelation_place', 'makkah')->count(),
                'madinah_chapters' => Chapter::where('revelation_place', 'madinah')->count(),
                'total_reciters' => Reciter::count(),
                'total_tafsirs' => Tafsir::count(),
                'longest_chapter' => Chapter::orderBy('verses_count', 'desc')->first(),
                'shortest_chapter' => Chapter::orderBy('verses_count', 'asc')->first(),
                'average_verses_per_chapter' => round(Chapter::avg('verses_count'), 2),
                'average_words_per_verse' => round(Word::count() / Verse::count(), 2),
            ];
        });
    }

    /**
     * Get a random verse
     */
    public function getRandomVerse(array $options = []): ?object
    {
        $query = Verse::with(['chapter', 'words']);

        if (isset($options['chapter_id'])) {
            $query->where('chapter_id', $options['chapter_id']);
        }

        if (isset($options['juz_number'])) {
            $query->where('juz_number', $options['juz_number']);
        }

        if (isset($options['page_number'])) {
            $query->where('page_number', $options['page_number']);
        }

        return $query->inRandomOrder()->first();
    }

    /**
     * Get a random verse from chapter
     */
    public function getRandomVerseFromChapter(int $chapterId): ?object
    {
        return Verse::where('chapter_id', $chapterId)
            ->with(['chapter', 'words'])
            ->inRandomOrder()
            ->first();
    }

    /**
     * Get a random verse from juz
     */
    public function getRandomVerseFromJuz(int $juzNumber): ?object
    {
        return Verse::where('juz_number', $juzNumber)
            ->with(['chapter', 'words'])
            ->inRandomOrder()
            ->first();
    }

    /**
     * Get a random verse from page
     */
    public function getRandomVerseFromPage(int $pageNumber): ?object
    {
        return Verse::where('page_number', $pageNumber)
            ->with(['chapter', 'words'])
            ->inRandomOrder()
            ->first();
    }

    /**
     * Get verses with a set of numbers
     */
    public function getVersesByIds(array $verseIds, array $options = []): Collection
    {
        $query = Verse::whereIn('id', $verseIds)
            ->with(['chapter', 'words']);

        if (isset($options['include_recitations']) && $options['include_recitations']) {
            $query->with('recitations.reciter');
        }

        return $query->get();
    }

    /**
     * Get verses with a set of keys
     */
    public function getVersesByKeys(array $verseKeys, array $options = []): Collection
    {
        $query = Verse::whereIn('verse_key', $verseKeys)
            ->with(['chapter', 'words']);

        if (isset($options['include_recitations']) && $options['include_recitations']) {
            $query->with('recitations.reciter');
        }

        return $query->get();
    }

    /**
     * Get verses with a set of verse numbers in a specific surah
     */
    public function getVersesByNumbers(int $chapterId, array $verseNumbers, array $options = []): Collection
    {
        $query = Verse::where('chapter_id', $chapterId)
            ->whereIn('verse_number', $verseNumbers)
            ->with(['chapter', 'words']);

        if (isset($options['include_recitations']) && $options['include_recitations']) {
            $query->with('recitations.reciter');
        }

        return $query->orderBy('verse_number')->get();
    }

    /**
     * Get a range of verses from a specific Surah
     */
    public function getVerseRange(int $chapterId, int $startVerse, int $endVerse, array $options = []): Collection
    {
        $query = Verse::where('chapter_id', $chapterId)
            ->whereBetween('verse_number', [$startVerse, $endVerse])
            ->with(['chapter', 'words']);

        if (isset($options['include_recitations']) && $options['include_recitations']) {
            $query->with('recitations.reciter');
        }

        return $query->orderBy('verse_number')->get();
    }
}
