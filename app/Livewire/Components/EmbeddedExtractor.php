<?php

namespace App\Livewire\Components;

use App\Filament\Resources\ExtractionRunResource\Actions\DownloadAsCsvAction;
use App\Filament\Resources\ExtractionRunResource\Actions\DownloadAsJsonAction;
use App\Filament\Resources\ExtractionRunResource\Actions\DownloadAsXmlAction;
use App\Jobs\GenerateDataJob;
use App\Livewire\Components\EmbeddedExtractor\ExtractorSteps;
use App\Livewire\Components\EmbeddedExtractor\HasStepLabels;
use App\Livewire\Components\EmbeddedExtractor\HasUploadForm;
use App\Livewire\Components\EmbeddedExtractor\WidgetAlignment;
use App\Livewire\Components\Shared\HasPartialJson;
use App\Models\ExtractionBucket;
use App\Models\ExtractionRun;
use App\Models\ExtractionRun\RunStatus;
use App\Models\SavedExtractor;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Mateffy\Magic;
use WireElements\WireExtender\Attributes\Embeddable;

/**
 * @property-read SavedExtractor $saved_extractor
 * @property-read ?ExtractionBucket $bucket
 * @property-read ?ExtractionRun $run
 */
#[Embeddable]
class EmbeddedExtractor extends Component implements HasForms, HasActions
{
    use HasUploadForm;
    use HasPartialJson;
    use HasStepLabels;

    #[Locked]
    public string $extractorId;

    #[Url(as: 'bucket', keep: true)]
    public ?string $bucketId = null;

    #[Url(as: 'run')]
    public ?string $runId = null;

    #[Url(as: 'expiresAt', keep: true)]
    public string $expiresAt;

    #[Url(as: 'signature', keep: true)]
    public string $secret;

    #[Url(history: true)]
    public ExtractorSteps $step = ExtractorSteps::Introduction;

    public array $files = [];

    #[Computed]
    public function saved_extractor(): SavedExtractor
    {
        return SavedExtractor::findOrFail($this->extractorId);
    }

    #[Computed]
    public function bucket(): ?ExtractionBucket
    {
        return $this->bucketId
            ? ExtractionBucket::find($this->bucketId)
            : null;
    }

    #[Computed]
    public function run(): ?ExtractionRun
    {
        return $this->runId
            ? ExtractionRun::find($this->runId)
            : null;
    }

    public static function generateIdSignature(?string $extractorId = null, ?string $bucketId = null, ?string $runId = null, ?string $expiresAt = null): ?string
    {
        if ($bucketId === null && $runId === null && $extractorId === null) {
            return null;
        }

        return hash_hmac('sha256', "{$extractorId}.{$bucketId}.{$runId}.{$expiresAt}", config('app.key'));
    }



    public function boot()
    {
        if ($this->step === ExtractorSteps::Bucket && $this->bucketId === null) {
            $this->step = ExtractorSteps::Introduction;
        }

        if ($this->step === ExtractorSteps::Extraction && $this->runId === null && $this->bucketId !== null) {
            $this->step = ExtractorSteps::Bucket;
        } elseif ($this->step === ExtractorSteps::Extraction && $this->runId === null) {
            $this->step = ExtractorSteps::Introduction;
        }

        if ($this->step === ExtractorSteps::Results && $this->runId === null && $this->bucketId !== null) {
            $this->step = ExtractorSteps::Bucket;
        } elseif ($this->step === ExtractorSteps::Results && $this->runId === null) {
            $this->step = ExtractorSteps::Introduction;
        }
    }

    public function mount(): void
    {
        $expiresAt = $this->expiresAt ?? request()->get('expiresAt');

        if (! $expiresAt) {
            abort(401, 'No expiration');
        }

        $date = Date::createFromTimestamp($expiresAt);

        if ($date->isPast()) {
            abort(401, 'Expired');
        }

        $checkSignature = self::generateIdSignature(
            extractorId: $this->extractorId,
            bucketId: $this->bucketId,
            runId: $this->runId,
            expiresAt: $this->expiresAt
        );

        if ($this->secret === $checkSignature) {
            $this->bucketId = $this->bucketId ?? request()->get('bucketId');
            $this->runId = $this->runId ?? request()->get('runId');
        } else {
            abort(401, 'Invalid signature');
        }

        $horizontalAlignment = WidgetAlignment::tryFrom(request()->get('horizontal-alignment') ?? '');
        $verticalAlignment = WidgetAlignment::tryFrom(request()->get('vertical-alignment') ?? '');

        if ($horizontalAlignment && $verticalAlignment) {
            $this->horizontalAlignment = $horizontalAlignment;
            $this->verticalAlignment = $verticalAlignment;
        } else if (request()->routeIs('full-page-extractor')) {
            $this->horizontalAlignment = WidgetAlignment::Center;
            $this->verticalAlignment = WidgetAlignment::Center;
        } else if (request()->routeIs('embedded-extractor')) {
            $this->horizontalAlignment = WidgetAlignment::Stretch;
            $this->verticalAlignment = WidgetAlignment::Center;
        }

        if (! $this->bucket) {
            $this->bucketId = ExtractionBucket::createEmbedded()->id;
            unset($this->bucket);

            $this->refreshSignature();
        }

        if ($this->run) {
            $this->refreshJson();

            // If the run is still running, we should go straight to the extraction step
            if ($this->run->status->shouldPoll()) {
                $this->step = ExtractorSteps::Extraction;
            }
        }
    }

    public function begin(): void
    {
        $options = $this->saved_extractor->getContextOptions();

        $run = $this->bucket->runs()->create([
            'strategy' => $this->saved_extractor->strategy,
            'started_by_id' => auth()->user()?->id,
            'target_schema' => $this->saved_extractor->json_schema,
            'saved_extractor_id' => $this->saved_extractor->id,
            'model' => $this->saved_extractor->model ?? Magic::defaultModelName(),
            'include_text' => $options->includeText,
            'include_embedded_images' => $options->includeEmbeddedImages,
            'mark_embedded_images' => $options->markEmbeddedImages,
            'include_page_images' => $options->includePageImages,
            'mark_page_images' => $options->markPageImages,
            'chunk_size' => $this->saved_extractor->chunk_size ?? config('llm-magic.artifacts.default_max_tokens')
        ]);

        $this->runId = $run->id;
        unset($this->run);

        $this->refreshSignature();

        GenerateDataJob::dispatch(run: $this->run);

        $this->step = ExtractorSteps::Extraction;
    }

    protected function refreshSignature(): void
    {
        $this->expiresAt = Date::now()->addDay()->timestamp;
        $this->secret = self::generateIdSignature(
            extractorId: $this->extractorId,
            bucketId: $this->bucketId,
            runId: $this->runId,
            expiresAt: $this->expiresAt
        );
    }

    public function finish(): void
    {
        if (!in_array($this->run?->status, [ExtractionRun\RunStatus::Completed, ExtractionRun\RunStatus::Failed])) {
            return;
        }

        $this->step = ExtractorSteps::Results;

        $this->dispatchCompletion();
    }

    public function cancel(): void
    {
        $this->step = ExtractorSteps::Bucket;
    }

    public function nextStep(): void
    {
        $this->step = match ($this->step) {
            ExtractorSteps::Introduction => ExtractorSteps::Bucket,
            ExtractorSteps::Bucket => ExtractorSteps::Extraction,
            ExtractorSteps::Extraction => ExtractorSteps::Results,
            ExtractorSteps::Results => ExtractorSteps::Introduction,
        };
    }

    public function backStep(): void
    {
        $this->step = match ($this->step) {
            ExtractorSteps::Introduction => ExtractorSteps::Introduction,
            ExtractorSteps::Bucket => ExtractorSteps::Introduction,
            ExtractorSteps::Extraction => ExtractorSteps::Bucket,
            ExtractorSteps::Results => ExtractorSteps::Extraction,
        };
    }

    public function dispatchCompletion(): void
    {
        $this->dispatch('magic-iframe-submit', data: $this->resultData);

        assert($this->run !== null, 'Run should be set');

        $this->run->dispatchWebhook();
    }

    public function downloadFile(string $fileId)
    {
        $file = $this->bucket->files()->find($fileId);

        if (! $file) {
            return;
        }

        return response()->download($file->getPath());
    }

    public function deleteFile(string $fileId): void
    {
        $this->bucket->files()->find($fileId)->delete();

        Notification::make()
            ->success()
            ->title('File deleted')
            ->send();
    }

    public function restart($hardRestart = false): void
    {
        if ($hardRestart) {
            $this->bucketId = ExtractionBucket::createEmbedded()->id;
            unset($this->bucket);
        }

        $this->runId = null;
        unset($this->run);

        $this->refreshSignature();

        $this->begin();
    }

    public function submit(): void
    {
        if ($this->config->redirectUrl) {
            // Submit

            $this->redirect($this->config->redirectUrl);
        }

        $this->dispatchCompletion();
    }

    public function canDownload(): bool
    {
        return $this->run?->status === RunStatus::Completed && $this->run?->saved_extractor->allow_download;
    }

    public function downloadAsCsvAction(): DownloadAsCsvAction
    {
        return DownloadAsCsvAction::make()
            ->visible(fn () => $this->canDownload())
            ->grouped()
            ->record($this->run);
    }

    public function downloadAsJsonAction(): DownloadAsJsonAction
    {
        return DownloadAsJsonAction::make()
            ->visible(fn () => $this->canDownload())
            ->grouped()
            ->record($this->run);
    }

    public function downloadAsXmlAction(): DownloadAsXmlAction
    {
        return DownloadAsXmlAction::make()
            ->visible(fn () => $this->canDownload())
            ->grouped()
            ->record($this->run);
    }

    public function updated(string $statePath)
    {
        if (Str::startsWith($statePath, 'resultData')) {
            $this->validateData();
        }
    }

    public function canContinue(): bool
    {
        if ($this->step === ExtractorSteps::Extraction) {
            return count($this->validationErrors) === 0;
        }

        return true;
    }

    public function render()
    {
        return view('livewire.embedded-extractor');
    }
}
