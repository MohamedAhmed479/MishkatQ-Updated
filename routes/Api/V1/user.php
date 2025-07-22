<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserPreferenceController;
use App\Http\Controllers\Api\V1\MemorizationPlanController;

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



});
