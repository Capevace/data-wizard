<?php

namespace App\Models;

use Akaunting\Money\Money;
use ApiPlatform\Laravel\Eloquent\Filter\EqualsFilter;
use ApiPlatform\Laravel\Eloquent\Filter\PartialSearchFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Filament\Resources\ExtractionRunResource\Pages\RunPage;
use App\Jobs\GenerateDataJob;
use App\Livewire\Components\EmbeddedExtractor;
use App\Models\Actor\ActorMessageType;
use App\Models\Concerns\TokenStatsCast;
use App\Models\Concerns\UsesUuid;
use App\Models\ExtractionRun\RunStatus;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use JsonException;
use Mateffy\Magic;
use Mateffy\Magic\Builder\ExtractionLLMBuilder;
use Mateffy\Magic\Chat\ActorTelemetry;
use Mateffy\Magic\Chat\Messages\Message;
use Mateffy\Magic\Chat\Prompt\Role;
use Mateffy\Magic\Chat\TokenStats;
use Mateffy\Magic\Extraction\ArtifactBatcher;
use Mateffy\Magic\Extraction\Artifacts\Artifact;
use Mateffy\Magic\Extraction\ContextOptions;
use Mateffy\Magic\Extraction\EvaluationType;
use Mateffy\Magic\Extraction\Slices\Slice;
use Mateffy\Magic\Models\ElElEm;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\SchemaContract;

/**
 * @property-read User $started_by
 * @property ?array $target_schema
 * @property ?SchemaContract $target_schema_typed
 * @property ?array $result_json Should be used internally to set the final data. Use $run->data instead.
 * @property ?string $partial_result_json Should be used internally to set the partial data. Use $run->partial_data instead.
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
 * @property ?CarbonImmutable $finished_at
 * @property ?CarbonImmutable $started_at
 * @property ?int $chunk_size
 * @property ?array $data
 * @property string|array|null $partial_data
 * @property-read ?SavedExtractor $saved_extractor
 * @property-read CarbonInterval $duration
 * @property-read string $formatted_duration
 * @property-read string $evaluation_type
 */
#[ApiResource(
    uriTemplate: '/runs',
    operations: [
        new GetCollection
    ],
    middleware: ['auth:sanctum']
)]
#[ApiResource(
    uriTemplate: '/runs/{id}',
    description: 'An in-progress or completed extraction run',
    operations: [
        new Get
    ],
    middleware: ['auth:sanctum']
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
        'started_at',
        'finished_at',
        'chunk_size'
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
        'started_at' => 'immutable_datetime',
        'finished_at' => 'immutable_datetime',
        'chunk_size' => 'int',
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

    public function getDurationAttribute(): ?CarbonInterval
    {
        if (! $this->started_at) {
            return null;
        }

        $started_at = $this->started_at;
        $finished_at = $this->finished_at ?? now();

        return $started_at->diffAsCarbonInterval($finished_at);
    }

    public function getFormattedDurationAttribute(): string
    {
        return $this->duration?->forHumans() ?? __('Not started yet');
    }

    public function getTargetSchemaTypedAttribute(): ?SchemaContract
    {
        return Schema::import(
            json_decode(json_encode($this->target_schema))
        );
    }

    public function getTotalInputTokensAttribute(): int
    {
        // Some APIs give back accurate token counts, so we can use that.
        // If not, we use our own calculation method.
        return $this->token_stats->inputTokens ?? $this->bucket->calculateInputTokens(contextOptions: $this->getContextOptions());
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

    public function setDataAttribute(array|Collection|null $data): void
    {
        if ($data instanceof Collection) {
            $data = $data->toArray();
        }

        $this->result_json = $data;

        // Setting the final data should also set the partial data so there's no difference between the two.
        $this->partial_data = $data;
    }

    public function getPartialDataAttribute(): ?array
    {
        return json_decode($this->partial_result_json, associative: true) ?? $this->data;
    }

    /**
     * @throws JsonException
     */
    public function setPartialDataAttribute(string|array|null $data): void
    {
        if (is_array($data)) {
            $data = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        $this->partial_result_json = $data;
    }

    public function getEvaluationTypeAttribute(): EvaluationType
    {
        return $this->getContextOptions()->getEvaluationType();
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

    /**
     * While the stored token stats are more accurate and returned from LLM APIs (or not if they don't support it),
     * we fill in some stats using the internal estimations, which are always available.
     *
     * We also support an evaluation mode, where the internal estimations are ALWAYS used to allow for
     * comparing the costs of different strategies and models. In this mode, the same token calculation is used
     * so the returned values are consistent across LLM providers.
     */
    public function getEnrichedTokenStats(): TokenStats
    {
        if (config('app.evaluation_mode') || $this->token_stats === null) {
            $cached = cache()->get('run-token-stats-' . $this->id);

            if ($cached) {
                return $cached;
            }

            $messages = $this->actors()
                ->with('messages')
                ->get()
                ->flatMap(fn (Actor $actor) => $actor->messages);

            $count = $messages
                ->reduce(fn (int $carry, ActorMessage $message) => $carry + $message->calculateTokens(), 0);

            $stats = TokenStats::withInputAndOutput(
                inputTokens: $count,
                // Output tokens are not supported in evaluation mode, as the values are different across LLM providers.
                outputTokens: $this->calculateOutputTokens(),
                cost: ElElEm::fromString($this->model)->getModelCost(),
            );

            // If the run is finished, we cache the token stats for a minute to make it load quicker.
            // TODO: it might make sense to store this permanently with the option to recalculate it.
            if ($this->finished_at) {
                // Since the token stats are expensive to calculate, we cache them for a minute.
                cache()->put('run-token-stats-' . $this->id, $stats, now()->addHour());
            }

            return $stats;
        }

        return $this->token_stats;
    }

    public function calculateOutputTokens(): int
    {
        return $this->actors()
            ->with([
                'messages' => fn ($query) => $query
                    ->where('role', Role::Assistant)
                    ->where('type', '!=', ActorMessageType::Base64Image)
            ])
            ->get()
            ->flatMap(fn (Actor $actor) => $actor->messages
                // We're only interested in Assistant messages. I'm not aware of any current LLMs directly outputting
                // image data, but to be sure we filter it out.
                ->filter(fn (ActorMessage $message) => $message->role === Role::Assistant && $message->type !== ActorMessageType::Base64Image)
            )
            ->sum(fn (ActorMessage $message) => $message->calculateTokens());
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

    public function makeMagicExtractor(): ExtractionLLMBuilder
    {
        return Magic::extract()
            ->model(ElElEm::fromString($this->model))
            ->instructions($this->saved_extractor->output_instructions)
            ->schema($this->target_schema)
            ->strategy($this->strategy)
            ->chunkSize($this->chunk_size)
            ->contextOptions($this->getContextOptions())
            ->artifacts($this->bucket->artifacts->all())
            ->onMessage(function (Message $message, ?string $actorId = null) {
                /** @var ?Actor $actor */
                $actor = $this->actors()->find($actorId);

                if (! $actor) {
                    Log::warning('Actor not found', [
                        'runId' => $this->id,
                        'actorId' => $actorId,
                        'message' => $message->toArray()
                    ]);
                }

                $actor?->add($message);
            })
            ->onTokenStats(function (TokenStats $tokenStats) {
                $this->token_stats = $tokenStats;
                $this->save();
            })
            ->onActorTelemetry(function (ActorTelemetry $telemetry) {
                /** @var ?Actor $actor */
                $actor = $this->actors()->find($telemetry->id);

                if (! $actor) {
                    $actor = $this->actors()->make();
                    $actor->id = $telemetry->id;
                }

                $actor->fill($telemetry->toDatabase());
                $actor->save();
            })
            ->onDataProgress(function (array $data) {
                $this->partial_data = $data;
                $this->save();
            });
    }

    public function getCompletedSteps(): int
    {
        return $this->actors()->count();
    }

    public function getEstimatedSteps(): int
    {
        return $this
            ->makeMagicExtractor()
            ->makeStrategy()
            ->getEstimatedSteps($this->bucket->artifacts->all());
    }

    public function retry(): self
    {
        $run = $this->replicate();
        $run->status = RunStatus::Pending;
        $run->data = null;
        $run->partial_data = null;
        $run->finished_at = null;
        $run->token_stats = null;
        $run->started_by_id = auth()->id();
        $run->save();

        GenerateDataJob::dispatch(run: $run);

        return $run;
    }
}
