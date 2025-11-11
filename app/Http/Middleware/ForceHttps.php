<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if we're in production and request is not secure
        $env = env('APP_ENV', 'local');
        if (!$request->secure() && $env === 'production') {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}
