<?php

namespace RobMellett\HttpLogging\Support;

use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

class SecureMessageFormatter extends JsonFormatter
{
    public function format(LogRecord $record): string
    {
        $result = parent::format($record);

        ray($result)->color('red');

        return $result;
    }
}
