<?php

namespace RobMellett\HttpLogging;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpLogging
{
    public function __invoke(callable $handler)
    {
        $uuid = app(Str::class)->uuid();

        return function (RequestInterface $request, array $options) use ($handler, $uuid) {
            $this->logRequest($uuid, $request);

            return $handler($request, $options)->then(
                function (ResponseInterface $response) use ($uuid) {
                    $this->logResponse($uuid, $response);

                    return $response;
                }
            );
        };
    }

    private function logRequest(string $uuid, RequestInterface $request): void
    {
        $channel = config('http-logging.channel');

        Log::channel($channel)->debug("Request {$uuid}", [
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
    }

    private function logResponse(string $uuid, ResponseInterface $response): void
    {
        $channel = config('http-logging.channel');

        Log::channel($channel)->debug("Response {$uuid}", [
            'response_id' => $uuid,
            'status_code' => $response->getStatusCode(),
            'headers' => $response->getHeaders(),
            'body' => json_decode($response->getBody(), true),
        ]);
    }
}
