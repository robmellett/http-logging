<?php

namespace RobMellett\HttpLogging\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RobMellett\HttpLogging\Support\RemoveSecrets;

class RemoveSecretsTest extends TestCase
{
    /** @test */
    public function can_remove_secrets_from_array(): void
    {
        $headers = [
            'User-Agent' => [
                'GuzzleHttp/7',
            ],
            'Host' => [
                'jsonplaceholder.typicode.com',
            ],
            'Authorization' => [
                'Bearer ' . bin2hex(random_bytes(16)),
            ],
            'Content-Type' => [
                'application/json',
            ],
        ];

        $redactedHeaders = RemoveSecrets::from($headers);

        $this->assertSame([
            'User-Agent' => [
                'GuzzleHttp/7',
            ],
            'Host' => [
                'jsonplaceholder.typicode.com',
            ],
            'Authorization' => [
                '[--REDACTED--]',
            ],
            'Content-Type' => [
                'application/json',
            ],
        ], $redactedHeaders);
    }

    /** @test */
    public function can_replace_bearer_token(): void
    {
        $regex = '/^(?i)Bearer (.*)(?-i)/';
        $replacement = '[--REDACTED--]';

        $token = bin2hex(random_bytes(16));
        $bearerToken = 'Bearer '.$token;

        $result = preg_replace($regex, $replacement, $bearerToken);

        $this->assertEquals($replacement, $result);
    }
}
