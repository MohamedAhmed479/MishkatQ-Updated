<?php

namespace App\Http\Middleware;

use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Handle an incoming request and log it
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        try {
            $response = $next($request);
            
            // Log successful request
            $this->logRequest($request, $response, 'success', $startTime);
            
            return $response;
            
        } catch (\Throwable $e) {
            // Log failed request
            $this->logRequest($request, null, 'failed', $startTime);
            
            // Re-throw the exception
            throw $e;
        }
    }

    /**
     * Log the request details
     */
    private function logRequest(Request $request, ?Response $response, string $status, float $startTime): void
    {
        // Skip logging for certain routes to avoid noise
        if ($this->shouldSkipLogging($request)) {
            return;
        }

        $executionTime = microtime(true) - $startTime;
        $action = $this->getActionFromRequest($request);
        $severity = $this->getSeverityFromRequest($request, $response, $status);

        $this->auditService->log(
            action: $action,
            description: $this->getDescription($request, $response),
            severity: $severity,
            category: $this->getCategoryFromRequest($request),
            isSensitive: $this->isSensitiveRequest($request),
            metadata: [
                'execution_time_ms' => round($executionTime * 1000, 2),
                'response_status' => $response?->getStatusCode(),
                'response_size' => $response?->headers->get('Content-Length'),
                'memory_usage' => memory_get_peak_usage(true),
                'route_name' => $request->route()?->getName(),
                'route_parameters' => $request->route()?->parameters(),
            ],
            status: $status
        );
    }

    /**
     * Determine if we should skip logging for this request
     */
    private function shouldSkipLogging(Request $request): bool
    {
        $skipPaths = [
            'health',
            'ping',
            'metrics',
            '_debugbar',
            'telescope',
            'horizon',
        ];

        $path = $request->path();
        
        foreach ($skipPaths as $skipPath) {
            if (str_starts_with($path, $skipPath)) {
                return true;
            }
        }

        // Skip OPTIONS requests
        if ($request->method() === 'OPTIONS') {
            return true;
        }

        return false;
    }

    /**
     * Get action name from request
     */
    private function getActionFromRequest(Request $request): string
    {
        $route = $request->route();
        
        if ($route && $route->getAction('uses')) {
            $action = $route->getAction('uses');
            if (is_string($action) && str_contains($action, '@')) {
                [, $methodName] = explode('@', $action);
                return $methodName;
            }
        }

        $method = $request->method();
        $path = $request->path();

        // Special cases for auth routes
        if (str_contains($path, 'login')) return 'login';
        if (str_contains($path, 'logout')) return 'logout';
        if (str_contains($path, 'register')) return 'register';
        if (str_contains($path, 'reset')) return 'password_reset';
        if (str_contains($path, 'verify')) return 'email_verify';

        return match($method) {
            'GET' => 'view',
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'access'
        };
    }

    /**
     * Get description for the request
     */
    private function getDescription(Request $request, ?Response $response): string
    {
        $method = $request->method();
        $path = $request->path();
        $statusCode = $response?->getStatusCode() ?? 'Unknown';

        return "{$method} {$path} - Status: {$statusCode}";
    }

    /**
     * Determine severity based on request and response
     */
    private function getSeverityFromRequest(Request $request, ?Response $response, string $status): string
    {
        if ($status === 'failed') {
            return 'high';
        }

        $statusCode = $response?->getStatusCode();

        if ($statusCode >= 500) {
            return 'critical';
        }

        if ($statusCode >= 400) {
            return 'medium';
        }

        if ($this->isSensitiveRequest($request)) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Get category based on request path
     */
    private function getCategoryFromRequest(Request $request): string
    {
        $path = $request->path();

        if (str_contains($path, 'auth') || str_contains($path, 'login') || str_contains($path, 'register')) {
            return 'auth';
        }

        if (str_contains($path, 'admin')) {
            return 'admin';
        }

        if (str_contains($path, 'api')) {
            return 'api';
        }

        return 'web';
    }

    /**
     * Determine if request contains sensitive data
     */
    private function isSensitiveRequest(Request $request): bool
    {
        $sensitivePaths = [
            'auth',
            'login',
            'register',
            'password',
            'reset',
            'admin',
            'user',
            'profile',
        ];

        $path = $request->path();

        foreach ($sensitivePaths as $sensitivePath) {
            if (str_contains($path, $sensitivePath)) {
                return true;
            }
        }

        // Check for sensitive parameters
        $sensitiveParams = ['password', 'token', 'secret', 'key'];
        foreach ($sensitiveParams as $param) {
            if ($request->has($param)) {
                return true;
            }
        }

        return false;
    }
}
