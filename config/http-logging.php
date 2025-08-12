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
