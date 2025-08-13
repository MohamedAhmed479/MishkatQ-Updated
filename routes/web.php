<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    AuthController as AdminAuthController,
    DashboardController as AdminDashboardController,
    UserManagementController,
    BadgeController,
    DeviceController,
    ChapterController,
    AuditLogController,
    JuzController,
    VerseController,
    WordController,
    TafsirController,
    ReciterController,
    RecitationController,
    LeaderboardController,
    NotificationController,
    AdminManagementController,
    RoleController,
    PermissionController,
    ProfileController as AdminProfileController,
    MemorizationPlanController as AdminMemorizationPlanController,
    PlanItemController as AdminPlanItemController,
    SpacedRepetitionController as AdminSpacedRepetitionController,
    ReviewRecordController as AdminReviewRecordController
};

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

// Guest routes (login)
Route::prefix('admin')->middleware('guest:admin')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
});

// Protected routes
Route::prefix('admin')->as('admin.')->middleware('auth:admin')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('export/reviews', [AdminDashboardController::class, 'exportReviews'])->name('dashboard.export-reviews');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [AdminProfileController::class, 'edit'])->name('edit');
        Route::put('/', [AdminProfileController::class, 'update'])->name('update');
    });

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */
    Route::resource('users', UserManagementController::class);

    /*
    |--------------------------------------------------------------------------
    | Badges
    |--------------------------------------------------------------------------
    */
    Route::prefix('badges')->name('badges.')->group(function () {
        Route::patch('{badge}/toggle-status', [BadgeController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('{badge}/awarded-users', [BadgeController::class, 'awardedUsers'])->name('awarded-users');
    });
    Route::resource('badges', BadgeController::class);

    /*
    |--------------------------------------------------------------------------
    | Devices
    |--------------------------------------------------------------------------
    */
    Route::prefix('devices')->name('devices.')->group(function () {
        Route::patch('{device}/revoke-token', [DeviceController::class, 'revokeToken'])->name('revoke-token');
        Route::get('user/{user}', [DeviceController::class, 'userDevices'])->name('user-devices');
        Route::delete('bulk-delete', [DeviceController::class, 'bulkDelete'])->name('bulk-delete');
    });
    Route::resource('devices', DeviceController::class);

    /*
    |--------------------------------------------------------------------------
    | Quran Content
    |--------------------------------------------------------------------------
    */
    Route::prefix('chapters')->name('chapters.')->group(function () {
        Route::get('{chapter}/verses', [ChapterController::class, 'verses'])->name('verses');
        Route::get('{chapter}/memorization-progress', [ChapterController::class, 'memorizationProgress'])->name('memorization-progress');
        Route::get('{chapter}/memorization-plans', [ChapterController::class, 'memorizationPlans'])->name('memorization-plans');
    });
    Route::resources([
        'chapters'     => ChapterController::class,
        'juzs'         => JuzController::class,
        'verses'       => VerseController::class,
        'words'        => WordController::class,
        'tafsirs'      => TafsirController::class,
        'reciters'     => ReciterController::class,
        'recitations'  => RecitationController::class,
    ]);

    /*
    |--------------------------------------------------------------------------
    | Memorization Plans
    |--------------------------------------------------------------------------
    */
    Route::resources([
        'memorization-plans' => AdminMemorizationPlanController::class,
        'plan-items'         => AdminPlanItemController::class,
        'spaced-repetitions' => AdminSpacedRepetitionController::class,
        'review-records'     => AdminReviewRecordController::class,
    ]);

    /*
    |--------------------------------------------------------------------------
    | Leaderboards
    |--------------------------------------------------------------------------
    */
    Route::prefix('leaderboards')->name('leaderboards.')->group(function () {
        Route::delete('bulk-delete', [LeaderboardController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('recalculate', [LeaderboardController::class, 'recalculate'])->name('recalculate');
    });
    Route::resource('leaderboards', LeaderboardController::class);

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::delete('bulk-delete', [NotificationController::class, 'bulkDelete'])->name('bulk-delete');
        Route::patch('{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::patch('{notification}/mark-as-unread', [NotificationController::class, 'markAsUnread'])->name('mark-as-unread');
        Route::patch('bulk-mark-as-read', [NotificationController::class, 'bulkMarkAsRead'])->name('bulk-mark-as-read');
        Route::patch('bulk-mark-as-unread', [NotificationController::class, 'bulkMarkAsUnread'])->name('bulk-mark-as-unread');
    });
    Route::resource('notifications', NotificationController::class)->only(['index', 'show', 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Audit Logs
    |--------------------------------------------------------------------------
    */
    Route::resource('audit-logs', AuditLogController::class)->parameters([
        'audit-logs' => 'audit_log'
    ]);

    /*
    |--------------------------------------------------------------------------
    | Admin Management
    |--------------------------------------------------------------------------
    */
    Route::resource('admins', AdminManagementController::class)->except(['show']);

    /*
    |--------------------------------------------------------------------------
    | Roles & Permissions
    |--------------------------------------------------------------------------
    */
    Route::resource('roles', RoleController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('permissions', PermissionController::class)->only(['index', 'store', 'update', 'destroy']);
});
