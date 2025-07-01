<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class FileUploadRateLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasFile('pdfFile')) {
            return $next($request);
        }

        $key = 'file_upload:' . $request->user()->id;

        // Allow 3 file uploads per hour per user
        $executed = RateLimiter::attempt(
            $key,
            $maxAttempts = 3,
            function () {
                // This closure is executed if the rate limit is not exceeded
            },
            $decayMinutes = 60
        );

        if (!$executed) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'error' => 'Too many file upload attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.'
            ], 429);
        }

        return $next($request);
    }
}
