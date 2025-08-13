<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponse::unauthorized('غير مصرح بالوصول');
        }

        if (! $user->hasVerifiedEmail()) {
            return ApiResponse::error('يرجى تفعيل البريد الإلكتروني أولاً', 403);
        }

        return $next($request);
    }
}


