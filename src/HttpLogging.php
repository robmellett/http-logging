<?php

namespace RobMellett\HttpLogging;

use Psr\Http\Message\RequestInterface;

class HttpLogging
{
    // public function __invoke(callable $handler)
    // {
    //     return function (RequestInterface $request, array $options) use ($handler) {
    //         logger("This is from the request middleware class");
    //
    //         return $handler($request, $options);
    //     };
    // }

    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options)  use ($handler) {
            logger("This is from the request middleware class");

            return $handler($request, $options)->then(
                function ($response) {
                    logger("This is from the response middleware class");
                    return $response;
                }
            );
        };
    }
}
