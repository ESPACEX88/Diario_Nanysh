<?php

namespace Tests\Unit;

use App\Services\VisitNotifier;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VisitNotifierTest extends TestCase
{
    public function test_sends_ntfy_for_browser_visit(): void
    {
        config(['services.site_visit.ntfy_topic' => 'test-topic-xyz']);

        Http::fake([
            'ntfy.sh/*' => Http::response('ok', 200),
        ]);

        $request = Request::create('/', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
            'REMOTE_ADDR' => '10.0.0.1',
            'HTTP_CF_CONNECTING_IP' => '203.0.113.50',
        ]);

        $status = app(VisitNotifier::class)->notifyClosedPageVisit($request);

        $this->assertTrue(str_starts_with($status, 'sent:') || str_starts_with($status, 'fail:'));
        // Con Http::fake, el stream real puede fallar; el cliente HTTP debe enviar.
        Http::assertSent(function (HttpRequest $httpRequest) {
            return str_contains($httpRequest->url(), 'ntfy.sh/test-topic-xyz')
                && str_contains($httpRequest->body(), '203.0.113.50');
        });
    }

    public function test_skips_bots(): void
    {
        config(['services.site_visit.ntfy_topic' => 'test-topic-xyz']);
        Http::fake();

        $request = Request::create('/', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (compatible; Googlebot/2.1)',
            'REMOTE_ADDR' => '10.0.0.2',
        ]);

        $status = app(VisitNotifier::class)->notifyClosedPageVisit($request);

        $this->assertSame('skip:bot', $status);
        Http::assertNothingSent();
    }

    public function test_uses_hardcoded_topic_when_config_empty(): void
    {
        config(['services.site_visit.ntfy_topic' => '']);
        Http::fake([
            'ntfy.sh/*' => Http::response('ok', 200),
        ]);

        $request = Request::create('/', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 Chrome/120.0.0.0',
            'REMOTE_ADDR' => '10.0.0.3',
        ]);

        app(VisitNotifier::class)->notifyClosedPageVisit($request);

        Http::assertSent(function (HttpRequest $httpRequest) {
            return str_contains($httpRequest->url(), 'ntfy.sh/diario-nahysh-visitas-5660d0');
        });
    }
}
