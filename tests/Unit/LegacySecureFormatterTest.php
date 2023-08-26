<?php

namespace RobMellett\HttpLogging\Tests\Unit;

use Composer\InstalledVersions;
use RobMellett\HttpLogging\Support\LegacySecureJsonFormatter;
use RobMellett\HttpLogging\Tests\TestCase;

class LegacySecureJsonFormatterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (InstalledVersions::getVersion('monolog/monolog') < '2.0.0') {
            $this->markTestSkipped('This test is only for Monolog < 2.0.0+');
        }
    }

    /** @test */
    public function can_remove_secret_values_from_logs()
    {
        config()->set('http-logging.secure_json_formatter.secrets', [
            'Ym9zY236Ym9zY28=',
        ]);

        $formatter = new LegacySecureJsonFormatter();

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

        $formatter = new LegacySecureJsonFormatter();

        $result = $formatter->format(
            $this->fakeLogRecord(
                $this->fakeLogContext()
            )
        );

        $this->assertStringNotContainsString('Bearer Ym9zY236Ym9zY28=', $result);

        $this->assertStringContainsString('[--REDACTED--]', $result);
    }

    /** @test */
    public function can_extract_secrets_from_services_config()
    {
        config()->set('services', [
            'google' => [
                'client_id' => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET', 'Ym9zY236Ym9zY28='),
                'redirect' => env('GOOGLE_REDIRECT', '/nova/google/callback'),
            ],

            'mailgun' => [
                'domain' => env('MAILGUN_DOMAIN'),
                'secret' => env('MAILGUN_SECRET', '+0*RU{ULbv5svWK'),
                'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
            ],
        ]);

        $formatter = new LegacySecureJsonFormatter();

        $result = $formatter->format(
            $this->fakeLogRecord(
                $this->fakeLogContext()
            )
        );

        $this->assertStringNotContainsString('Ym9zY236Ym9zY28=', $result);

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

    private function fakeLogRecord(array $context = []): array
    {
        // return new LogRecord(
        //     now()->toDateTimeImmutable(),
        //     'testing',
        //     Level::Debug,
        //     'Response 0b65fca7-a768-4832-8401-da52aa2885a9',
        //     $context
        // );

        return [
            now()->toDateTimeImmutable(),
            'testing',
            100,
            'Response 0b65fca7-a768-4832-8401-da52aa2885a9',
            $context,
        ];
    }
}
