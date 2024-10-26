<?php

namespace App\Filament\Pages\LlmSettings;

use Illuminate\Support\Collection;
use Mateffy\Magic\Providers\ApiKey;
use Mateffy\Magic\Providers\ApiKey\ApiKeyProvider;
use Mateffy\Magic\Providers\ApiKey\ApiKeyTokenType;

readonly class ApiConnectionDTO
{
    public function __construct(
        public ApiKeyProvider $provider,
        public bool $active = false,
        public array $fields = [],
    ) {}

    public static function withKeys(ApiKeyProvider $provider, Collection $apiKeys): self
    {
        return new self(
            provider: $provider,
            active: $apiKeys
                ->where('type', ApiKeyTokenType::Token)
                ->isNotEmpty(),
            fields: $apiKeys
                ->mapWithKeys(fn (ApiKey $apiKey) => [
                    $apiKey->type->value => $apiKey->protected_secret,
                ])
                ->toArray(),
        );
    }

    public function toArray(): array
    {
        return [
            'fields' => $this->fields,
            'active' => $this->active,
        ];
    }
}
