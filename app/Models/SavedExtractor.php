<?php

namespace App\Models;

use ApiPlatform\Laravel\Eloquent\Filter\PartialSearchFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Livewire\Components\EmbeddedExtractor;
use App\Livewire\Components\EmbeddedExtractor\ExtractorSteps;
use App\Livewire\Components\EmbeddedExtractor\WidgetAlignment;
use App\Models\Concerns\UsesUuid;
use App\Rules\JsonSchemaRule;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mateffy\Magic\Chat\TokenStats;
use Mateffy\Magic\Extraction\ContextOptions;
use Spatie\WebhookServer\WebhookCall;
use Swaggest\JsonSchema\JsonSchema;

/**
 * @property string $strategy
 * @property ?string $label
 * @property array $json_schema
 * @property ?string $model
 * @property ?string $output_instructions
 * @property ?string $introduction_view_heading
 * @property ?string $introduction_view_description
 * @property ?string $introduction_view_next_button_label
 * @property ?string $bucket_view_heading
 * @property ?string $bucket_view_description
 * @property ?string $bucket_view_back_button_label
 * @property ?string $bucket_view_continue_button_label
 * @property ?string $bucket_view_begin_button_label
 * @property ?string $extraction_view_heading
 * @property ?string $extraction_view_description
 * @property ?string $extraction_view_back_button_label
 * @property ?string $extraction_view_continue_button_label
 * @property ?string $extraction_view_restart_button_label
 * @property ?string $extraction_view_start_button_label
 * @property ?string $extraction_view_cancel_button_label
 * @property ?string $extraction_view_pause_button_label
 * @property ?string $results_view_heading
 * @property ?string $results_view_description
 * @property ?string $results_view_back_button_label
 * @property ?string $results_view_submit_button_label
 * @property ?string $webhook_url
 * @property ?string $webhook_secret
 * @property ?string $redirect_url
 * @property ?bool $allow_download
 * @property ?bool $enable_webhook
 * @property bool $include_text
 * @property bool $include_embedded_images
 * @property bool $mark_embedded_images
 * @property bool $include_page_images
 * @property bool $mark_page_images
 * @property ?int $chunk_size
 * @property-read ?CarbonImmutable $last_ran_at
 * @property-read ?JsonSchema $typed_schema
 */
#[ApiResource(
    uriTemplate: '/extractors',
    operations: [
        new GetCollection,
        new Post(),
    ],
    rules: [
        'json_schema' => ['required', 'json', new JsonSchemaRule],
        'strategy' => ['required', 'string', 'max:255'],
        'model' => ['nullable', 'string', 'max:255'],
        'output_instructions' => ['nullable', 'string'],
        'webhook_url' => ['nullable', 'url', 'required_if:enable_webhook,true', 'required_with:webhook_secret', 'max:255'],
        'webhook_secret' => ['nullable', 'string', 'max:255'],
        'redirect_url' => ['nullable', 'url', 'max:255'],
        'allow_download' => ['nullable', 'boolean'],
        'enable_webhook' => ['nullable', 'boolean'],
        'include_text' => ['nullable', 'boolean'],
        'include_embedded_images' => ['nullable', 'boolean'],
        'mark_embedded_images' => ['nullable', 'boolean'],
        'include_page_images' => ['nullable', 'boolean'],
        'mark_page_images' => ['nullable', 'boolean'],

        // Labels
        'page_title' => ['nullable', 'string', 'max:255'],
        'introduction_view_heading' => ['nullable', 'string', 'max:255'],
        'introduction_view_description' => ['nullable', 'string'],
        'introduction_view_next_button_label' => ['nullable', 'string', 'max:255'],
        'bucket_view_heading' => ['nullable', 'string', 'max:255'],
        'bucket_view_description' => ['nullable', 'string'],
        'bucket_view_back_button_label' => ['nullable', 'string', 'max:255'],
        'bucket_view_continue_button_label' => ['nullable', 'string', 'max:255'],
        'bucket_view_begin_button_label' => ['nullable', 'string', 'max:255'],
        'extraction_view_heading' => ['nullable', 'string', 'max:255'],
        'extraction_view_description' => ['nullable', 'string'],
        'extraction_view_back_button_label' => ['nullable', 'string', 'max:255'],
        'extraction_view_continue_button_label' => ['nullable', 'string', 'max:255'],
        'extraction_view_restart_button_label' => ['nullable', 'string', 'max:255'],
        'extraction_view_start_button_label' => ['nullable', 'string', 'max:255'],
        'extraction_view_cancel_button_label' => ['nullable', 'string', 'max:255'],
        'extraction_view_pause_button_label' => ['nullable', 'string', 'max:255'],
        'results_view_heading' => ['nullable', 'string', 'max:255'],
        'results_view_description' => ['nullable', 'string'],
        'results_view_back_button_label' => ['nullable', 'string', 'max:255'],
        'results_view_submit_button_label' => ['nullable', 'string', 'max:255'],
    ],
    middleware: ['auth:sanctum']
)]
#[ApiResource(
    uriTemplate: '/extractors/{id}',
    operations: [
        new Get,
        new Delete
    ],
    rules: [
        'json_schema' => ['required', 'json', new JsonSchemaRule],
        'model' => ['nullable', 'string', 'max:255'],
        'output_instructions' => ['nullable', 'string'],
        'webhook_url' => ['nullable', 'url', 'required_if:enable_webhook,true', 'required_with:webhook_secret', 'max:255'],
        'webhook_secret' => ['nullable', 'string', 'max:255'],
        'redirect_url' => ['nullable', 'url', 'max:255'],
        'allow_download' => ['nullable', 'boolean'],
        'enable_webhook' => ['nullable', 'boolean'],
        'include_text' => ['nullable', 'boolean'],
        'include_embedded_images' => ['nullable', 'boolean'],
        'mark_embedded_images' => ['nullable', 'boolean'],
        'include_page_images' => ['nullable', 'boolean'],
        'mark_page_images' => ['nullable', 'boolean'],

        // Labels
        'page_title' => ['nullable', 'string', 'max:255'],
        'introduction_view_heading' => ['nullable', 'string', 'max:255'],
        'introduction_view_description' => ['nullable', 'string'],
        'introduction_view_next_button_label' => ['nullable', 'string', 'max:255'],
        'bucket_view_heading' => ['nullable', 'string', 'max:255'],
        'bucket_view_description' => ['nullable', 'string'],
        'bucket_view_back_button_label' => ['nullable', 'string', 'max:255'],
        'bucket_view_continue_button_label' => ['nullable', 'string', 'max:255'],
        'bucket_view_begin_button_label' => ['nullable', 'string', 'max:255'],
        'extraction_view_heading' => ['nullable', 'string', 'max:255'],
        'extraction_view_description' => ['nullable', 'string'],
        'extraction_view_back_button_label' => ['nullable', 'string', 'max:255'],
        'extraction_view_continue_button_label' => ['nullable', 'string', 'max:255'],
        'extraction_view_restart_button_label' => ['nullable', 'string', 'max:255'],
        'extraction_view_start_button_label' => ['nullable', 'string', 'max:255'],
        'extraction_view_cancel_button_label' => ['nullable', 'string', 'max:255'],
        'extraction_view_pause_button_label' => ['nullable', 'string', 'max:255'],
        'results_view_heading' => ['nullable', 'string', 'max:255'],
        'results_view_description' => ['nullable', 'string'],
        'results_view_back_button_label' => ['nullable', 'string', 'max:255'],
        'results_view_submit_button_label' => ['nullable', 'string', 'max:255'],
    ],
    middleware: ['auth:sanctum']
)]
#[QueryParameter(key: 'model', filter: PartialSearchFilter::class)]
#[QueryParameter(key: 'output_instructions', filter: PartialSearchFilter::class)]
class SavedExtractor extends Model
{
    use SoftDeletes;
    use UsesUuid;

    protected $fillable = [
        'strategy',
        'label',
        'json_schema',
        'output_instructions',
        'model',

        'page_title',
        'logo',

        'introduction_view_heading',
        'introduction_view_description',
        'introduction_view_next_button_label',

        'bucket_view_heading',
        'bucket_view_description',
        'bucket_view_back_button_label',
        'bucket_view_continue_button_label',
        'bucket_view_begin_button_label',

        'extraction_view_heading',
        'extraction_view_description',
        'extraction_view_back_button_label',
        'extraction_view_continue_button_label',
        'extraction_view_restart_button_label',
        'extraction_view_start_button_label',
        'extraction_view_cancel_button_label',
        'extraction_view_pause_button_label',

        'results_view_heading',
        'results_view_description',
        'results_view_back_button_label',
        'results_view_submit_button_label',

        'include_text',
        'include_embedded_images',
        'mark_embedded_images',
        'include_page_images',
        'mark_page_images',

        'allow_download',
        'enable_webhook',

        'webhook_url',
        'webhook_secret',

        'redirect_url',

        'chunk_size'
    ];

    protected $casts = [
        'json_schema' => 'json',
        'allow_download' => 'bool',
        'enable_webhook' => 'bool',
        'include_text' => 'bool',
        'include_embedded_images' => 'bool',
        'mark_embedded_images' => 'bool',
        'include_page_images' => 'bool',
        'mark_page_images' => 'bool',
        'chunk_size' => 'int',
    ];

    protected $attributes = [
        'strategy' => 'parallel',
        'allow_download' => true,
        'enable_webhook' => false,
        'include_text' => true,
        'include_embedded_images' => true,
        'mark_embedded_images' => true,
        'include_page_images' => false,
        'mark_page_images' => false,
    ];

    protected $hidden = [
        'webhook_secret',
        'logo',
    ];

    public function runs(): HasMany
    {
        return $this->hasMany(ExtractionRun::class);
    }

    public function getLastRanAtAttribute(): ?CarbonImmutable
    {
        return $this->runs()->latest()->first()?->started_at;
    }

    public function getTypedSchemaAttribute(): ?JsonSchema
    {
        try {
            return JsonSchema::import($this->json_schema);
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }
    }

    public function getEmbeddedUrl(?ExtractorSteps $step = null, ?WidgetAlignment $horizontalAlignment = null, ?WidgetAlignment $verticalAlignment = null): string
    {
        $step = $step ?? ExtractorSteps::Introduction;
        $horizontalAlignment = $horizontalAlignment ?? WidgetAlignment::Center;
        $verticalAlignment = $verticalAlignment ?? WidgetAlignment::Center;

        $expiresAt = CarbonImmutable::now()->addDay()->timestamp;

        return route('embedded-extractor', [
            'extractorId' => $this->id,
            'step' => $step->value,
            'horizontal-alignment' => $horizontalAlignment->value,
            'vertical-alignment' => $verticalAlignment->value,
            'expiresAt' => $expiresAt,
            'signature' => EmbeddedExtractor::generateIdSignature(extractorId: $this->id, expiresAt: $expiresAt),
        ]);
    }

    public function getContextOptions(): ContextOptions
    {
        return new ContextOptions(
            includeText: $this->include_text,
            includeEmbeddedImages: $this->include_embedded_images,
            markEmbeddedImages: $this->mark_embedded_images,
            includePageImages: $this->include_page_images,
            markPageImages: $this->mark_page_images,
        );
    }

    public function dispatchWebhook(
        string $runId,
        array $data,
        string $model,
        int $duration_seconds,
        ?TokenStats $tokenStats,
        bool $sync = false
    ): void
    {
        if (!$this->enable_webhook || empty($this->webhook_url) || empty($this->webhook_secret)) {
            return;
        }

        $call = WebhookCall::create()
            ->url($this->webhook_url)
            ->uuid($runId)
            ->payload([
                'data' => $data,
                'runId' => $runId,
                'model' => $model,
                'duration_seconds' => $duration_seconds,
                'token_stats' => $tokenStats?->toArray(),
            ])
            ->useSecret($this->webhook_secret);

        if ($sync) {
            $call
                ->maximumTries(1)
                ->dispatchSync();
        } else {
            $call->dispatch();
        }
    }

    public function getEmbeddedUrlAttribute(): string
    {
        return $this->getEmbeddedUrl(
            horizontalAlignment: WidgetAlignment::Stretch,
            verticalAlignment: WidgetAlignment::Stretch
        );
    }

    public function getFullPageUrlAttribute(): string
    {
        return $this->getEmbeddedUrl(
            horizontalAlignment: WidgetAlignment::Center,
            verticalAlignment: WidgetAlignment::Center
        );
    }
}
