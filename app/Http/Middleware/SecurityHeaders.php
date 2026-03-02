<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (method_exists($response, 'header')) {
            $response->header('X-Frame-Options', 'SAMEORIGIN');
            $response->header('X-XSS-Protection', '1; mode=block');
            $response->header('X-Content-Type-Options', 'nosniff');
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            // Simplified CSP to avoid breaking existing inline scripts while still providing some protection
            $response->header('Content-Security-Policy', "default-src 'self' 'unsafe-inline' 'unsafe-eval' https: http: data: blob: wss:;");
        }

        return $response;
    }
}
