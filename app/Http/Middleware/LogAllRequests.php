<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAllRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log every request that reaches Laravel
        \Log::info('Request received', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'has_files' => !empty($request->allFiles()),
            'files_count' => count($request->allFiles()),
            'files_keys' => array_keys($request->allFiles()),
            'content_length' => $request->header('Content-Length'),
            'content_type' => $request->header('Content-Type'),
            'all_headers' => $request->headers->all(),
            'input_keys' => array_keys($request->all()),
            'php_upload_max' => ini_get('upload_max_filesize'),
            'php_post_max' => ini_get('post_max_size'),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
        ]);

        try {
            $response = $next($request);

            \Log::info('Response sent', [
                'method' => $request->method(),
                'path' => $request->path(),
                'status' => $response->getStatusCode(),
                'duration' => microtime(true) - LARAVEL_START,
            ]);

            return $response;
        } catch (\Exception $e) {
            \Log::error('Request failed with exception', [
                'method' => $request->method(),
                'path' => $request->path(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
