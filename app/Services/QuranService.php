<?php

namespace App\Services;

use App\Repositories\Interfaces\QuranInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class QuranService
{
    protected $quranRepository;

    public function __construct(QuranInterface $quranRepository)
    {
        $this->quranRepository = $quranRepository;
    }

    /**
     * Get a list of Chapters with filtering and sorting
     */
    public function getChapters(array $filters = [], array $sort = []): array
    {
        $chapters = $this->quranRepository->getChapters($filters, $sort);

        return [
            'data' => $chapters,
            'total' => $chapters->count(),
            'filters' => $filters,
            'sort' => $sort
        ];
    }

    /**
     * Get a specific Surah and its verses
     */
    public function getChapterWithVerses(int $chapterId, array $options = []): ?array
    {
        $chapter = $this->quranRepository->getChapter($chapterId);

        if (!$chapter) {
            return null;
        }

        $perPage = $options['per_page'] ?? 10;
        $page = $options['page'] ?? 1;

        $verses = $this->quranRepository->getChapterVerses($chapterId, [], $perPage);

        // تعديل رقم الصفحة إذا تم تمريره
        if ($page > 1 && $page <= $verses->lastPage()) {
            $verses = $this->quranRepository->getChapterVerses($chapterId, [], $perPage, $page);
        }

        $verses->appends([
            'per_page' => $perPage,
        ]);

        return [
            'chapter' => $chapter,
            'verses' => $verses->items(),
            'pagination' => [
                'current_page' => $verses->currentPage(),
                'last_page' => $verses->lastPage(),
                'per_page' => $verses->perPage(),
                'total' => $verses->total(),
                'from' => $verses->firstItem(),
                'to' => $verses->lastItem(),
                'next_page_url' => $verses->nextPageUrl(),
                'prev_page_url' => $verses->previousPageUrl(),
            ]
        ];
    }

    /**
     * Get verses from a specific surah
     */
    public function getChapterVerses(int $chapterId, array $filters = [], int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        return $this->quranRepository->getChapterVerses($chapterId, $filters, $perPage, $page);
    }

    /**
     * Get Juzs
     */
    public function getJuzs(array $options = []): array
    {
        $juzs = $this->quranRepository->getJuzs($options);

        return [
            'data' => $juzs->items(),
            'pagination' => [
                'current_page' => $juzs->currentPage(),
                'last_page' => $juzs->lastPage(),
                'per_page' => $juzs->perPage(),
                'total' => $juzs->total(),
                'from' => $juzs->firstItem(),
                'to' => $juzs->lastItem(),
                'next_page_url' => $juzs->nextPageUrl(),
                'prev_page_url' => $juzs->previousPageUrl(),
            ]
        ];
    }

    /**
     * Get a specific juz with its verses
     */
    public function getJuzWithVerses(int $juzNumber, array $options = []): ?array
    {
        $juz = $this->quranRepository->getJuz($juzNumber);

        if (!$juz) {
            return null;
        }

        $perPage = $options['per_page'] ?? 20;
        $page = $options['page'] ?? 1;

        $verses = $this->quranRepository->getJuzVerses($juzNumber, $options, $perPage, $page);

        return [
            'juz' => $juz,
            'verses' => $verses->items(),
            'pagination' => [
                'current_page' => $verses->currentPage(),
                'last_page' => $verses->lastPage(),
                'per_page' => $verses->perPage(),
                'total' => $verses->total(),
                'from' => $verses->firstItem(),
                'to' => $verses->lastItem(),
                'next_page_url' => $verses->nextPageUrl(),
                'prev_page_url' => $verses->previousPageUrl(),
            ]
        ];
    }

    /**
     * Get verses from a specific page
     */
    public function getPageVerses(int $pageNumber, array $options = []): Collection
    {
        return $this->quranRepository->getPageVerses($pageNumber);
    }

    /**
     * Get verses for a specific hizb
     */
    public function getHizbVerses(int $hizbNumber, array $options = []): array
    {
        $perPage = $options['per_page'] ?? 20;
        $page = $options['page'] ?? 1;
        $verses = $this->quranRepository->getHizbVerses($hizbNumber, $perPage, $page);

        $verses->appends([
            'per_page' => $perPage,
        ]);

        return [
            'data' => $verses->items(),
            'pagination' => [
                'current_page' => $verses->currentPage(),
                'last_page' => $verses->lastPage(),
                'per_page' => $verses->perPage(),
                'total' => $verses->total(),
                'from' => $verses->firstItem(),
                'to' => $verses->lastItem(),
                'next_page_url' => $verses->nextPageUrl(),
                'prev_page_url' => $verses->previousPageUrl(),
            ],
            'hizb_number' => $hizbNumber
        ];
    }

    /**
     * Get verses for a specific rub
     */
    public function getRubVerses(int $rubNumber, array $options = []): array
    {
        $perPage = $options['per_page'] ?? 20;
        $page = $options['page'] ?? 1;
        $verses = $this->quranRepository->getRubVerses($rubNumber, $perPage, $page);

        return [
            'data' => $verses->items(),
            'pagination' => [
                'current_page' => $verses->currentPage(),
                'last_page' => $verses->lastPage(),
                'per_page' => $verses->perPage(),
                'total' => $verses->total(),
                'from' => $verses->firstItem(),
                'to' => $verses->lastItem(),
                'next_page_url' => $verses->nextPageUrl(),
                'prev_page_url' => $verses->previousPageUrl(),
            ],
            'rub_number' => $rubNumber
        ];
    }

    /**
     * Get the verses of prostration
     */
    public function getSajdaVerses(): array
    {
        $verses = $this->quranRepository->getSajdaVerses();

        return [
            'data' => $verses,
            'total' => $verses->count()
        ];
    }

    /**
     * Get words
     */
    public function getWords(array $filters = [], array $options = []): array
    {
        $perPage = $options['per_page'] ?? 50;
        $page = $options['page'] ?? 1;

        $words = $this->quranRepository->getWords($filters, $perPage, $page);

        return [
            'data' => $words->items(),
            'pagination' => [
                'current_page' => $words->currentPage(),
                'last_page' => $words->lastPage(),
                'per_page' => $words->perPage(),
                'total' => $words->total(),
                'from' => $words->firstItem(),
                'to' => $words->lastItem(),
                'next_page_url' => $words->nextPageUrl(),
                'prev_page_url' => $words->previousPageUrl(),
            ],
            'filters' => $filters
        ];
    }

    /**
     * Get recitations
     */
    public function getRecitations(array $filters = [], array $options = []): array
    {
        $perPage = $options['per_page'] ?? 20;
        $page = $options['page'] ?? 1;

        $recitations = $this->quranRepository->getRecitations($filters, $perPage, $page);

        return [
            'data' => $recitations->items(),
            'pagination' => [
                'current_page' => $recitations->currentPage(),
                'last_page' => $recitations->lastPage(),
                'per_page' => $recitations->perPage(),
                'total' => $recitations->total(),
                'from' => $recitations->firstItem(),
                'to' => $recitations->lastItem(),
                'next_page_url' => $recitations->nextPageUrl(),
                'prev_page_url' => $recitations->previousPageUrl(),
            ],
            'filters' => $filters
        ];
    }

    /**
     * Get Quran statistics
     */
    public function getStatistics(): array
    {
        return $this->quranRepository->getStatistics();
    }

    /**
     * Get a random verse
     */
    public function getRandomVerse(array $options = []): ?object
    {
        return $this->quranRepository->getRandomVerse($options);
    }

    /**
     * Get tafsirs
     */
    public function getTafsirs(): array
    {
        $tafsirs = $this->quranRepository->getTafsirs();

        return [
            'data' => $tafsirs,
            'total' => $tafsirs->count()
        ];
    }

    /**
     * Get reciters
     */
    public function getReciters(): array
    {
        $reciters = $this->quranRepository->getReciters();

        return [
            'data' => $reciters,
            'total' => $reciters->count()
        ];
    }

    /**
     * Get a specific verse
     */
    public function getVerse(int $verseId, array $options = []): ?object
    {
        return $this->quranRepository->getVerse($verseId);
    }

    /**
     * Get verses with a set of numbers
     */
    public function getVersesByIds(array $verseIds, array $options = []): array
    {
        $verses = $this->quranRepository->getVersesByIds($verseIds, $options);

        return [
            'data' => $verses,
            'total' => $verses->count(),
            'requested_ids' => $verseIds,
            'found_ids' => $verses->pluck('id')->toArray()
        ];
    }

    /**
     * Get verses with a set of keys (e.g. 2:255)
     */
    public function getVersesByKeys(array $verseKeys, array $options = []): array
    {
        $verses = $this->quranRepository->getVersesByKeys($verseKeys, $options);

        return [
            'data' => $verses,
            'total' => $verses->count(),
            'requested_keys' => $verseKeys,
            'found_keys' => $verses->pluck('verse_key')->toArray()
        ];
    }

    /**
     * Get verses with a set of verse numbers in a specific surah
     */
    public function getVersesByNumbers(int $chapterId, array $verseNumbers, array $options = []): array
    {
        $verses = $this->quranRepository->getVersesByNumbers($chapterId, $verseNumbers, $options);

        return [
            'data' => $verses,
            'total' => $verses->count(),
            'chapter_id' => $chapterId,
            'requested_numbers' => $verseNumbers,
            'found_numbers' => $verses->pluck('verse_number')->toArray()
        ];
    }

    /**
     * Get a range of verses from a specific Surah
     */
    public function getVerseRange(int $chapterId, int $startVerse, int $endVerse, array $options = []): array
    {
        $verses = $this->quranRepository->getVerseRange($chapterId, $startVerse, $endVerse, $options);

        return [
            'data' => $verses,
            'total' => $verses->count(),
            'chapter_id' => $chapterId,
            'start_verse' => $startVerse,
            'end_verse' => $endVerse,
            'range' => "{$startVerse}-{$endVerse}"
        ];
    }
}
