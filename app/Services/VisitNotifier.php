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

        $ip = $this->visitorIp($request);
        $throttleMinutes = max(1, (int) config('services.site_visit.throttle_minutes', 15));
        $throttleKey = 'closed_visit_notify:' . sha1($ip);

        // Si el caché falla, igual intentamos notificar (mejor un duplicado que silencio).
        try {
            if (! Cache::add($throttleKey, true, now()->addMinutes($throttleMinutes))) {
                return;
            }
        } catch (\Throwable $e) {
            Log::warning('Throttle de visitas falló; se notifica igual', [
                'error' => $e->getMessage(),
            ]);
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

    private function visitorIp(Request $request): string
    {
        // Respaldo por si TrustProxies aún no corrió: X-Forwarded-For / CF-Connecting-IP
        $forwarded = $request->headers->get('CF-Connecting-IP')
            ?: $request->headers->get('X-Real-IP');

        if (is_string($forwarded) && $forwarded !== '') {
            return trim(explode(',', $forwarded)[0]);
        }

        $xff = $request->headers->get('X-Forwarded-For');
        if (is_string($xff) && $xff !== '') {
            return trim(explode(',', $xff)[0]);
        }

        return $request->ip() ?: 'unknown';
    }

    private function notifyNtfy(array $payload): void
    {
        $topic = trim((string) config('services.site_visit.ntfy_topic', ''));
        if ($topic === '') {
            Log::info('SITE_VISIT_NTFY_TOPIC no configurado; se omite ntfy');

            return;
        }

        $title = 'Alguien visitó el Diario de Nahysh';
        $message = "Vieron el mensaje de despedida 😢\n"
            . "IP: {$payload['ip']}\n"
            . "Ruta: {$payload['path']}\n"
            . "Hora: {$payload['at']}\n"
            . "Navegador: {$payload['user_agent']}";

        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    'Title' => $title,
                    'Priority' => 'default',
                    'Tags' => 'sobbing_face,broken_heart',
                ])
                ->withBody($message, 'text/plain')
                ->post('https://ntfy.sh/' . rawurlencode($topic));

            if (! $response->successful()) {
                Log::warning('ntfy respondió error al avisar visita', [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 200),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar aviso ntfy de visita', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function notifyDiscord(array $payload): void
    {
        $webhook = trim((string) config('services.site_visit.discord_webhook', ''));
        if ($webhook === '') {
            return;
        }

        try {
            $response = Http::timeout(8)->post($webhook, [
                'content' => "😢 Alguien visitó el Diario de Nahysh y vio el mensaje de despedida.\n"
                    . "**IP:** `{$payload['ip']}`\n"
                    . "**Ruta:** `{$payload['path']}`\n"
                    . "**Hora:** {$payload['at']}\n"
                    . "**Navegador:** {$payload['user_agent']}",
            ]);

            if (! $response->successful()) {
                Log::warning('Discord webhook respondió error al avisar visita', [
                    'status' => $response->status(),
                ]);
            }
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

        foreach ([
            'bot',
            'spider',
            'crawl',
            'slurp',
            'facebookexternalhit',
            'preview',
            'wget',
            'curl',
            'python-requests',
            'httpclient',
            'headless',
        ] as $needle) {
            if (str_contains($ua, $needle)) {
                return true;
            }
        }

        return false;
    }
}
