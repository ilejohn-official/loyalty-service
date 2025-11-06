<?php

namespace App\Services;

use App\DTOs\UserDto;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Lightweight UserClient used to retrieve user details.
 *
 * Behavior:
 * - First try to load the local User model (DB) via Eloquent.
 * - If not found and an external user service URL is configured via
 *   USER_SERVICE_URL, attempt an HTTP GET to fetch user data.
 * - If external data is returned, create a transient User model instance
 *   with the returned attributes (sufficient for loyalty processing).
 */
class UserClient
{
    public function getById(int $id): ?UserDto
    {
        // Try local DB first
        try {
            $local = User::find($id);
            if ($local) {
                return UserDto::fromModel($local);
            }
        } catch (\Throwable $e) {
            Log::warning('Local user lookup failed', ['id' => $id, 'error' => $e->getMessage()]);
        }

        // Fallback to external user service if configured in config/user.php
        $base = config('user.service_url');
        if (! $base) {
            return null;
        }

        $cacheKey = "user:{$id}";
        $ttl = config('user.cache_ttl', 300);

        // Try cache first
        $cached = Cache::get($cacheKey);
        if ($cached && is_array($cached)) {
            return new UserDto($cached);
        }

        try {
            $endpoint = config('user.internal_endpoint', '/api/v1/internal/users/');
            $timeout = config('user.timeout', 2);
            $url = rtrim($base, '/').'/'.trim($endpoint, '/').'/'.$id;
            $resp = Http::timeout($timeout)
                ->withHeaders(['Accept' => 'application/json'])
                ->get($url);

            if ($resp->successful()) {
                $data = $resp->json();

                // Normalize payload: accept either data wrapper or raw model
                if (isset($data['data'])) {
                    $data = $data['data'];
                }

                // Build a UserDto with attributes returned by the user service
                $dto = new UserDto($data);

                // Cache for subsequent lookups
                try {
                    Cache::put($cacheKey, $dto->toArray(), $ttl);
                } catch (\Throwable $e) {
                    Log::warning('Failed to cache user data', ['id' => $id, 'error' => $e->getMessage()]);
                }

                return $dto;
            }
        } catch (\Throwable $e) {
            Log::warning('External user service lookup failed', ['id' => $id, 'error' => $e->getMessage()]);
        }

        return null;
    }
}
