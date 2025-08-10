<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\BadgeController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\ChapterController;

// Landing or other public routes can stay here...

// Admin panel routes (Blade views, session-based auth for 'admin' guard)
Route::prefix('admin')->as('admin.')->group(function () {
    // Authentication
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::post('logout', [AdminAuthController::class, 'logout'])
        ->middleware('auth:admin')
        ->name('logout');

    // Protected admin area
    Route::middleware('auth:admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Users CRUD
        Route::resource('users', UserManagementController::class);

        // Badges CRUD
        Route::resource('badges', BadgeController::class);
        Route::patch('badges/{badge}/toggle-status', [BadgeController::class, 'toggleStatus'])->name('badges.toggle-status');
        Route::get('badges/{badge}/awarded-users', [BadgeController::class, 'awardedUsers'])->name('badges.awarded-users');

        // Devices CRUD
        Route::resource('devices', DeviceController::class);
        Route::patch('devices/{device}/revoke-token', [DeviceController::class, 'revokeToken'])->name('devices.revoke-token');
        Route::get('devices/user/{user}', [DeviceController::class, 'userDevices'])->name('devices.user-devices');
        Route::delete('devices/bulk-delete', [DeviceController::class, 'bulkDelete'])->name('devices.bulk-delete');

        Route::resource("chapters", ChapterController::class);
        
        // Additional chapter routes
        Route::get('chapters/{chapter}/verses', [ChapterController::class, 'verses'])->name('chapters.verses');
        Route::get('chapters/{chapter}/memorization-progress', [ChapterController::class, 'memorizationProgress'])->name('chapters.memorization-progress');
        Route::get('chapters/{chapter}/memorization-plans', [ChapterController::class, 'memorizationPlans'])->name('chapters.memorization-plans');

    });
});

