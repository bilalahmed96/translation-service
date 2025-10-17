<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extract token from Authorization header
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'Missing or invalid token format'], 401);
        }

        $providedToken = trim(substr($authHeader, 7));

        // Compare with system token
        $validToken = config('app.api_token');

        if (!$validToken || $providedToken !== $validToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
