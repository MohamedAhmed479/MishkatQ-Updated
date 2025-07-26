<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\MemorizationProgress;
use App\Services\MemorizationProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemorizationProgressController extends Controller
{
    public function __construct(
        protected MemorizationProgressService $memorizationProgressService,
    )
    {
    }

    public function getProgress(): JsonResponse
    {
        $user = Auth::user();

        $data = $user->getMemorizationProgress();

        return ApiResponse::success($data, "تم استرجاع بيانات تقدم المستخدم");
    }

    public function getChapterProgress(int $chapterId): JsonResponse
    {
        $user = Auth::user();
        $progress = $user->memorizationProgress()
            ->where('chapter_id', $chapterId)
            ->with('chapter')
            ->first();

        if (!$progress) {
            return ApiResponse::success(null, "لم يتم العثور على أي تقدم في هذا الفصل");
        }

        $data =  [
            'chapter_id' => $progress->chapter_id,
            'chapter_name' => $progress->chapter->name_ar,
            'verses_memorized' => $progress->verses_memorized,
            'total_verses' => $progress->total_verses,
            'progress_percentage' => number_format($progress->getProgressPercentage(), 2, '.', ''),

            'status' => $progress->status,
            'last_reviewed_at' => $progress->last_reviewed_at,
        ];

        return ApiResponse::success($data, "بيانات تقدم المستخدم لسوره " . $progress->chapter->name_ar);
    }

}
