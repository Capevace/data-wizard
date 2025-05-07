<?php

namespace App\Filament\Pages;

use App\Filament\Forms\ModelSelect;
use App\Filament\Forms\StrategySelect;
use App\Jobs\GenerateDataJob;
use App\Models\ExtractionBucket;
use App\Models\ExtractionRun;
use App\Models\SavedExtractor;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Session;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Mateffy\Magic;
use Mateffy\Magic\Support\PartialJson;

/**
 * Playground page for testing and experimenting with data extraction.
 *
 * @property-read SavedExtractor $extractor
 * @property-read ExtractionBucket $bucket
 * @property-read ExtractionRun|null $run
 * @property-read array|null $resultData
 */
class Playground extends Page
{
    use WithFileUploads;

    protected static string $view = 'filament.pages.playground';
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $title = 'Playground';
    protected static ?string $slug = 'playground';

    public const string DEFAULT_SCHEMA_JSON = <<<'JSON'
    {
        "type": "object",
        "properties": {
            "name": {
                "type": "string"
            },
            "age": {
                "type": "integer"
            }
        },
        "required": ["name", "age"]
    }
    JSON;


    #[Locked]
    #[Url(as: 'extractor')]
    public ?string $extractorId = null;

    #[Locked]
    #[Url(as: 'bucket')]
    public ?string $bucketId = null;

    #[Locked]
    #[Url(as: 'run')]
    public ?string $runId = null;

    public bool $show_saved_indicator = false;

    #[Session]
    public ?string $tab = 'gui';

    public ?string $schema_json;

    public ?string $model;

    public ?string $strategy;

    public ?string $output_instructions;

    public ?array $resultData = null;

    #[Computed]
    public function extractor(): SavedExtractor
    {
        $create = fn () => $this->extractorId = SavedExtractor::create([
            'label' => "Playground " . now()->format('Y-m-d H:i:s'),
            'json_schema' => self::DEFAULT_SCHEMA_JSON,
            'model' => Magic::defaultModelName(),
            'strategy' => 'simple',
            'output_instructions' => '',
        ])->id;

        if ($this->extractorId === null) {
            $create();
        }

        $extractor = SavedExtractor::find($this->extractorId);

        if ($extractor === null) {
            $create();
            $extractor = SavedExtractor::findOrFail($this->extractorId);
        }

        return $extractor;
    }

    #[Computed]
    public function bucket(): ?ExtractionBucket
    {
        $create = fn () => $this->bucketId = ExtractionBucket::create()->id;

        if ($this->bucketId === null) {
            $create();
        }

        $bucket = ExtractionBucket::find($this->bucketId);

        if ($bucket === null) {
            $create();
            $bucket = ExtractionBucket::findOrFail($this->bucketId);
        }

        return $bucket;
    }

    #[Computed]
    public function run(): ?ExtractionRun
    {
        $run = $this->runId
            ? ExtractionRun::findOrFail($this->runId)
            : null;

        if ($run) {
            $this->resultData = $run->data
                ?? (is_array($run->partial_data)
                    ? $run->partial_data
                    : PartialJson::parse($run->partial_data)
                );
        }

        return $run;
    }

    public function mount(): void
    {
        $this->form->fill([
            'model' => $this->extractor->model,
            'strategy' => $this->extractor->strategy,
        ]);

        $this->schema_json = is_array($this->extractor->json_schema)
            ? json_encode($this->extractor->json_schema, JSON_PRETTY_PRINT)
            : $this->extractor->json_schema;
        $this->output_instructions = $this->extractor->output_instructions;
    }

    public function updated(string $statePath)
    {
        if (in_array($statePath, ['model', 'strategy', 'schema_json', 'output_instructions'])) {
            $this->extractor->update([
                'json_schema' => $this->schema_json,
                'model' => $this->model,
                'strategy' => $this->strategy,
                'output_instructions' => $this->output_instructions,
            ]);

            $this->show_saved_indicator = true;
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->live()
            ->columns()
            ->schema([
                ModelSelect::make('model'),
                StrategySelect::make('strategy')
            ]);
    }

    public function startRun(): void
    {
        $run = $this->bucket->runs()->create([
            'saved_extractor_id' => $this->extractor->id,
            'started_by_id' => auth()->id(),
            'target_schema' => json_decode($this->schema_json, true),
            'output_instructions' => $this->output_instructions,
            'model' => $this->model,
            'strategy' => $this->strategy,
            'include_text' => true,
            'include_embedded_images' => true,
            'mark_embedded_images' => true,
            'include_page_images' => true,
            'mark_page_images' => true,
            'chunk_size' => 25000
        ]);

        $this->runId = $run->id;

        unset($this->run);

        GenerateDataJob::dispatch(run: $run);
    }

    public function validateData()
    {
        // TODO: Validate data against the schema


    }

    public function resetPlayground(): void
    {
        $this->extractorId = null;
        $this->bucketId = null;
        $this->runId = null;

        unset($this->extractor);
        unset($this->bucket);
        unset($this->run);

        $this->show_saved_indicator = false;
        $this->schema_json = self::DEFAULT_SCHEMA_JSON;
        $this->model = Magic::defaultModelName();
        $this->strategy = 'simple';
        $this->output_instructions = '';
        $this->resultData = null;

        $this->form->fill([
            'model' => $this->model,
            'strategy' => $this->strategy,
        ]);
    }

    public function getHeader(): ?View
    {
        return view('components.empty');
    }
}
