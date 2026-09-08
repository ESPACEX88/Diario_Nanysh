<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VisitNotifier
{
    /**
     * Intento desde el servidor (puede fallar por rate-limit de ntfy en la IP de Render).
     * El aviso fiable va en el navegador del visitante (closed.blade.php).
     *
     * @return string Motivo/resultado para diagnóstico (view-source / header).
     */
    public function notifyClosedPageVisit(Request $request): string
    {
        if ($this->looksLikeBot($request)) {
            return 'skip:bot;browser-beacon';
        }

        $topic = $this->ntfyTopic();
        if ($topic === '') {
            return 'skip:no-topic';
        }

        // Si ntfy ya nos rate-limiteó desde Render, no martillar más (el JS del visitante avisa).
        if (Cache::get('ntfy_publish_cooldown')) {
            return 'skip:server-cooldown;browser-beacon';
        }

        $payload = [
            'ip' => $this->visitorIp($request),
            'path' => '/' . ltrim($request->path(), '/'),
            'user_agent' => substr((string) $request->userAgent(), 0, 180),
            'at' => now()->timezone('America/Guatemala')->toDateTimeString(),
        ];

        $ntfy = $this->notifyNtfy($topic, $payload);
        $this->notifyDiscord($payload);

        if (str_contains($ntfy, '429')) {
            Cache::put('ntfy_publish_cooldown', true, now()->addMinutes(45));

            return $ntfy.';browser-beacon';
        }

        return $ntfy.';browser-beacon';
    }

    public function forceTestPing(string $note = 'ping'): string
    {
        $topic = $this->ntfyTopic();
        if ($topic === '') {
            return 'skip:no-topic';
        }

        return $this->notifyNtfy($topic, [
            'ip' => 'test',
            'path' => '/visit-ping',
            'user_agent' => $note,
            'at' => now()->timezone('America/Guatemala')->toDateTimeString(),
        ]);
    }

    public function ntfyTopic(): string
    {
        return trim((string) (config('services.site_visit.ntfy_topic') ?: 'diario-nahysh-visitas-5660d0'));
    }

    private function visitorIp(Request $request): string
    {
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

    private function notifyNtfy(string $topic, array $payload): string
    {
        $title = 'Alguien visitó el Diario de Nahysh';
        $message = "Vieron el mensaje de despedida 😢\n"
            . "IP: {$payload['ip']}\n"
            . "Ruta: {$payload['path']}\n"
            . "Hora: {$payload['at']}\n"
            . "Navegador: {$payload['user_agent']}";

        $url = 'https://ntfy.sh/' . rawurlencode($topic);

        $streamResult = $this->postNtfyWithStream($url, $title, $message);
        if ($streamResult === 'ok') {
            return 'sent:stream';
        }

        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    'Title' => $title,
                    'Priority' => 'high',
                    'Tags' => 'sobbing_face,broken_heart',
                    'Content-Type' => 'text/plain',
                ])
                ->withBody($message, 'text/plain')
                ->post($url);

            if ($response->successful()) {
                return 'sent:http';
            }

            Log::error('ntfy HTTP error', [
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 200),
                'stream' => $streamResult,
            ]);

            return 'fail:http-'.$response->status().';stream-'.$streamResult;
        } catch (\Throwable $e) {
            Log::error('ntfy HTTP exception', [
                'error' => $e->getMessage(),
                'stream' => $streamResult,
            ]);

            return 'fail:http-ex;stream-'.$streamResult;
        }
    }

    private function postNtfyWithStream(string $url, string $title, string $message): string
    {
        try {
            $headers = implode("\r\n", [
                'Content-Type: text/plain; charset=utf-8',
                'Title: '.$title,
                'Priority: high',
                'Tags: sobbing_face,broken_heart',
            ]);

            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => $headers,
                    'content' => $message,
                    'timeout' => 8,
                    'ignore_errors' => true,
                ],
            ]);

            $body = @file_get_contents($url, false, $context);
            $statusLine = $http_response_header[0] ?? '';

            if (is_string($body) && str_contains($statusLine, '200')) {
                return 'ok';
            }

            return 'status:'.substr($statusLine !== '' ? $statusLine : 'none', 0, 40);
        } catch (\Throwable $e) {
            return 'ex:'.substr($e->getMessage(), 0, 40);
        }
    }

    private function notifyDiscord(array $payload): void
    {
        $webhook = trim((string) (config('services.site_visit.discord_webhook') ?: ''));
        if ($webhook === '') {
            return;
        }

        try {
            Http::timeout(8)->post($webhook, [
                'content' => "😢 Alguien visitó el Diario de Nahysh y vio el mensaje de despedida.\n"
                    . "**IP:** `{$payload['ip']}`\n"
                    . "**Ruta:** `{$payload['path']}`\n"
                    . "**Hora:** {$payload['at']}\n"
                    . "**Navegador:** {$payload['user_agent']}",
            ]);
        } catch (\Throwable $e) {
            Log::error('Discord visita falló', ['error' => $e->getMessage()]);
        }
    }

    private function looksLikeBot(Request $request): bool
    {
        $ua = strtolower((string) $request->userAgent());

        if ($ua === '') {
            return true;
        }

        foreach ([
            'googlebot',
            'bingbot',
            'yandexbot',
            'duckduckbot',
            'baiduspider',
            'facebookexternalhit',
            'slurp',
            'twitterbot',
            'linkedinbot',
            'semrush',
            'ahrefs',
            'gptbot',
            'claudebot',
            'bytespider',
            'wget/',
            'curl/',
            'python-requests',
            'headlesschrome',
        ] as $needle) {
            if (str_contains($ua, $needle)) {
                return true;
            }
        }

        return false;
    }
}
