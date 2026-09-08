<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VisitNotifier
{
    /**
     * Notifica una visita a la página de cierre (ntfy y/o Discord), con rate limit.
     */
    public function notifyClosedPageVisit(Request $request): void
    {
        if ($this->looksLikeBot($request)) {
            return;
        }

        $ip = $request->ip() ?: 'unknown';
        $throttleKey = 'closed_visit_notify:' . sha1($ip);

        // Máx. 1 aviso por IP cada 30 minutos (evita spam)
        if (! Cache::add($throttleKey, true, now()->addMinutes(30))) {
            return;
        }

        $payload = [
            'ip' => $ip,
            'path' => '/' . ltrim($request->path(), '/'),
            'user_agent' => substr((string) $request->userAgent(), 0, 180),
            'at' => now()->timezone('America/Guatemala')->toDateTimeString(),
        ];

        $this->notifyNtfy($payload);
        $this->notifyDiscord($payload);
    }

    private function notifyNtfy(array $payload): void
    {
        $topic = trim((string) env('SITE_VISIT_NTFY_TOPIC', ''));
        if ($topic === '') {
            return;
        }

        $title = 'Alguien visitó el Diario de Nahysh';
        $message = "Vieron el mensaje de despedida 😢\n"
            . "IP: {$payload['ip']}\n"
            . "Ruta: {$payload['path']}\n"
            . "Hora: {$payload['at']}\n"
            . "Navegador: {$payload['user_agent']}";

        try {
            Http::timeout(4)
                ->withHeaders([
                    'Title' => $title,
                    'Priority' => 'default',
                    'Tags' => 'sobbing_face,broken_heart',
                ])
                ->withBody($message, 'text/plain')
                ->post('https://ntfy.sh/' . rawurlencode($topic));
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar aviso ntfy de visita', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function notifyDiscord(array $payload): void
    {
        $webhook = trim((string) env('SITE_VISIT_DISCORD_WEBHOOK', ''));
        if ($webhook === '') {
            return;
        }

        try {
            Http::timeout(4)->post($webhook, [
                'content' => "😢 Alguien visitó el Diario de Nahysh y vio el mensaje de despedida.\n"
                    . "**IP:** `{$payload['ip']}`\n"
                    . "**Ruta:** `{$payload['path']}`\n"
                    . "**Hora:** {$payload['at']}\n"
                    . "**Navegador:** {$payload['user_agent']}",
            ]);
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar aviso Discord de visita', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function looksLikeBot(Request $request): bool
    {
        $ua = strtolower((string) $request->userAgent());

        if ($ua === '') {
            return true;
        }

        foreach (['bot', 'spider', 'crawl', 'slurp', 'facebookexternalhit', 'preview', 'wget', 'curl', 'python-requests'] as $needle) {
            if (str_contains($ua, $needle)) {
                return true;
            }
        }

        return false;
    }
}
