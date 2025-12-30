<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Quote Request Expiry Settings
    |--------------------------------------------------------------------------
    |
    | Default expiry time for quote requests in minutes. Can be overridden
    | at the trader level or per individual quote request.
    |
    */
    'request' => [
        'default_expiry_minutes' => env('QUOTE_REQUEST_EXPIRY_MINUTES', 1440), // 24 hours
    ],

    /*
    |--------------------------------------------------------------------------
    | Quote Response Expiry Settings
    |--------------------------------------------------------------------------
    |
    | Default expiry time for quote responses in minutes. Can be overridden
    | at the trader level or per individual quote response.
    |
    */
    'response' => [
        'default_expiry_minutes' => env('QUOTE_RESPONSE_EXPIRY_MINUTES', 60), // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | Processing Settings
    |--------------------------------------------------------------------------
    |
    | Settings for the expiry processing commands.
    |
    */
    'processing' => [
        'chunk_size' => 100,
    ],
];
