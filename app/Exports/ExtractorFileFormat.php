<?php

namespace App\Exports;

use App\Models\SavedExtractor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class ExtractorFileFormat implements ModelFileFormat
{
    public static string $model = SavedExtractor::class;

    use BaseModelFileFormat;

    public const LABELS = [
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
    ];

    public function __construct(
        public string $id,
        public string $strategy,
        public string $label,
        public array $json_schema,
        public ?string $output_instructions,

        public ?string $page_title,
        public ?string $logo,

        public bool $allow_download,
        public bool $enable_webhook,

        public ?string $webhook_url,
        public ?string $webhook_secret,
        public ?string $redirect_url,

        public array $labels,
    )
    {
    }

    public function toData(): array
    {
        return [
            'id' => $this->id,
            'strategy' => $this->strategy,
            'label' => $this->label,
            'json_schema' => $this->json_schema,
            'output_instructions' => $this->output_instructions,
            'page_title' => $this->page_title,
            'logo' => $this->logo,
            'allow_download' => $this->allow_download,
            'webhook' => $this->enable_webhook && $this->webhook_url !== null
                ? ['url' => $this->webhook_url, 'secret' => $this->webhook_secret]
                : null,
            'redirect' => $this->redirect_url !== null
                ? ['url' => $this->redirect_url]
                : null,
            'labels' => $this->labels,
        ];
    }

    public function toModelData(): array
    {
        return [
            'strategy' => $this->strategy,
            'label' => $this->label,
            'json_schema' => $this->json_schema,
            'output_instructions' => $this->output_instructions,
            'page_title' => $this->page_title,
            'logo' => $this->logo,
            'allow_download' => $this->allow_download,
            'enable_webhook' => $this->enable_webhook,
            'webhook_url' => $this->webhook_url,
            'webhook_secret' => $this->webhook_secret,
            'redirect_url' => $this->redirect_url,
            ...$this->labels,
        ];
    }

    public static function fromData(array $data): self
    {
        $validator = validator($data, [
            'strategy' => ['required', 'string'],
            'label' => ['required', 'string'],
            'json_schema' => ['required', 'array'],
            'output_instructions' => ['nullable', 'string'],
            'page_title' => ['nullable', 'string'],
            'logo' => ['nullable', 'string'],
            'allow_download' => ['nullable', 'boolean'],
            'enable_webhook' => ['nullable', 'boolean'],
            'webhook' => ['nullable', 'array'],
            'webhook.url' => ['required_unless:webhook,null', 'string', 'url'],
            'webhook.secret' => ['nullable', 'string'],
            'redirect' => ['nullable', 'array'],
            'redirect.url' => ['required_unless:redirect,null', 'string', 'url'],
            'labels' => ['required', 'array'],
            'labels.*' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException("Invalid data: {$validator->errors()->first()}");
        }

        $data['labels'] = collect($data['labels'] ?? [])
            ->filter(fn (?string $value, string $key) => in_array($key, self::LABELS))
            ->mapWithKeys(fn (?string $value, string $key) => [$key => $value])
            ->toArray() ?? [];

        return new self(
            id: $data['id'],
            strategy: $data['strategy'],
            label: $data['label'],
            json_schema: $data['json_schema'],
            output_instructions: $data['output_instructions'] ?? null,
            page_title: $data['page_title'] ?? null,
            logo: $data['logo'] ?? null,
            allow_download: $data['allow_download'] ?? true,
            enable_webhook: Arr::get($data, 'webhook.url') !== null,
            webhook_url: Arr::get($data, 'webhook.url'),
            webhook_secret: Arr::get($data, 'webhook.secret'),
            redirect_url: Arr::get($data, 'redirect.url'),
            labels: $data['labels'] ?? [],
        );
    }

    /**
     * @param SavedExtractor $model
     */
    public static function fromModel(Model $model): self
    {
        return new self(
            id: $model->id,
            strategy: $model->strategy,
            label: $model->label,
            json_schema: $model->json_schema,
            output_instructions: $model->output_instructions,
            page_title: $model->page_title,
            logo: $model->logo,
            allow_download: $model->allow_download,
            enable_webhook: $model->enable_webhook,
            webhook_url: $model->webhook_url,
            webhook_secret: $model->webhook_secret,
            redirect_url: $model->redirect_url,
            labels: [
                'introduction_view_heading' => $model->introduction_view_heading,
                'introduction_view_description' => $model->introduction_view_description,
                'introduction_view_next_button_label' => $model->introduction_view_next_button_label,
                'bucket_view_heading' => $model->bucket_view_heading,
                'bucket_view_description' => $model->bucket_view_description,
                'bucket_view_back_button_label' => $model->bucket_view_back_button_label,
                'bucket_view_continue_button_label' => $model->bucket_view_continue_button_label,
                'bucket_view_begin_button_label' => $model->bucket_view_begin_button_label,
                'extraction_view_heading' => $model->extraction_view_heading,
                'extraction_view_description' => $model->extraction_view_description,
                'extraction_view_back_button_label' => $model->extraction_view_back_button_label,
                'extraction_view_continue_button_label' => $model->extraction_view_continue_button_label,
                'extraction_view_restart_button_label' => $model->extraction_view_restart_button_label,
                'extraction_view_start_button_label' => $model->extraction_view_start_button_label,
                'extraction_view_cancel_button_label' => $model->extraction_view_cancel_button_label,
                'extraction_view_pause_button_label' => $model->extraction_view_pause_button_label,
                'results_view_heading' => $model->results_view_heading,
                'results_view_description' => $model->results_view_description,
                'results_view_back_button_label' => $model->results_view_back_button_label,
                'results_view_submit_button_label' => $model->results_view_submit_button_label,
            ]
        );
    }
}
