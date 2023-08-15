<?php

namespace RobMellett\HttpLogging;

use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpLogging
{
    public function __invoke(callable $handler)
    {
        $uuid = uniqid();

        return function (RequestInterface $request, array $options) use ($handler, $uuid) {
            Log::debug("Request {$uuid}", [
                'request_id' => $uuid,
                'method' => $request->getMethod(),
                'uri' => [
                    'scheme' => $request->getUri()->getScheme(),
                    'host' => $request->getUri()->getHost(),
                    'path' => $request->getUri()->getPath(),
                    'query' => $request->getUri()->getQuery(),
                ],
                'headers' => $request->getHeaders(),
            ]);

            return $handler($request, $options)->then(
                function (ResponseInterface $response) use ($uuid) {
                    Log::debug("Response {$uuid}", [
                        'response_id' => $uuid,
                        'status_code' => $response->getStatusCode(),
                        'headers' => $response->getHeaders(),
                        'body' => json_decode($response->getBody(), true),
                    ]);

                    return $response;
                }
            );
        };
    }
}
