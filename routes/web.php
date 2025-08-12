<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\BadgeController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\JuzController;
use App\Http\Controllers\Admin\VerseController;
use App\Http\Controllers\Admin\WordController;
use App\Http\Controllers\Admin\TafsirController;
use App\Http\Controllers\Admin\ReciterController;
use App\Http\Controllers\Admin\RecitationController;
use App\Http\Controllers\Admin\LeaderboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;

// Landing or other public routes can stay here...

// Authentication
Route::middleware('guest:admin')->prefix('admin')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
});

// Admin panel routes (Blade views, session-based auth for 'admin' guard)
Route::prefix('admin')->as('admin.')->group(function () {
    // Authentication
    Route::middleware('guest:admin')->group(function () {
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
        Route::resource('juzs', JuzController::class);
        Route::resource('verses', VerseController::class);
        Route::resource('words', WordController::class);
        Route::resource('tafsirs', TafsirController::class);
        Route::resource('reciters', ReciterController::class);
        Route::resource('recitations', RecitationController::class);

        // Leaderboards CRUD
        Route::resource('leaderboards', LeaderboardController::class);
        Route::delete('leaderboards/bulk-delete', [LeaderboardController::class, 'bulkDelete'])->name('leaderboards.bulk-delete');
        Route::post('leaderboards/recalculate', [LeaderboardController::class, 'recalculate'])->name('leaderboards.recalculate');

        // Notifications
        Route::resource('notifications', NotificationController::class)->only(['index','show','destroy']);
        Route::delete('notifications/bulk-delete', [NotificationController::class, 'bulkDelete'])->name('notifications.bulk-delete');
        Route::patch('notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
        Route::patch('notifications/{notification}/mark-as-unread', [NotificationController::class, 'markAsUnread'])->name('notifications.mark-as-unread');
        Route::patch('notifications/bulk-mark-as-read', [NotificationController::class, 'bulkMarkAsRead'])->name('notifications.bulk-mark-as-read');
        Route::patch('notifications/bulk-mark-as-unread', [NotificationController::class, 'bulkMarkAsUnread'])->name('notifications.bulk-mark-as-unread');

        // Audit Logs CRUD
        Route::resource('audit-logs', AuditLogController::class)->parameters([
            'audit-logs' => 'audit_log'
        ]);

        // Admins CRUD
        Route::resource('admins', AdminManagementController::class)->except(['show']);

        // Roles & Permissions
        Route::resource('roles', RoleController::class)->only(['index','store','update','destroy']);
        Route::resource('permissions', PermissionController::class)->only(['index','store','update','destroy']);

        // Additional chapter routes
        Route::get('chapters/{chapter}/verses', [ChapterController::class, 'verses'])->name('chapters.verses');
        Route::get('chapters/{chapter}/memorization-progress', [ChapterController::class, 'memorizationProgress'])->name('chapters.memorization-progress');
        Route::get('chapters/{chapter}/memorization-plans', [ChapterController::class, 'memorizationPlans'])->name('chapters.memorization-plans');

    });
});

