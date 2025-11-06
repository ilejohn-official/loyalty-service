<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccess
{
    /**
     * The mock token used for client-side authentication.
     * This should match the MOCK_TOKEN_VALUE in the React AdminLogin.tsx file.
     */
    protected const MOCK_ADMIN_TOKEN = 'mock_admin_2025_token';

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');

        if (empty($header) || ! str_starts_with($header, 'Bearer ')) {
            return response()->json([
                'message' => 'Unauthorized. Bearer token required.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = substr($header, 7);

        if ($token !== self::MOCK_ADMIN_TOKEN) {
            return response()->json([
                'message' => 'Unauthorized. Invalid token provided.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
