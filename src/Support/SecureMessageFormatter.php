<?php

namespace RobMellett\HttpLogging\Support;

use GuzzleHttp\MessageFormatter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class SecureMessageFormatter extends MessageFormatter
{
    public function __construct(?string $template = self::CLF)
    {
        parent::__construct($template);
    }

    public function format(
        RequestInterface $request,
        ?ResponseInterface $response = null,
        ?Throwable $error = null
    ): string {
        return parent::format($request, $response, $error);
    }
}
