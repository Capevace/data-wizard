<?php

namespace App\Livewire\Components;

use App\Filament\Resources\ExtractionRunResource\Actions\DownloadAsCsvAction;
use App\Filament\Resources\ExtractionRunResource\Actions\DownloadAsJsonAction;
use App\Filament\Resources\ExtractionRunResource\Actions\DownloadAsXmlAction;
use App\Jobs\GenerateDataJob;
use App\Livewire\Components\EmbeddedExtractor\ExtractorSteps;
use App\Livewire\Components\EmbeddedExtractor\HasStepLabels;
use App\Livewire\Components\EmbeddedExtractor\HasUploadForm;
use App\Livewire\Components\Shared\HasPartialJson;
use App\Models\ExtractionBucket;
use App\Models\ExtractionRun;
use App\Models\ExtractionRun\RunStatus;
use App\Models\SavedExtractor;
use App\WidgetAlignment;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Spatie\WebhookServer\WebhookCall;
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

    #[Url(as: 'signature', keep: true)]
    public ?string $secret = null;

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

    public static function generateIdSignature(?string $bucketId = null, ?string $runId = null): ?string
    {
        if ($bucketId === null && $runId === null) {
            return null;
        }

        return hash_hmac('sha256', "{$bucketId}.{$runId}", config('app.key'));
    }

    public function boot()
    {
        if (!$this->secret) {
            $this->bucketId = ExtractionBucket::createEmbedded()->id;
            $this->runId = null;
            $this->secret = self::generateIdSignature(bucketId: $this->bucketId, runId: $this->runId);
        } else if ($this->secret === self::generateIdSignature(bucketId: $this->bucketId ?? request()->get('bucketId'), runId: $this->runId ?? request()->get('runId'))) {
            $this->bucketId = $this->bucketId ?? request()->get('bucketId');
            $this->runId = $this->runId ?? request()->get('runId');
        } else {
            abort(404);
        }

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

    public function mount()
    {
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

            $this->secret = self::generateIdSignature(bucketId: $this->bucketId, runId: $this->runId);
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
        $run = $this->bucket->runs()->create([
            'strategy' => $this->saved_extractor->strategy,
            'started_by_id' => auth()->user()?->id,
            'target_schema' => $this->saved_extractor->json_schema,
            'saved_extractor_id' => $this->saved_extractor->id,
            'model' => $this->saved_extractor->model ?? config('magic-extract.default-model'),
        ]);

        $this->runId = $run->id;
        unset($this->run);

        $this->secret = self::generateIdSignature(bucketId: $this->bucketId, runId: $this->runId);

        GenerateDataJob::dispatch(
            run: $this->run,
            startedBy: auth()->user()
        );

        $this->step = ExtractorSteps::Extraction;
    }

    public function finish(): void
    {
        if (!in_array($this->run?->status, [ExtractionRun\RunStatus::Completed, ExtractionRun\RunStatus::Failed])) {
            return;
        }

//        $validated = $this->validate([
//            'data' => ['required', 'array'],
//        ]);

        $this->step = ExtractorSteps::Results;

        $this->dispatchCompletion();
    }

    public function cancel(): void
    {
        $this->step = ExtractorSteps::Bucket;

//        $this->run->stop();
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

    public function dispatchCompletion()
    {
        $this->dispatch('magic-iframe-submit', data: $this->resultData);

        if ($this->saved_extractor->webhook_url && $this->saved_extractor->enable_webhook) {
            WebhookCall::create()
                ->url($this->saved_extractor->webhook_url)
                ->doNotVerifySsl()
                ->payload([
                    'extractor_id' => $this->saved_extractor->id,
                    'bucket_id' => $this->bucketId,
                    'run_id' => $this->runId,
                    'data' => $this->resultData,
                ])
                ->useSecret($this->saved_extractor->webhook_secret)
                ->dispatchSync();
        }
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
//        $this->run->stop();

        if ($hardRestart) {
            $this->bucketId = ExtractionBucket::createEmbedded()->id;
            unset($this->bucket);
        }

        $this->runId = null;
        unset($this->run);

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

    public function render()
    {
        return view('livewire.embedded-extractor');
    }
}
