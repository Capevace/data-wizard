<?php

namespace App\Livewire\Components;

use App\Jobs\GenerateDataJob;
use App\Models\ExtractionBucket;
use App\Models\ExtractionRun;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * @property-read ExtractionBucket $bucket
 * @property-read ?ExtractionRun $run
 */
class Generator extends Component
{
    #[Locked]
    public string $bucketId;

    #[Locked]
    public ?string $runId = null;

    #[Locked]
    public ?array $partialResultJson = null;


    #[Computed]
    public function bucket(): ExtractionBucket
    {
        return ExtractionBucket::findOrFail($this->bucketId);
    }

    #[Computed]
    public function run(): ?ExtractionRun
    {
        return $this->runId
            ? $this->bucket->runs()->findOrFail($this->runId)
            : null;
    }

    public function mount()
    {
        $this->refreshJson();

//        $sourceUrl = route('api.bucket.runs.generate', [
//            'bucket' => $this->bucketId,
//            'run' => $this->runId,
//        ]);
//
//        $this->js("\$dispatch('start-streaming', { sourceUrl: '$sourceUrl' })");
    }

    public function refreshJson()
    {
        $this->partialResultJson = json_decode($this->run?->partial_result_json, associative: true);
    }

    public function render()
    {
        return view('livewire.generator');
    }
}
