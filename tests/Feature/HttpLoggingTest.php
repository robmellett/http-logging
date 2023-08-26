<?php

namespace RobMellett\HttpLogging\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RobMellett\HttpLogging\HttpLogging;
use RobMellett\HttpLogging\Tests\TestCase;

class HttpLoggingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(Str::class, function (MockInterface $mock) {
            $mock->shouldReceive('uuid')->andReturn('0b65fca7-a768-4832-8401-da52aa2885a9');
        });

        config()->set('http-logging.secure_json_formatter.secrets', [
            'Ym9zY236Ym9zY28=',
        ]);
    }

    /** @test */
    public function can_fetch_requests_without_middleware()
    {
        $response = Http::get('https://jsonplaceholder.typicode.com/posts');

        $this->assertTrue($response->ok());
    }

    /** @test */
    public function can_fetch_requests_with_middleware()
    {
        $response = Http::withRequestMiddleware(
            function (RequestInterface $request) {
                logger('This is from the request middleware');

                return $request;
            }
        )->withResponseMiddleware(
            function (ResponseInterface $response) {
                logger('This is from the response middleware');

                return $response;
            }
        )
            ->get('https://jsonplaceholder.typicode.com/posts');

        $this->assertTrue($response->ok());
    }

    /** @test */
    public function can_fetch_requests_with_middleware_class()
    {
        Log::shouldReceive('channel->debug')
            ->once()
            ->withArgs(function ($message) {
                return $message == 'Request 0b65fca7-a768-4832-8401-da52aa2885a9';
            });

        Log::shouldReceive('channel->debug')
            ->once()
            ->withArgs(function ($message) {
                return $message == 'Response 0b65fca7-a768-4832-8401-da52aa2885a9';
            });

        $response = Http::withMiddleware(new HttpLogging())
            ->withToken('Ym9zY236Ym9zY28=')
            ->asJson()
            ->get('https://jsonplaceholder.typicode.com/posts?userId=1');

        $this->assertTrue($response->ok());
    }

    /** @test */
    public function can_set_custom_channel_name()
    {
        $config = [
            'channel' => 'custom_http_logs',
        ];

        $response = Http::withMiddleware(new HttpLogging($config))
            ->withToken('Ym9zY236Ym9zY28=')
            ->asJson()
            ->get('https://jsonplaceholder.typicode.com/posts?userId=1');

        $this->assertTrue($response->ok());
    }
}
