# Laravel HTTP Logging Middleware

[![Latest Version on Packagist](https://img.shields.io/packagist/v/robmellett/http-logging.svg?style=flat-square)](https://packagist.org/packages/robmellett/http-logging)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/robmellett/http-logging/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/robmellett/http-logging/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/robmellett/http-logging/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/robmellett/http-logging/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/robmellett/http-logging.svg?style=flat-square)](https://packagist.org/packages/robmellett/http-logging)

A small lightweight package to log all Guzzle http request and responses.

## Installation

You can install the package via composer:

```bash
composer require robmellett/http-logging
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="http-logging-config"
```

This is the contents of the published config file:

```php
<?php

// config for RobMellett/HttpLogging
return [
    /*
     *  The Laravel Log Channel to send logs to.
     */
    'channel' => 'http_logs',

    /*
     * Customize how the Secure Json Formatter redacts secrets.
     */
    'secure_json_formatter' => [

        /*
         * Secret Values will be replaced with this value.
         */
        'redacted_value' => '[--REDACTED--]',

        /*
         * By default, we will attempt to look for secrets in the Laravel 'config/services.php'.
         *
         * Any values that contain the following words will be redacted:
         * "key", "secret", "password", "hash", "token"
         */
        'extract_service_secrets' => true,

        /*
         * Specific values to redact from the logs.
         */
        'secrets' => [
            // e.g
            // env('API_SECRET'),
        ],

        /*
         * Regular expressions to redact from the logs.
         */
        'regexes' => [
            // e.g
            // '/Bearer\s\w+/',
        ],
    ],
];
```

## Usage

You can add this middleware to the Laravel Http Client or Guzzle in the following way.

```php
use RobMellett\HttpLogging\HttpLogging;

Http::withMiddleware(new HttpLogging())
    ->asJson()
    ->get('https://jsonplaceholder.typicode.com/posts');
```

You can configure the Log Formatter by adding the following to the Laravel logging config file.

```php
// config/logging.php

'channels' => [
    // ...Previous config
    
    'http_logs' => [
        'driver' => 'single',
        'path' => storage_path('logs/laravel.log'),
        'level' => 'debug',

        // This will remove sensitive values such as "key", "secret", "hash", "token" from the logs
        'formatter' => RobMellett\HttpLogging\Support\SecureJsonFormatter::class
        
        // Or if you would prefer to send sensitive data to the logs
        //'formatter' => Monolog\Formatter\JsonFormatter::class,
    ],
]
```

Which will send the following http request/response info to the logs.

A Http Request

```json
{
  "message": "Request 0b65fca7-a768-4832-8401-da52aa2885a9",
  "context": {
    "request_id": "0b65fca7-a768-4832-8401-da52aa2885a9",
    "method": "GET",
    "uri": {
      "scheme": "https",
      "host": "jsonplaceholder.typicode.com",
      "path": "/posts",
      "query": "userId=1"
    },
    "headers": {
      "User-Agent": [
        "GuzzleHttp/7"
      ],
      "Host": [
        "jsonplaceholder.typicode.com"
      ],
      "Authorization": [
        "Bearer [--REDACTED--]"
      ],
      "Content-Type": [
        "application/json"
      ]
    }
  },
  "level": 100,
  "level_name": "DEBUG",
  "channel": "testing",
  "datetime": "2023-08-16T10:13:41.356030+00:00",
  "extra": {}
}
```

A Http Response

```json
{
  "message": "Response 0b65fca7-a768-4832-8401-da52aa2885a9",
  "context": {
    "response_id": "0b65fca7-a768-4832-8401-da52aa2885a9",
    "status_code": 200,
    "headers": {
      "Date": ["Wed, 16 Aug 2023 00:41:13 GMT"],
      "Content-Type": ["application/json; charset=utf-8"],
      "Transfer-Encoding": ["chunked"],
      "Connection": ["keep-alive"],
      "X-Powered-By": ["Express"],
      "X-Ratelimit-Limit": ["1000"],
      "X-Ratelimit-Remaining": ["999"],
      "X-Ratelimit-Reset": ["1691921646"],
      "Vary": ["Origin, Accept-Encoding"],
      "Access-Control-Allow-Credentials": ["true"],
      "Cache-Control": ["max-age=43200"],
      "Pragma": ["no-cache"],
      "Expires": ["-1"],
      "X-Content-Type-Options": ["nosniff"],
      "Etag": ["W/\"aa6-j2NSH739l9uq40OywFMn7Y0C/iY\""],
      "Via": ["1.1 vegur"],
      "CF-Cache-Status": ["HIT"],
      "Age": ["18801"],
      "Report-To": [
        "{\"endpoints\":[{\"url\":\"https:\\/\\/a.nel.cloudflare.com\\/report\\/v3?s=gRUkX3pH6GRGwHCE%2BqKF%2ByJRGZs9MkqF8BqXa0nlmYSVzgrcmQkIGfD9lC8IlSXKvSiiyZHxrzgLy8pcOCSMRv5xFh2LyXWOkXDEtFcSr1FINwhjxRwYTZQZIaFzTulP4lUnjlrXdERp57lEXT3C\"}],\"group\":\"cf-nel\",\"max_age\":604800}"
      ],
      "NEL": [
        "{\"success_fraction\":0,\"report_to\":\"cf-nel\",\"max_age\":604800}"
      ],
      "Server": ["cloudflare"],
      "CF-RAY": ["7f75a160dc9991c0-SIN"],
      "alt-svc": ["h3=\":443\"; ma=86400"]
    },
    "body": [
      {
        "userId": 1,
        "id": 1,
        "title": "sunt aut facere repellat provident occaecati excepturi optio reprehenderit",
        "body": "quia et suscipit\nsuscipit recusandae consequuntur expedita et cum\nreprehenderit molestiae ut ut quas totam\nnostrum rerum est autem sunt rem eveniet architecto"
      }
    ]
  },
  "level": 100,
  "level_name": "DEBUG",
  "channel": "testing",
  "datetime": "2023-08-16T00:41:13.037161+00:00",
  "extra": {}
}
```

## Secure Json Formatter

By default, we will attempt to flatten the Laravel `config/services.php` array and find any keys that contain the words `key`, `secret`, `password`, `hash`, `token` and exclude them from the logs.

You can disable this functionality by setting the `secure_json_formatter.extract_service_secrets` config option to `false`.

```php
<?php

return [
    /*
     * Customize how the Secure Json Formatter redacts secrets.
     */
    'secure_json_formatter' => [
        // ...previous values
    
        'extract_service_secrets' => false,
    ],
];
```

You can optionally add your own keys to the `secure_json_formatter.secrets` config option.

```php
<?php

return [
    /*
     * Customize how the Secure Json Formatter redacts secrets.
     */
    'secure_json_formatter' => [
        // ...previous values
    
        /*
         * Specific values to redact from the logs.
         */
        'secrets' => [
            env('SERVICE_API_SECRET'),
        ],
    ],
];
```

You can optionally add your own regular expressions to the `secure_json_formatter.regexes` config option.

```php
<?php

return [
    /*
     * Customize how the Secure Json Formatter redacts secrets.
     */
    'secure_json_formatter' => [
        // ...previous values
    
        /*
         * Regular expressions to redact from the logs.
         */
        'regexes' => [
            // e.g
            '/Bearer\s\w+/',
        ],
    ],
];
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Rob Mellett](https://github.com/robmellett)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
