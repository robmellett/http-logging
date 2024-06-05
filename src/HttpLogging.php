<?php

namespace RobMellett\HttpLogging;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RobMellett\HttpLogging\Support\RemoveSecrets;

class HttpLogging
{
    /**
     * Laravel Log Channel to send logs to.
     */
    protected readonly string $channel;

    public function __construct(
        private readonly array $config = []
    ) {
        $this->channel = $config['channel'] ?? config('http-logging.channel');
    }

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
        Log::channel($this->channel)->debug("Request $uuid", [
            'request_id' => $uuid,
            'method' => $request->getMethod(),
            'uri' => [
                'scheme' => $request->getUri()->getScheme(),
                'host' => $request->getUri()->getHost(),
                'path' => $request->getUri()->getPath(),
                'query' => $request->getUri()->getQuery(),
            ],
            'headers' => $this->replaceSensitiveValues(
                $request->getHeaders()
            ),
            'body' => json_decode($request->getBody(), true),
        ]);
    }

    private function logResponse(string $uuid, ResponseInterface $response): void
    {
        Log::channel($this->channel)->debug("Response $uuid", [
            'response_id' => $uuid,
            'status_code' => $response->getStatusCode(),
            'headers' => $response->getHeaders(),
            'body' => json_decode($response->getBody(), true),
        ]);
    }

    private function replaceSensitiveValues(array $values = []): array
    {
        return RemoveSecrets::from($values);
    }
}
