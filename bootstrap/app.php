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
            ->timezone('Africa/Cairo')
            ->at('00:00');

        // Update weekly leaderboards at midnight on Sundays
        $schedule->command('leaderboards:update --type=weekly')
            ->weekly()
            ->sundays()
            ->timezone('Africa/Cairo')
            ->at('00:00');

        // Update monthly leaderboards at midnight on the first day of each month
        $schedule->command('leaderboards:update --type=monthly')
            ->monthly()
            ->timezone('Africa/Cairo')
            ->at('00:00');

        // Update yearly leaderboards at midnight on January 1st
        $schedule->command('leaderboards:update --type=yearly')
            ->yearly()
            ->timezone('Africa/Cairo')
            ->at('00:00');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        // Exclude resend-verification from CSRF protection as a fallback
        // (useForm should handle CSRF automatically, but this ensures it works)
        $middleware->validateCsrfTokens(except: [
            'resend-verification',
        ]);
        
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'verified.user' => \App\Http\Middleware\EnsureUserEmailIsVerified::class,
            'audit' => \App\Http\Middleware\AuditMiddleware::class,
        ]);
        // Add Inertia middleware to web routes
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);
        // Apply audit middleware to API routes
        $middleware->api(append: [
            \App\Http\Middleware\AuditMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle InvalidSignatureException for API routes
        $exceptions->render(function (\Illuminate\Routing\Exceptions\InvalidSignatureException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                // Log the failed signature verification for debugging
                \Illuminate\Support\Facades\Log::warning('Invalid signature for email verification', [
                    'url' => $request->fullUrl(),
                    'app_url' => config('app.url'),
                    'request_url' => $request->url(),
                    'signature' => $request->query('signature'),
                    'expires' => $request->query('expires'),
                    'ip' => $request->ip(),
                ]);
                
                return response()->json([
                    'status' => false,
                    'message' => 'رابط التحقق غير صالح أو منتهي الصلاحية',
                    'data' => [
                        'error' => 'Invalid signature',
                        'hint' => 'تأكد من أن الرابط لم يتم تعديله وأنه لم ينتهِ صلاحيته'
                    ]
                ], 403);
            }
        });
    })->create();
