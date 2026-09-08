<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SiteClosed
{
    /**
     * Muestra la página de cierre permanente en todas las rutas públicas de la app.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Dejar health/keep-alive para monitoreo; el resto muestra la despedida.
        if ($request->is('health', 'keep-alive')) {
            return $next($request);
        }

        return response()->view('closed', [], 410);
    }
}
