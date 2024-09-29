<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Swaggest\JsonSchema\JsonSchema;

/**
 * @property string $strategy
 * @property ?string $label
 * @property bool $was_automatically_created
 * @property array $json_schema
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
 * @property-read ?\Carbon\CarbonImmutable $last_ran_at
 * @property-read ?JsonSchema $typed_schema
 */
class SavedExtractor extends Model
{
    use SoftDeletes;
    use UsesUuid;

    protected $fillable = [
        'strategy',
        'label',
        'was_automatically_created',
        'json_schema',
        'output_instructions',

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

        'allow_download',
        'enable_webhook',

        'webhook_url',
        'webhook_secret',

        'redirect_url',

        'json_schema_string',
    ];

    protected $casts = [
        'json_schema' => 'json',
    ];

    protected $attributes = [
        'strategy' => 'parallel',
        'was_automatically_created' => false,
        'allow_download' => true,
        'enable_webhook' => false,
    ];

//    protected $appends = ['json_schema_string'];

    public function runs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ExtractionRun::class);
    }

    public function getLastRanAtAttribute(): ?\Carbon\CarbonImmutable
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

//    public function getJsonSchemaStringAttribute(): ?string
//    {
//        return json_encode($this->json_schema, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
//    }
//
//    public function setJsonSchemaStringAttribute(?string $jsonSchemaString): void
//    {
//        $this->json_schema = json_decode($jsonSchemaString, true, 512, JSON_THROW_ON_ERROR);
//    }

    public function logUsage(): void
    {
        $this->updated_at = now()->toImmutable();
        $this->save();
    }

    public function getEmbeddedUrl(): string
    {
        return route('embedded-extractor', ['extractorId' => $this->id]);
    }
}
