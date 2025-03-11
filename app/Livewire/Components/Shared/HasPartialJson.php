<?php

namespace App\Livewire\Components\Shared;

use App\Models\ExtractionRun;
use Illuminate\Support\Arr;
use JsonException;
use Livewire\Attributes\Locked;
use Mateffy\Magic\Support\JsonValidator;

/**
 * @property-read ?ExtractionRun $run
 */
trait HasPartialJson
{
    public ?array $resultData = null;

    #[Locked]
    public array $validationErrors = [];

    #[Locked]
    public int $lastValidationTimestamp = 0;

    public function refreshJson()
    {
        $this->resultData = $this->run?->partial_data ?? $this->run?->data;
    }

    /**
     * @throws JsonException
     */
    public function validateData(): bool
    {
        $now = now()->timestamp;

        // The last validation must have been at least 1 second ago
        if ($now - $this->lastValidationTimestamp < 1) {
            return false;
        }

        $this->lastValidationTimestamp = $now;

        if ($this->resultData === null) {
            $this->validationErrors = [];
            return true;
        }

        $validator = app(JsonValidator::class);

        $resultDataWithNulls = json_decode(json_encode($this->resultData), true);

        // Deeply replace empty strings with nulls
        array_walk_recursive($resultDataWithNulls, function (&$value) {
            if ($value === '') {
                $value = null;
            }
        });

        // The errors are stored in a dot-notated array, we want to store them as a nested array
        // so that we can simply use the same statePath logic we use for the data and just replace "$wire.resultData" with "$wire.validationErrors"
        $errors = $validator->validate(
            data: $resultDataWithNulls,
            schema: $this->run->target_schema ?? $this->saved_extractor->json_schema
        );

        // Using Arr::set with dot-notated keys will result in a nested array
        $this->validationErrors = [];

        foreach (($errors ?? []) as $dotKey => $message) {
            Arr::set($this->validationErrors, $dotKey, $message);
        }

        return true;
    }
}
