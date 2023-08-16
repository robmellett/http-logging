<?php

namespace RobMellett\HttpLogging\Support;

use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

class SecureJsonFormatter extends JsonFormatter
{
    public function format(LogRecord $record): string
    {
        $result = parent::format($record);

        return $result;
    }
}
