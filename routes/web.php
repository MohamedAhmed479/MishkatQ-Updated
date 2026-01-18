<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\SocialLoginController;

use App\Http\Controllers\Admin\{
    AuthController as AdminAuthController,
    DashboardController,
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
    Route::post('login', [AdminAuthController::class, 'login'])->name('admin.login');
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
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('export/reviews', [DashboardController::class, 'exportReviews'])->name('dashboard.export-reviews');

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
                Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
                Route::delete('bulk-delete', [AuditLogController::class, 'bulkDelete'])->name('bulk-delete');
                Route::delete('bulk-delete-by-date', [AuditLogController::class, 'bulkDeleteByDate'])->name('bulk-delete-by-date');
            });
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

// Public landing page
Route::get('/', function () {
    return view('landing');
});

/*
|--------------------------------------------------------------------------
| User Frontend Routes (Inertia)
|--------------------------------------------------------------------------
*/

// Guest routes
Route::middleware('guest:web')->group(function () {
    Route::get('login', fn () => \Inertia\Inertia::render('Auth/Login'))->name('user.login');
    Route::post('login', [\App\Http\Controllers\Web\AuthController::class, 'login'])->name('user.login.post');
    Route::post('resend-verification', [\App\Http\Controllers\Web\AuthController::class, 'resendVerificationEmail'])->name('user.verification.resend');
    Route::get('register', fn () => \Inertia\Inertia::render('Auth/Register'))->name('user.register');
    Route::post('register', [\App\Http\Controllers\Web\AuthController::class, 'register'])->name('user.register.post');
    Route::get('forgot-password', fn () => \Inertia\Inertia::render('Auth/ForgotPassword'))->name('user.password.request');
    Route::post('forgot-password', [\App\Http\Controllers\Web\AuthController::class, 'forgotPassword'])->name('user.password.email');
    Route::get('verify-otp', function (Request $request) {
        return \Inertia\Inertia::render('Auth/VerifyOtp', [
            'email' => $request->session()->get('email'),
            'status' => $request->session()->get('status'),
        ]);
    })->name('user.password.verify');
    Route::post('verify-otp', [\App\Http\Controllers\Web\AuthController::class, 'verifyOtp'])->name('user.password.verify.post');
    Route::get('reset-password', [\App\Http\Controllers\Web\AuthController::class, 'showResetPasswordForm'])->name('user.password.reset');
    Route::post('reset-password', [\App\Http\Controllers\Web\AuthController::class, 'resetPassword'])->name('user.password.reset.post');
});

// Logout
Route::post('logout', [\App\Http\Controllers\Web\AuthController::class, 'logout'])->middleware('auth:web')->name('user.logout');

// Protected user routes
Route::prefix('app')->middleware(['auth:web'])->group(function () {
    // Onboarding
    Route::get('preferences', fn () => \Inertia\Inertia::render('Onboarding/Preferences'))->name('user.preferences');
    
    // Dashboard
    Route::get('dashboard', [\App\Http\Controllers\Web\DashboardController::class, 'index'])->name('user.dashboard');
    
    // Plans
    Route::get('plans', [\App\Http\Controllers\Web\PlanController::class, 'index'])->name('user.plans');
    Route::get('plans/create', [\App\Http\Controllers\Web\PlanController::class, 'create'])->name('user.plans.create');
    Route::post('plans', [\App\Http\Controllers\Web\PlanController::class, 'store'])->name('user.plans.store');
    Route::get('plans/{plan}', [\App\Http\Controllers\Web\PlanController::class, 'show'])->name('user.plans.show');
    Route::get('plans/{plan}/items/{item}', [\App\Http\Controllers\Web\PlanController::class, 'itemDetails'])->name('user.plans.item-details');
    Route::post('plans/{plan}/pause', [\App\Http\Controllers\Web\PlanController::class, 'pause'])->name('user.plans.pause');
    Route::post('plans/{plan}/activate', [\App\Http\Controllers\Web\PlanController::class, 'activate'])->name('user.plans.activate');
    
    // Memorization Session
    Route::get('session/{planItem}', [\App\Http\Controllers\Web\SessionController::class, 'memorize'])->name('user.session.memorize');
    Route::post('session/{planItem}/complete', [\App\Http\Controllers\Web\SessionController::class, 'complete'])->name('user.session.complete');
    
    // Revisions
    Route::get('revisions', [\App\Http\Controllers\Web\RevisionController::class, 'index'])->name('user.revisions');
    Route::get('revisions/{revision}', [\App\Http\Controllers\Web\RevisionController::class, 'show'])->name('user.revisions.show');
    Route::post('revisions/{revision}/record', [\App\Http\Controllers\Web\RevisionController::class, 'record'])->name('user.revisions.record');
    
    // Quran Browser
    Route::get('quran', [\App\Http\Controllers\Web\QuranController::class, 'index'])->name('user.quran');
    Route::get('quran/chapter/{chapter}', [\App\Http\Controllers\Web\QuranController::class, 'chapter'])->name('user.quran.chapter');
    Route::get('quran/juz/{juz}', [\App\Http\Controllers\Web\QuranController::class, 'juz'])->name('user.quran.juz');
    Route::get('quran/verse/{verse}/tafsir', [\App\Http\Controllers\Web\QuranController::class, 'tafsir'])->name('user.quran.tafsir');
    Route::post('quran/verse/{verse}/note', [\App\Http\Controllers\Web\QuranController::class, 'saveNote'])->name('user.quran.save-note');
    Route::delete('quran/verse/{verse}/note', [\App\Http\Controllers\Web\QuranController::class, 'deleteNote'])->name('user.quran.delete-note');
    
    // Achievements & Gamification
    Route::get('achievements', [\App\Http\Controllers\Web\AchievementController::class, 'index'])->name('user.achievements');
    Route::get('leaderboard', [\App\Http\Controllers\Web\LeaderboardController::class, 'index'])->name('user.leaderboard');
    
    // Analytics
    Route::get('analytics', [\App\Http\Controllers\Web\AnalyticsController::class, 'index'])->name('user.analytics');
    
    // Settings
    Route::get('settings', [\App\Http\Controllers\Web\SettingsController::class, 'index'])->name('user.settings');
    Route::put('settings', [\App\Http\Controllers\Web\SettingsController::class, 'update'])->name('user.settings.update');
    
    // Notifications
    Route::get('notifications', [\App\Http\Controllers\Web\NotificationController::class, 'index'])->name('user.notifications');
    Route::get('notifications/api', [\App\Http\Controllers\Web\NotificationController::class, 'api'])->name('user.notifications.api');
    Route::patch('notifications/{id}/read', [\App\Http\Controllers\Web\NotificationController::class, 'markAsRead'])->name('user.notifications.read');

    // Reading System (Hatmah/Wird)
    Route::prefix('reading')->name('user.reading.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\ReadingController::class, 'index'])->name('index');
        Route::get('plans/create', [\App\Http\Controllers\Web\ReadingController::class, 'create'])->name('create');
        Route::post('plans', [\App\Http\Controllers\Web\ReadingController::class, 'store'])->name('store');
        Route::get('experience/{planId}', [\App\Http\Controllers\Web\ReadingController::class, 'experience'])->name('experience');
        Route::get('plans/{planId}/settings', [\App\Http\Controllers\Web\ReadingController::class, 'settings'])->name('settings');
        Route::get('statistics', [\App\Http\Controllers\Web\ReadingController::class, 'statistics'])->name('statistics');
        
        // API-like routes for AJAX calls (using web middleware for session auth)
        Route::prefix('api')->group(function () {
            Route::patch('plans/{planId}/settings', [\App\Http\Controllers\Web\ReadingController::class, 'updateSettings'])->name('api.settings');
            Route::post('plans/{planId}/progress', [\App\Http\Controllers\Web\ReadingController::class, 'markProgress'])->name('api.progress');
            Route::post('plans/{planId}/pause', [\App\Http\Controllers\Web\ReadingController::class, 'pausePlan'])->name('api.pause');
            Route::post('plans/{planId}/resume', [\App\Http\Controllers\Web\ReadingController::class, 'resumePlan'])->name('api.resume');
            Route::post('plans/{planId}/adjust', [\App\Http\Controllers\Web\ReadingController::class, 'autoAdjust'])->name('api.adjust');
            Route::delete('plans/{planId}', [\App\Http\Controllers\Web\ReadingController::class, 'deletePlan'])->name('api.delete');
        });
    });
});

// OAuth Routes
Route::get('auth/{provider}/redirect', [SocialLoginController::class, 'redirect'])
    ->name('auth.socilaite.redirect');
Route::get('auth/{provider}/callback', [SocialLoginController::class, 'Callback'])
    ->name('auth.socilaite.callback');
