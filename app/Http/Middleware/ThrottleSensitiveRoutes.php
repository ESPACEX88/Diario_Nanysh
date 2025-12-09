<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleSensitiveRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'sensitive:' . $request->ip();
        
        // Limitar a 10 requests por minuto para rutas sensibles
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            
            return back()->withErrors([
                'rate_limit' => "Demasiados intentos. Por favor, espera {$seconds} segundos."
            ])->withInput();
        }
        
        RateLimiter::hit($key, 60); // 1 minuto
        
        return $next($request);
    }
}

