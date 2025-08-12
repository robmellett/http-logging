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
            '/Bearer\s\w+/',
        ],
    ],
];
