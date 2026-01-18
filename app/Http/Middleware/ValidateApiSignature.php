<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Use Laravel's signed URL validation
            if (!$request->hasValidSignature()) {
                // Log the failed signature verification for debugging
                Log::warning('Invalid signature for API request', [
                    'url' => $request->fullUrl(),
                    'app_url' => config('app.url'),
                    'request_url' => $request->url(),
                    'signature' => $request->query('signature'),
                    'expires' => $request->query('expires'),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
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

            return $next($request);
        } catch (InvalidSignatureException $e) {
            // Log the exception
            Log::warning('InvalidSignatureException caught', [
                'url' => $request->fullUrl(),
                'app_url' => config('app.url'),
                'message' => $e->getMessage(),
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
    }
}
