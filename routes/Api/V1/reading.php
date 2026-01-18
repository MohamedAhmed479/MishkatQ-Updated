<?php

use App\Http\Controllers\Api\V1\ReadingPlanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Reading Plan API Routes
|--------------------------------------------------------------------------
|
| Routes for the Quran reading (Hatmah/Wird) system.
| All routes require authentication via sanctum.
|
*/

Route::middleware(['auth:user'])->prefix('reading')->group(function () {
    
    // Reading Plans
    Route::prefix('plans')->group(function () {
        // Get all user's reading plans
        Route::get('/', [ReadingPlanController::class, 'index']);
        
        // Get suggested plan templates
        Route::get('/suggestions', [ReadingPlanController::class, 'suggestions']);
        
        // Get reading statistics
        Route::get('/statistics', [ReadingPlanController::class, 'statistics']);
        
        // Create a new reading plan
        Route::post('/', [ReadingPlanController::class, 'store']);
        
        // Get specific plan with daily wird
        Route::get('/{planId}', [ReadingPlanController::class, 'show']);
        
        // Delete a reading plan
        Route::delete('/{planId}', [ReadingPlanController::class, 'destroy']);
        
        // Mark reading progress
        Route::post('/{planId}/progress', [ReadingPlanController::class, 'markProgress']);
        
        // Pause a plan
        Route::post('/{planId}/pause', [ReadingPlanController::class, 'pause']);
        
        // Resume a plan
        Route::post('/{planId}/resume', [ReadingPlanController::class, 'resume']);
        
        // Auto-adjust plan to meet deadline
        Route::post('/{planId}/adjust', [ReadingPlanController::class, 'autoAdjust']);
        
        // Update plan settings
        Route::patch('/{planId}/settings', [ReadingPlanController::class, 'updateSettings']);
        
        // Start a new Hatmah after completing
        Route::post('/{planId}/new-hatmah', [ReadingPlanController::class, 'startNewHatmah']);
    });
});
