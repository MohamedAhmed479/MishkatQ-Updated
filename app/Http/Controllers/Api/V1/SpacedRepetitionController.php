<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostponeRevisionRequest;
use App\Services\SpacedRepetitionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SpacedRepetitionController extends Controller
{
    public $spacedRepetitionService;

    public function __construct(SpacedRepetitionService $spacedRepetitionService) {
        $this->spacedRepetitionService = $spacedRepetitionService;
    }

    public function postpone(PostponeRevisionRequest $request, $revisionId)
    {
        try {
            $postponeDate = Carbon::parse($request->validated()['postpone_date']);

            return $this->spacedRepetitionService->postponeRevision($revisionId, $postponeDate);

        } catch (ValidationException $exception) {
            return ApiResponse::validationError($exception->errors());
        } catch (\Throwable $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }

    public function getTodayRevisions(): JsonResponse
    {
        try {

            return $this->spacedRepetitionService->todayRevisions(Auth::id());

        } catch (\Throwable $e) {
            return ApiResponse::error("حدث خطأ ما", 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getLastUncompletedRevisions(): JsonResponse
    {
        try {

            return $this->spacedRepetitionService->lastUncompletedRevisions(Auth::id());

        } catch (\Throwable $e) {
            return ApiResponse::error("حدث خطأ ما", 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function getContentForSpecificRevision(int $revisionId): JsonResponse
    {
        try {

            return $this->spacedRepetitionService->getRevisionContent($revisionId);

        } catch (\Throwable $e) {
            return ApiResponse::error("حدث خطأ ما", 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

}
