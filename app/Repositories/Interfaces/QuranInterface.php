<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface QuranInterface
{
    /**
     * Get the list of surahs
     */
    public function getChapters(array $filters = [], array $sort = []): Collection;

    /**
     * Get a specific Surah
     */
    public function getChapter(int $id): ?object;

    /**
     * Get a surah with its verses
     */
    public function getChapterWithVerses(int $id): ?array;

    /**
     * Get verses from a specific surah
     */
    public function getChapterVerses(int $chapterId, array $filters = [], int $perPage = 10, int $page = 1): LengthAwarePaginator;

    /**
     * Get a specific verse
     */
    public function getVerse(int $id): ?object;

    /**
     * Get juzs
     */
    public function getJuzs(array $options = []): LengthAwarePaginator;

    /**
     * Get a specific juz
     */
    public function getJuz(int $juzNumber): ?object;

    /**
     * Get verses from a specific part
     */
    public function getJuzVerses(int $juzNumber, array $filters = [], int $perPage = 20, int $page = 1): LengthAwarePaginator;

    /**
     * Get verses from a specific page
     */
    public function getPageVerses(int $pageNumber): Collection;

    /**
     * Get verses for a specific hizb
     */
    public function getHizbVerses(int $hizbNumber, int $perPage = 20, int $page = 1): LengthAwarePaginator;

    /**
     * Get verses for a specific rub
     */
    public function getRubVerses(int $rubNumber, int $perPage = 20, int $page = 1): LengthAwarePaginator;

    /**
     * Get the verses of Sajda
     */
    public function getSajdaVerses(): Collection;

    /**
     * Get words
     */
    public function getWords(array $filters = [], int $perPage = 50, int $page = 1): LengthAwarePaginator;

    /**
     * Get tafsirs
     */
    public function getTafsirs(): Collection;

    /**
     * Get readers
     */
    public function getReciters(): Collection;

    /**
     * Get recitations
     */
    public function getRecitations(array $filters = [], int $perPage = 20, int $page = 1): LengthAwarePaginator;

    /**
     * Get Quran statistics
     */
    public function getStatistics(): array;

    /**
     * Get a random verse
     */
    public function getRandomVerse(array $options = []): ?object;

    /**
     * Get a random verse from chapter
     */
    public function getRandomVerseFromChapter(int $chapterId): ?object;

    /**
     * Get a random verse from juz
     */
    public function getRandomVerseFromJuz(int $juzNumber): ?object;

    /**
     * Get a random verse from page
     */
    public function getRandomVerseFromPage(int $pageNumber): ?object;

    /**
     * Get verses with a set of numbers
     */
    public function getVersesByIds(array $verseIds, array $options = []): Collection;

    /**
     * Get verses with a set of keys
     */
    public function getVersesByKeys(array $verseKeys, array $options = []): Collection;

    /**
     * Get verses with a set of verse numbers in a specific surah
     */
    public function getVersesByNumbers(int $chapterId, array $verseNumbers, array $options = []): Collection;

    /**
     * Get a range of verses from a specific Surah
     */
    public function getVerseRange(int $chapterId, int $startVerse, int $endVerse, array $options = []): Collection;
}
