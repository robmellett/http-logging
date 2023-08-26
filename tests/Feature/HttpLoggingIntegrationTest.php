<?php

namespace Feature;

use Composer\InstalledVersions;
use Illuminate\Support\Facades\Http;
use RobMellett\HttpLogging\HttpLogging;
use RobMellett\HttpLogging\Support\SecureJsonFormatter;
use RobMellett\HttpLogging\Tests\TestCase;

class HttpLoggingIntegrationTest extends TestCase
{
    /** @test */
    public function can_write_to_logs_with_secure_json_formatter()
    {
        if (InstalledVersions::getVersion('monolog/monolog') < '2.0.0') {
            $this->markTestSkipped('This test is only for Monolog > 2.0.0+');
        }

        $spy = $this->spy(SecureJsonFormatter::class);

        config()->set('logging.channels.http_logs', [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',

            'formatter' => SecureJsonFormatter::class,
        ]);

        Http::withMiddleware(new HttpLogging([
            'channel' => 'http_logs',
        ]))
            ->withToken('Ym9zY236Ym9zY28=')
            ->asJson()
            ->get('https://jsonplaceholder.typicode.com/posts?userId=1');

        $spy->shouldHaveReceived('format')->twice();
    }

    /** @test */
    public function can_write_to_logs_with_legacy_secure_json_formatter()
    {
        if (InstalledVersions::getVersion('monolog/monolog') > '2.0.0') {
            $this->markTestSkipped('This test is only for Monolog < 2.0.0+');
        }

        $spy = $this->spy(SecureJsonFormatter::class);

        config()->set('logging.channels.http_logs', [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',

            'formatter' => SecureJsonFormatter::class,
        ]);

        Http::withMiddleware(new HttpLogging([
            'channel' => 'http_logs',
        ]))
            ->withToken('Ym9zY236Ym9zY28=')
            ->asJson()
            ->get('https://jsonplaceholder.typicode.com/posts?userId=1');

        $spy->shouldHaveReceived('format')->twice();
    }
}
