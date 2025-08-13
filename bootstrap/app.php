<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('reviews:process-daily')
            ->dailyAt('02:00')
            ->timezone('Africa/Cairo')
            ->onSuccess(function () {
                \Illuminate\Support\Facades\Log::info('Daily reviews processing completed successfully.');
            })
            ->onFailure(function () {
                \Illuminate\Support\Facades\Log::error('Daily reviews processing failed.');
            });

        $schedule->command('memorization:auto-adjust')
            ->dailyAt('01:00')
            ->timezone('Africa/Cairo')
            ->onSuccess(function () {
                \Illuminate\Support\Facades\Log::info('auto-adjustment completed successfully.');
            })
            ->onFailure(function () {
                \Illuminate\Support\Facades\Log::error('auto-adjustment processing failed.');
            });

        // Update daily leaderboards at midnight
        $schedule->command('leaderboards:update --type=daily')
            ->daily()
            ->at('00:00');

        // Update weekly leaderboards at midnight on Sundays
        $schedule->command('leaderboards:update --type=weekly')
            ->weekly()
            ->sundays()
            ->at('00:00');

        // Update monthly leaderboards at midnight on the first day of each month
        $schedule->command('leaderboards:update --type=monthly')
            ->monthly()
            ->at('00:00');

        // Update yearly leaderboards at midnight on January 1st
        $schedule->command('leaderboards:update --type=yearly')
            ->yearly()
            ->at('00:00');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'verified.user' => \App\Http\Middleware\EnsureUserEmailIsVerified::class,
            'audit' => \App\Http\Middleware\AuditMiddleware::class,

        ]);
        // Apply audit middleware to API routes
        $middleware->api(append: [
            \App\Http\Middleware\AuditMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
