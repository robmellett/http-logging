<?php

namespace RobMellett\HttpLogging\Tests\Unit;

use Monolog\Level;
use Monolog\LogRecord;
use RobMellett\HttpLogging\Support\SecureJsonFormatter;
use RobMellett\HttpLogging\Tests\TestCase;

class SecureJsonFormatterTest extends TestCase
{
    /** @test */
    public function can_remove_secret_values_from_logs()
    {
        config()->set('http-logging.secure_json_formatter.secrets', [
            'Ym9zY236Ym9zY28=',
        ]);

        $formatter = new SecureJsonFormatter();

        $result = $formatter->format(
            $this->fakeLogRecord(
                $this->fakeLogContext()
            )
        );

        $this->assertStringNotContainsString('Ym9zY236Ym9zY28=', $result);

        $this->assertStringContainsString('[--REDACTED--]', $result);
    }

    /** @test */
    public function can_remove_regex_values_from_logs()
    {
        config()->set('http-logging.secure_json_formatter.regexes', [
            '/Bearer\s\w+/',
        ]);

        $formatter = new SecureJsonFormatter();

        $result = $formatter->format(
            $this->fakeLogRecord(
                $this->fakeLogContext()
            )
        );

        $this->assertStringNotContainsString('Bearer Ym9zY236Ym9zY28=', $result);

        $this->assertStringContainsString('[--REDACTED--]', $result);
    }

    private function fakeLogContext(): array
    {
        return [
            'request_id' => '0b65fca7-a768-4832-8401-da52aa2885a9',
            'method' => 'GET',
            'uri' => [
                'scheme' => 'https',
                'host' => 'jsonplaceholder.typicode.com',
                'path' => '/posts',
                'query' => 'userId=1',
            ],
            'headers' => [
                'User-Agent' => [
                    'GuzzleHttp/7',
                ],
                'Host' => [
                    'jsonplaceholder.typicode.com',
                ],
                'Content-Type' => [
                    'application/json',
                ],
                'Authorization' => [
                    'Bearer Ym9zY236Ym9zY28=',
                ],
            ],
        ];
    }

    private function fakeLogRecord(array $context = []): LogRecord
    {
        return new LogRecord(
            now()->toDateTimeImmutable(),
            'testing',
            Level::Debug,
            'Response 0b65fca7-a768-4832-8401-da52aa2885a9',
            $context
        );
    }
}
