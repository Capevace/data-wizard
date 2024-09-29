<?php

namespace App\Livewire\Components\Shared;

use App\Models\ExtractionRun;

/**
 * @property-read ?ExtractionRun $run
 */
trait HasPartialJson
{
    public ?array $resultData = null;

    public function refreshJson()
    {
        $this->resultData = $this->run?->partial_data ?? $this->run?->data;
    }
}
