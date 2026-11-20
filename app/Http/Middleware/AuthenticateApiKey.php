<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $plain = $request->header('X-API-KEY');

        if (! $plain && $request->bearerToken()) {
            $plain = $request->bearerToken();
        }

        if (! $plain) {
            return response()->json(['message' => 'API key tidak ditemukan. Kirim via header X-API-KEY.'], 401);
        }

        $key = ApiKey::findByPlain($plain);

        if (! $key || $key->isRevoked()) {
            return response()->json(['message' => 'API key tidak valid atau sudah dicabut.'], 401);
        }

        if ($key->isExpired()) {
            return response()->json(['message' => 'API key sudah kedaluwarsa.'], 401);
        }

        $key->forceFill(['last_used_at' => now()])->saveQuietly();
        $request->attributes->set('api_key', $key);

        return $next($request);
    }
}
