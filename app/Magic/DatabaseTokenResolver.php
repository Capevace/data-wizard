<?php

namespace App\Magic;

use App\Models\ApiKey;
use Mateffy\Magic\Exceptions\MissingApiToken;
use Mateffy\Magic\Support\ApiTokens\TokenResolver;

class DatabaseTokenResolver implements TokenResolver
{
    /**
     * @throws MissingApiToken
     */
    public function resolve(string $provider, string $key = 'token'): ?string
    {
        $apiKey = ApiKey::query()
            ->where('provider', $provider)
            ->where('type', $key)
            ->first();

        $token = $apiKey?->secret;

        if (empty($token) && $key === 'token') {
			throw new MissingApiToken($provider);
		}

		return $token;
    }
}
