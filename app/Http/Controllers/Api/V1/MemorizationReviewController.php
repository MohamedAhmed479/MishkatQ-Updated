<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostponeRevisionRequest;
use App\Http\Requests\recordPerformanceRequest;
use App\Repositories\Interfaces\PlanItemInterface;
use App\Repositories\Interfaces\SpacedRepetitionInterface;
use App\Services\MemorizationReviewService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MemorizationReviewController extends Controller
{

    public function __construct(
        protected MemorizationReviewService $memorizationReviewService
    )
    {
    }

    public function recordRevisionPerformance(recordPerformanceRequest $request, $revisionId): JsonResponse
    {
        try {
            DB::beginTransaction();

            $response = $this->memorizationReviewService->recordPerformance($request->validated(), $revisionId);

            DB::commit();

            return $response;

        } catch (\Throwable $e) {
            DB::rollBack();

            return ApiResponse::error("حدث خطأ ما", 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }


}
