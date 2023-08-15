<?php

namespace RobMellett\HttpLogging\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RobMellett\HttpLogging\HttpLogging;
use RobMellett\HttpLogging\Tests\TestCase;

class HttpLoggingTest extends TestCase
{
    /** @test */
    public function can_fetch_requests_without_middlware()
    {
        $response = Http::get('https://jsonplaceholder.typicode.com/posts');

        $this->assertTrue($response->ok());
    }

    /** @test */
    public function can_fetch_requests_with_middlware()
    {
        $response = Http::withRequestMiddleware(
            function (RequestInterface $request) {
                logger("This is from the request middleware");
                return $request;
            }
        )->withResponseMiddleware(
            function (ResponseInterface $response) {
                logger("This is from the response middleware");
                return $response;
            }
        )
            ->get('https://jsonplaceholder.typicode.com/posts');

        $this->assertTrue($response->ok());
    }

    /** @test */
    public function can_fetch_requests_with_middlware_with_class()
    {
        Http::buildHandlerStack()->push(new HttpLogging());

        $response = Http::withMiddleware(new HttpLogging())
            ->get('https://jsonplaceholder.typicode.com/posts');

        $this->assertTrue($response->ok());
    }
}
