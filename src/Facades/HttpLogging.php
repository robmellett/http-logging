<?php

namespace RobMellett\HttpLogging\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RobMellett\HttpLogging\HttpLogging
 */
class HttpLogging extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \RobMellett\HttpLogging\HttpLogging::class;
    }
}
