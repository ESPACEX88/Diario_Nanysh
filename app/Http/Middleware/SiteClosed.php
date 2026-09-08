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
        if ($request->is('health', 'keep-alive')) {
            return $next($request);
        }

        // Endpoint de prueba: confirma si Render puede hablar con ntfy.
        if ($request->is('visit-ping-5660d0')) {
            $result = app(VisitNotifier::class)->forceTestPing('manual-ping');

            return response("visit-ping: {$result}\n", 200, [
                'Content-Type' => 'text/plain; charset=utf-8',
                'Cache-Control' => 'no-store',
            ]);
        }

        $notifyStatus = 'skip:unrun';
        try {
            $notifyStatus = app(VisitNotifier::class)->notifyClosedPageVisit($request);
        } catch (\Throwable $e) {
            $notifyStatus = 'fail:ex';
            report($e);
        }

        return response()
            ->view('closed', ['notifyStatus' => $notifyStatus], 410)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('X-Visit-Notify', $notifyStatus);
    }
}
