<?php

namespace Feature;

use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use RobMellett\HttpLogging\HttpLogging;
use RobMellett\HttpLogging\Support\SecureJsonFormatter;
use RobMellett\HttpLogging\Tests\TestCase;

class HttpLoggingIntegrationTest extends TestCase
{
    #[Test]
    public function can_write_to_logs_with_secure_json_formatter()
    {
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
