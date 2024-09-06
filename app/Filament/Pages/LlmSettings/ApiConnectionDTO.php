<?php

namespace App\Filament\Pages\LlmSettings;

use App\Models\ApiKey;
use App\Models\ApiKey\ApiKeyProvider;
use App\Models\ApiKey\ApiKeyTokenType;
use Illuminate\Support\Collection;

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
