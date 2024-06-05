<?php

namespace RobMellett\HttpLogging\Support;

class RemoveSecrets
{
    public static function redactedValue(): string
    {
        return '[--REDACTED--]';
    }

    public static function regexesToRedact(): array
    {
        return [
            '/^(?i)Bearer (.*)(?-i)/',
        ];
    }

    public static function from(array $values = [])
    {
        foreach ($values as $key => $value) {
            foreach (self::regexesToRedact() as $regex) {
                $values[$key] = [preg_replace($regex, self::redactedValue(), $value[0])];
            }
        }

        return $values;
    }
}
