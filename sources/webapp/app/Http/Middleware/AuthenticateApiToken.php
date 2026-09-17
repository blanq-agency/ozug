<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Statamic\Facades\User;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $hash = hash('sha256', $token);

        $user = User::query()
            ->whereRole('api')
            ->get()
            ->first(fn ($user) => collect($user->get('api_tokens', []))
                ->contains(fn ($entry) => hash_equals($entry['token'] ?? '', $hash)));

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return $next($request);
    }
}
