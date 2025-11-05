<?php

return [
  /*
    |--------------------------------------------------------------------------
    | User service configuration
    |--------------------------------------------------------------------------
    |
    | The loyalty microservice will attempt to resolve user details locally
    | from the application database first. If the user cannot be found and a
    | remote user service is configured, it will fall back to calling that
    | service. Set USER_SERVICE_URL in your environment to enable the
    | remote lookup.
    |
    */

  // Base URL for the user service (optional)
  'service_url' => env('USER_SERVICE_URL', null),

  // HTTP timeout (seconds) when calling the user service
  'timeout' => env('USER_SERVICE_TIMEOUT', 2),

  // Internal endpoint path (appended to service_url). Adjust if different
  'internal_endpoint' => env('USER_SERVICE_INTERNAL_ENDPOINT', '/api/v1/internal/users/'),

  // Cache TTL (seconds) for user lookups when USER_SERVICE_URL is used
  'cache_ttl' => env('USER_SERVICE_CACHE_TTL', 300),
];
