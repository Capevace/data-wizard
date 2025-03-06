<?php

namespace App\Models;

use Akaunting\Money\Money;
use ApiPlatform\Laravel\Eloquent\Filter\EqualsFilter;
use ApiPlatform\Laravel\Eloquent\Filter\PartialSearchFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Filament\Resources\ExtractionRunResource\Pages\RunPage;
use App\Livewire\Components\EmbeddedExtractor;
use App\Models\Concerns\TokenStatsCast;
use App\Models\Concerns\UsesUuid;
use App\Models\ExtractionRun\RunStatus;
use Illuminate\Validation\Rules\Enum;
use Mateffy\Magic\Chat\TokenStats;
use Mateffy\Magic\Extraction\ContextOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\WebhookServer\WebhookCall;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\SchemaContract;

/**
 * @property-read User $started_by
 * @property ?array $target_schema
 * @property ?SchemaContract $target_schema_typed
 * @property ?array $result_json
 * @property ?string $partial_result_json
 * @property ?array $error
 * @property ?TokenStats $token_stats
 * @property string $strategy
 * @property string $model
 * @property ?string $saved_extractor_id
 * @property RunStatus $status
 * @property bool $include_text
 * @property bool $include_embedded_images
 * @property bool $mark_embedded_images
 * @property bool $include_page_images
 * @property bool $mark_page_images
 * @property-read ?SavedExtractor $saved_extractor
 * @property-read ?array $data
 * @property-read ?array $partial_data
 */
#[ApiResource(
    uriTemplate: '/runs',
    operations: [
        new GetCollection
    ],
)]
#[ApiResource(
    uriTemplate: '/runs/{id}',
    description: 'An in-progress or completed extraction run',
    operations: [
        new Get,
        new Delete,
        new Post(
            shortName: 'Start',
            description: 'Start a new extraction run',
        ),
    ],
    rules: [
        'target_schema' => ['required', 'json'],
        'strategy' => ['required', 'string', 'max:255'],
        'model' => ['required', 'string', 'max:255'],
        'saved_extractor_id' => ['required', 'string', 'exists:saved_extractors,id'],
        'include_text' => ['nullable', 'boolean'],
        'include_embedded_images' => ['nullable', 'boolean'],
        'mark_embedded_images' => ['nullable', 'boolean'],
        'include_page_images' => ['nullable', 'boolean'],
        'mark_page_images' => ['nullable', 'boolean'],
    ],
)]
#[QueryParameter(key: 'result_json', filter: PartialSearchFilter::class)]
#[QueryParameter(key: 'partial_result_json', filter: PartialSearchFilter::class)]
#[QueryParameter(key: 'error', filter: PartialSearchFilter::class)]
#[QueryParameter(key: 'model', filter: PartialSearchFilter::class)]
#[QueryParameter(key: 'saved_extractor_id', filter: EqualsFilter::class)]
#[QueryParameter(key: 'strategy', filter: EqualsFilter::class)]
#[QueryParameter(key: 'status', filter: EqualsFilter::class)]
class ExtractionRun extends Model
{
    use HasFactory;
    use UsesUuid;

    protected $hidden = [
        'target_schema_typed',
    ];

    protected $fillable = [
        'target_schema',
        'strategy',
        'status',
        'model',
        'error',
        'started_by_id',
        'result_json',
        'partial_result_json',
        'token_stats',
        'saved_extractor_id',
        'include_text',
        'include_embedded_images',
        'mark_embedded_images',
        'include_page_images',
        'mark_page_images',
    ];

    protected $casts = [
        'target_schema' => 'json',
        'error' => 'json',
        'status' => RunStatus::class,
        'result_json' => 'json',
        'token_stats' => TokenStatsCast::class,
        'include_text' => 'boolean',
        'include_embedded_images' => 'boolean',
        'mark_embedded_images' => 'boolean',
        'include_page_images' => 'boolean',
        'mark_page_images' => 'boolean',
    ];

    protected $attributes = [
        'target_schema' => null,
        'error' => null,
        'status' => RunStatus::Pending,
        'result_json' => '{}',
        'token_stats' => null,
        'include_text' => true,
        'include_embedded_images' => true,
        'mark_embedded_images' => false,
        'include_page_images' => false,
        'mark_page_images' => false,
    ];

    public function bucket(): BelongsTo
    {
        return $this->belongsTo(ExtractionBucket::class, 'bucket_id');
    }

    public function started_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by_id');
    }

    public function getTargetSchemaTypedAttribute(): ?SchemaContract
    {
        return Schema::import(
            json_decode(json_encode($this->target_schema))
        );
    }

    public function saved_extractor(): BelongsTo
    {
        return $this->belongsTo(SavedExtractor::class, 'saved_extractor_id');
    }

    public function calculateTotalCost(): Money
    {
        if (! $this->token_stats) {
            return Money::EUR(0);
        }

        return $this->token_stats->calculateTotalCost();
    }

    public function actors(): HasMany
    {
        return $this->hasMany(Actor::class, 'extraction_run_id');
    }

    public function getDataAttribute(): ?array
    {
        return $this->result_json;
    }

    public function getPartialDataAttribute(): ?array
    {
        return json_decode($this->partial_result_json, associative: true) ?? $this->data;
    }

    public function getEmbeddedUrl(): string
    {
        return route('embedded-extractor', [
            'extractorId' => $this->saved_extractor_id,
            'bucketId' => $this->bucket_id,
            'runId' => $this->id,
            'signature' => EmbeddedExtractor::generateIdSignature(bucketId: $this->bucket_id, runId: $this->id),
        ]);
    }

    public function getAdminUrl(): string
    {
        return RunPage::getUrl(['record' => $this->id], panel: 'admin');
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

    public function dispatchWebhook(): void
    {
        $finished_at = now();

        $this->saved_extractor->dispatchWebhook(
            runId: $this->id,
            data: $this->result_json,
            model: $this->model,
            duration_seconds: $this->created_at->diffInSeconds($finished_at),
            tokenStats: $this->token_stats
        );
    }
}
