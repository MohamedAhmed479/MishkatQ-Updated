<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChapterResource;
use App\Http\Resources\VerseResource;
use App\Http\Resources\WordResource;
use App\Http\Resources\RecitationResource;
use App\Http\Resources\ReciterResource;
use App\Services\QuranService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QuranController extends Controller
{
    protected $quranService;

    public function __construct(QuranService $quranService)
    {
        $this->quranService = $quranService;
    }

    /**
     * Get a list of Chapters
     */
    public function chapters(Request $request): JsonResponse
    {
        try {
            $filters = [];
            if ($request->has('revelation_place')) {
                $filters['revelation_place'] = $request->revelation_place;
            }

            $sort = [
                'by' => $request->get('sort_by', 'id'),
                'order' => $request->get('sort_order', 'asc')
            ];

            $result = $this->quranService->getChapters($filters, $sort);

            return ApiResponse::success([
                'data' => ChapterResource::collection($result['data']),
                'total' => $result['total']
            ]);

        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب السور.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Get a specific Chapter
     */
    public function chapter($chapterId, Request $request): JsonResponse
    {
        try {
            $result = $this->quranService->getChapterWithVerses($chapterId, [
                'per_page' => $request->get('per_page', 10),
                'page' => $request->get('page', 1)
            ]);

            if (!$result) {
                return ApiResponse::error('السورة غير موجودة', 404);
            }

            return ApiResponse::success([
                'data' => new ChapterResource($result['chapter']),
                'verses' => VerseResource::collection($result['verses']),
                'pagination' => $result['pagination']
            ]);

        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب بيانات السورة.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Get verses from a specific surah
     */
    public function chapterVerses($chapterId, Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('verse_number')) {
                $filters['verse_number'] = $request->verse_number;
            }
            if ($request->has('page_number')) {
                $filters['page_number'] = $request->page_number;
            }
            if ($request->has('juz_number')) {
                $filters['juz_number'] = $request->juz_number;
            }
            if ($request->has('hizb_number')) {
                $filters['hizb_number'] = $request->hizb_number;
            }
            if ($request->has('rub_number')) {
                $filters['rub_number'] = $request->rub_number;
            }
            if ($request->has('sajda')) {
                $filters['sajda'] = $request->boolean('sajda');
            }

            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            $verses = $this->quranService->getChapterVerses($chapterId, $filters, $perPage, $page);

            return ApiResponse::success([
                'data' => VerseResource::collection($verses->items()),
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
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب الآيات.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Get a specific verse
     */
    public function verse(int $verseId): JsonResponse
    {
        try {
            $verse = $this->quranService->getVerse($verseId);

            if (!$verse) {
                return ApiResponse::error('الآية غير موجودة', 404);
            }

            return ApiResponse::success([
                'data' => new VerseResource($verse)
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب الآية.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Get a random verse
     */
    public function randomVerse(): JsonResponse
    {
        try {
            $verse = $this->quranService->getRandomVerse();

            return ApiResponse::success([
                'data' => new VerseResource($verse)
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب آية عشوائية.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Get a random verse from chapter
     */
    public function randomVerseFromChapter($chapterId): JsonResponse
    {
        try {
            $verse = $this->quranService->getRandomVerse(['chapter_id' => $chapterId]);

            if (!$verse) {
                return ApiResponse::error('السورة غير موجودة أو لا تحتوي على آيات', 404);
            }

            return ApiResponse::success([
                'data' => new VerseResource($verse)
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب آية عشوائية من السورة.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Get a random verse from juz
     */
    public function randomVerseFromJuz($juzNumber): JsonResponse
    {
        try {
            $verse = $this->quranService->getRandomVerse(['juz_number' => $juzNumber]);

            if (!$verse) {
                return ApiResponse::error('الجزء غير موجود أو لا يحتوي على آيات', 404);
            }

            return ApiResponse::success([
                'data' => new VerseResource($verse)
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب آية عشوائية من الجزء.',
                500,
                $e->getMessage()
            );

        }
    }

    /**
     * Get a random verse from page
     */
    public function randomVerseFromPage($pageNumber): JsonResponse
    {
        try {
            $verse = $this->quranService->getRandomVerse(['page_number' => $pageNumber]);

            if (!$verse) {
                return ApiResponse::error('الصفحة غير موجودة أو لا تحتوي على آيات', 404);
            }

            return ApiResponse::success([
                'data' => new VerseResource($verse)
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب آية عشوائية من الصفحة.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Get juzs
     */
    public function juzs(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 30);
            $page = $request->get('page', 1);

            $result = $this->quranService->getJuzs([
                'per_page' => $perPage,
                'page' => $page
            ]);

            return ApiResponse::success([
                'data' => $result['data'],
                'pagination' => $result['pagination']
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب الاجزاء.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Get a specific juz
     */
    public function juz($juzNumber, Request $request): JsonResponse
    {
        try {
            $result = $this->quranService->getJuzWithVerses($juzNumber, [
                'per_page' => $request->get('per_page', 20),
                'page' => $request->get('page', 1)
            ]);

            if (!$result) {
                return ApiResponse::error('الجزء غير موجود', 404);
            }

            return ApiResponse::success([
                'data' => $result
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب الجزء.',
                500,
                $e->getMessage()
            );
        }

    }


    /**
     * Get verses from a specific page
     */
    public function pageVerses($pageNumber, Request $request): JsonResponse
    {
        try {
            $result = $this->quranService->getPageVerses($pageNumber);

            return ApiResponse::success([
                'data' => VerseResource::collection($result),
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب ايات الصفحة.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Get verses for a specific party
     */
    public function hizbVerses($hizbNumber, Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 20);
            $page = $request->get('page', 1);

            $result = $this->quranService->getHizbVerses($hizbNumber, ['per_page' => $perPage, 'page' => $page]);

            return ApiResponse::success([
                'data' => VerseResource::collection($result['data']),
                'pagination' => $result['pagination']
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب ايات الحزب.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Get verses for a specific quarter
     */
    public function rubVerses($rubNumber, Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 20);
            $page = $request->get('page', 1);

            $result = $this->quranService->getRubVerses($rubNumber, ['per_page' => $perPage, 'page' => $page]);

            return ApiResponse::success([
                'data' => VerseResource::collection($result['data']),
                'pagination' => $result['pagination']
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب ايات الربع.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Get the verses of prostration
     */
    public function sajdaVerses(): JsonResponse
    {
        try {
            $result = $this->quranService->getSajdaVerses();

            return ApiResponse::success([
                'data' => VerseResource::collection($result['data']),
                'total' => $result['total']
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب ايات السجدة.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Get words
     */
    public function words(Request $request): JsonResponse
    {
        try {
            $filters = [];

            $perPage = $request->get('per_page', 50);
            $page = $request->get('page', 1);

            $result = $this->quranService->getWords($filters, ['per_page' => $perPage, 'page' => $page]);

            return ApiResponse::success([
                'data' => WordResource::collection($result['data']),
                'pagination' => $result['pagination']
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب الكلمات.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Get tafsirs
     */
    public function tafsirs(): JsonResponse
    {
        try {
            $result = $this->quranService->getTafsirs();

            return ApiResponse::success([
                'data' => $result['data']
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب التفسيرات.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Get reciters
     */
    public function reciters(): JsonResponse
    {
        try {
            $result = $this->quranService->getReciters();

            return ApiResponse::success([
                'data' => ReciterResource::collection($result['data'])
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب القراء.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Get recitations
     */
    public function recitations(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('verse_id')) {
                $filters['verse_id'] = $request->verse_id;
            }

            if ($request->has('reciter_id')) {
                $filters['reciter_id'] = $request->reciter_id;
            }

            $perPage = $request->get('per_page', 20);
            $page = $request->get('page', 1);

            $result = $this->quranService->getRecitations($filters, ['per_page' => $perPage, 'page' => $page]);

            return ApiResponse::success([
                'data' => RecitationResource::collection($result['data']),
                'pagination' => $result['pagination']
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب التلاوات.',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Get Quran statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $result = $this->quranService->getStatistics();

            return ApiResponse::success([
                'data' => $result
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::error(
                'حدث خطأ أثناء جلب احصائيات القران الكريم.',
                500,
                $e->getMessage()
            );
        }
    }
}
