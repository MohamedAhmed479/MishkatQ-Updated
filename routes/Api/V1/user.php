<?php

use App\Http\Controllers\Api\V1\SpacedRepetitionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserPreferenceController;
use App\Http\Controllers\Api\V1\MemorizationPlanController;
use App\Http\Controllers\Api\V1\PlanItemController;
use App\Http\Controllers\Api\V1\MemorizationReviewController;
use App\Http\Controllers\Api\V1\MemorizationProgressController;
use App\Http\Controllers\Api\V1\AnalyticsController;

Route::middleware("auth:user")->group(function () {
    // =====================================================================================================
    //                                      User Preferences Routes
    // =====================================================================================================
    Route::controller(UserPreferenceController::class)->prefix("preferences")->group(function () {
        Route::get("/", "getUserPreferences");
        Route::put("/", "updateUserPreferences");
    });

    // =====================================================================================================
    //                                  Memorization Plans Routes
    // =====================================================================================================
    Route::controller(MemorizationPlanController::class)->prefix("/memorization-plans")->group(function () {
        Route::get("/", "index");
        Route::post("/", "store");
        Route::get("/{planId}", "getPlanDetails");
        Route::delete("/{planId}", "deletePlan");
        Route::post("/{planId}/active", "activateMemorizationPlan");
        Route::post("/{planId}/pause", "pauseMemorizationPlan");
    });

    // =====================================================================================================
    //                                 Daily Memorization Routes
    // =====================================================================================================
    Route::controller(PlanItemController::class)->prefix("daily-memorization")->group(function () {
        Route::get('/getTodayItem', 'getTodayItem');
        Route::get('/plan-item/{planItemId}', 'getContentForSpecificItem');
        Route::post('/complete', 'markAsCompleted');
        Route::get('/plan-item/{planItemId}/schedules', 'schedulesRevisions');
    });

    Route::controller(SpacedRepetitionController::class)->prefix("daily-memorization")->group(function () {
         Route::get("today-revisions", "getTodayRevisions");
         Route::get("last-uncompleted-revisions", "getLastUncompletedRevisions");
         Route::get('/revision/{revisionId}', 'getContentForSpecificRevision');
         Route::put('/{revisionId}/postpone', 'postpone');
    });

    Route::controller(MemorizationReviewController::class)->group(function () {
        Route::post('revision-reviews/{revisionId}/record', 'recordRevisionPerformance');
        Route::get('memorization-plans/{planId}/revisions/statistics', 'revisionsStatistics');
    });

    // =====================================================================================================
    //                                 Memorization Progress Routes
    // =====================================================================================================
    Route::controller(MemorizationProgressController::class)->prefix('memorization-progress')->group(function () {
        Route::get('/', 'getProgress');
        Route::get('/chapter/{chapterId}', 'getChapterProgress');
    });

    // =====================================================================================================
    //                                         Analytics Routes
    // =====================================================================================================
    Route::get('active-memorization-plan/analytics', [AnalyticsController::class, 'getProgressAnalytics']);

});
