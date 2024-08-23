<?php

namespace App\Filament\Pages\LlmSettings\Concerns;

use App\Filament\Pages\LlmSettings\ApiConnectionDTO;
use Closure;

trait HasApiConnectionDTO
{
    protected Closure|ApiConnectionDTO|null $apiConnectionDTO = null;

    public function apiConnection(Closure|ApiConnectionDTO $apiConnectionDTO): static
    {
        $this->apiConnectionDTO = $apiConnectionDTO;

        return $this;
    }

    public function getApiConnectionDTO(): ?ApiConnectionDTO
    {
        return $this->evaluate($this->apiConnectionDTO);
    }
}
