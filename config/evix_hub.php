<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Evix Hub Integration Settings
    |--------------------------------------------------------------------------
    |
    | Configuration settings for communicating with the central integration
    | hub at https://hub.evixdev.com.
    |
    */

    'base_url' => env('EVIX_HUB_BASE_URL', 'https://hub.evixdev.com'),

    'api_key' => env('EVIX_HUB_API_KEY', ''),

    'api_secret' => env('EVIX_HUB_API_SECRET', ''),

    'tenant_id' => env('EVIX_HUB_TENANT_ID', 1),

    'environment' => env('EVIX_HUB_ENV', 'production'), // production | sandbox | local

    'timeout' => (int) env('EVIX_HUB_TIMEOUT', 30),

    'cache_ttl' => (int) env('EVIX_HUB_CACHE_TTL', 3600),

    'endpoints' => [
        'manifest' => '/api/v1/erp/integrations/manifest',
        'activate' => '/api/v1/erp/integrations/activate',
        'deactivate' => '/api/v1/erp/integrations/deactivate',
        'test_connection' => '/api/v1/integrations/{slug}/test-connection',
    ],
];
