<?php

use App\Http\Controllers\Api\V1\SpacedRepetitionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserPreferenceController;
use App\Http\Controllers\Api\V1\MemorizationPlanController;
use App\Http\Controllers\Api\V1\PlanItemController;
use Illuminate\Support\Facades\Log;

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
    });

    Route::controller(SpacedRepetitionController::class)->prefix("daily-memorization")->group(function () {
        Route::get("today-revisions", "getTodayRevisions");
         Route::get("last-uncompleted-revisions", "getLastUncompletedRevisions");
         Route::get('/revision/{revisionId}', 'getContentForSpecificRevision');
    });



});



