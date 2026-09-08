<?php

namespace Tests\Unit;

use App\Services\VisitNotifier;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VisitNotifierTest extends TestCase
{
    public function test_sends_ntfy_for_browser_visit(): void
    {
        config(['services.site_visit.ntfy_topic' => 'test-topic-xyz']);
        config(['services.site_visit.throttle_minutes' => 15]);

        Cache::flush();
        Http::fake([
            'ntfy.sh/*' => Http::response('ok', 200),
        ]);

        $request = Request::create('/', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
            'REMOTE_ADDR' => '10.0.0.1',
            'HTTP_CF_CONNECTING_IP' => '203.0.113.50',
        ]);

        app(VisitNotifier::class)->notifyClosedPageVisit($request);

        Http::assertSent(function (HttpRequest $httpRequest) {
            return str_contains($httpRequest->url(), 'ntfy.sh/test-topic-xyz')
                && str_contains($httpRequest->body(), '203.0.113.50');
        });
    }

    public function test_skips_bots(): void
    {
        config(['services.site_visit.ntfy_topic' => 'test-topic-xyz']);
        Cache::flush();
        Http::fake();

        $request = Request::create('/', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Googlebot/2.1',
            'REMOTE_ADDR' => '10.0.0.2',
        ]);

        app(VisitNotifier::class)->notifyClosedPageVisit($request);

        Http::assertNothingSent();
    }

    public function test_throttles_same_ip(): void
    {
        config(['services.site_visit.ntfy_topic' => 'test-topic-xyz']);
        Cache::flush();
        Http::fake([
            'ntfy.sh/*' => Http::response('ok', 200),
        ]);

        $make = fn () => Request::create('/', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 Chrome/120.0.0.0',
            'REMOTE_ADDR' => '10.0.0.3',
            'HTTP_CF_CONNECTING_IP' => '198.51.100.9',
        ]);

        $notifier = app(VisitNotifier::class);
        $notifier->notifyClosedPageVisit($make());
        $notifier->notifyClosedPageVisit($make());

        Http::assertSentCount(1);
    }
}
