<?php

namespace RobMellett\HttpLogging\Support;

use Illuminate\Support\Str;
use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

class SecureJsonFormatter extends JsonFormatter
{
    public function format(array|LogRecord $record): string
    {
        $result = parent::format($record);

        $redactedValue = config('http-logging.secure_json_formatter.redacted_value', '[--REDACTED--]');

        foreach ($this->redactSecretsFromServices() as $secret) {
            $result = str($result)->replace($secret, $redactedValue);
        }

        foreach ($this->secretValuesToRedact() as $secret) {
            $result = str($result)->replace($secret, $redactedValue);
        }

        foreach ($this->regexesToRedact() as $regex) {
            $result = preg_replace($regex, $redactedValue, $result);
        }

        return $result;
    }

    private function redactSecretsFromServices(): array
    {
        if (! config('http-logging.secure_json_formatter.extract_service_secrets')) {
            return [];
        }

        $services = config('services', []);

        $flattenedServices = array_reduce($services, 'array_merge', []);

        return collect($flattenedServices)
            ->filter(function ($value, $key) {
                return Str::of($key)->lower()->contains(['api', 'key', 'secret', 'password', 'hash', 'token']);
            })
            ->values()
            ->filter()
            ->toArray();
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
