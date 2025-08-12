<?php

namespace RobMellett\HttpLogging\Support;

use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

class SecureJsonFormatter extends JsonFormatter
{
    public function format(LogRecord $record): string
    {
        $result = parent::format($record);

        $redactedValue = config('http-logging.secure_json_formatter.redacted_value', '[--REDACTED--]');

        foreach ($this->secretValuesToRedact() as $secret) {
            $result = str($result)->replace($secret, $redactedValue);
        }

        foreach ($this->regexesToRedact() as $regex) {
            $result = preg_replace($regex, $redactedValue, $result);
        }

        return $result;
    }

    private function secretValuesToRedact(): array
    {
        return config('http-logging.secure_json_formatter.secrets', []);
    }

    private function regexesToRedact(): array
    {
        return config('http-logging.secure_json_formatter.regexes', []);
    }
}
