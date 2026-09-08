<?php

namespace Tests\Unit;

use App\Services\VisitNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VisitNotifierTest extends TestCase
{
    public function test_skips_bots(): void
    {
        config(['services.site_visit.ntfy_topic' => 'test-topic-xyz']);
        Http::fake();

        $request = Request::create('/', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (compatible; Googlebot/2.1)',
            'REMOTE_ADDR' => '10.0.0.2',
        ]);

        $status = app(VisitNotifier::class)->notifyClosedPageVisit($request);

        $this->assertSame('skip:bot;browser-beacon', $status);
        Http::assertNothingSent();
    }

    public function test_uses_hardcoded_topic_when_config_empty(): void
    {
        config(['services.site_visit.ntfy_topic' => '']);

        $this->assertSame(
            'diario-nahysh-visitas-5660d0',
            app(VisitNotifier::class)->ntfyTopic()
        );
    }

    public function test_skips_server_publish_during_cooldown(): void
    {
        config(['services.site_visit.ntfy_topic' => 'test-topic-xyz']);
        Cache::put('ntfy_publish_cooldown', true, 60);
        Http::fake();

        $request = Request::create('/', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 Chrome/120.0.0.0',
            'REMOTE_ADDR' => '10.0.0.3',
        ]);

        $status = app(VisitNotifier::class)->notifyClosedPageVisit($request);

        $this->assertSame('skip:server-cooldown;browser-beacon', $status);
        Http::assertNothingSent();
    }
}
