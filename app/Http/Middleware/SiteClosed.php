<?php

namespace App\Http\Middleware;

use App\Services\VisitNotifier;
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

        try {
            app(VisitNotifier::class)->notifyClosedPageVisit($request);
        } catch (\Throwable $e) {
            // Nunca tumbar la página de despedida por un fallo de notificación
            report($e);
        }

        return response()->view('closed', [], 410);
    }
}
