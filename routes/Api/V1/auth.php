<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserAuthController;

Route::controller(UserAuthController::class)->prefix("auth")->group(function () {

    Route::post("register", "register")
        ->name('auth.register');

    Route::post("login", "login")
        ->name('auth.login');

    Route::post('forgot-password', 'passwordResetLink')
        ->name('password.email');

    Route::post('reset-password', 'resetPassword')
        ->name('password.store');
});

Route::controller(UserAuthController::class)->middleware("auth:user")->prefix("auth")->group(function () {
    Route::get('verify-email/{id}/{hash}',  "verifyEmail")
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', "sendEmailVerificationNotification")
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::post('logout', "logout")
        ->name('logout');
});
