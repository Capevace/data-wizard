<?php

namespace App\Models\Actor;

readonly class ActorTelemetry
{
    public function __construct(
        public string $model,
        public ?string $system_prompt,
    )
    {
    }

    public function toDatabase(): array
    {
        return [
            'model' => $this->model,
            'system_prompt' => $this->system_prompt,
        ];
    }
}
