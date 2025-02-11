<?php

namespace App\Magic\Tools;

use Mateffy\Magic\Chat\Messages\FunctionCall;
use Mateffy\Magic\Tools\InvokableTool;

class OutputStepLabels implements InvokableTool
{
    public function __construct() {}

    public function name(): string
    {
        return 'outputUILabels';
    }

    public function description(): ?string
    {
        return 'Creatively compose the UI labels for the given schema.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'required' => [
                'introduction_view_heading',
                'introduction_view_description',
                'introduction_view_next_button_label',
                'bucket_view_heading',
                'bucket_view_description',
                'bucket_view_back_button_label',
                'bucket_view_begin_button_label',
                'bucket_view_continue_button_label',
                'extraction_view_heading',
                'extraction_view_description',
                'extraction_view_back_button_label',
                'extraction_view_continue_button_label',
                'extraction_view_restart_button_label',
                'results_view_heading',
                'results_view_description',
                'results_view_back_button_label',
                'results_view_next_button_label',
            ],
            'properties' => [
                'introduction_view_heading' => [
                    'type' => 'string',
                ],
                'introduction_view_description' => [
                    'type' => 'string',
                ],
                'introduction_view_next_button_label' => [
                    'type' => 'string',
                ],
                'bucket_view_heading' => [
                    'type' => 'string',
                ],
                'bucket_view_description' => [
                    'type' => 'string',
                ],
                'bucket_view_back_button_label' => [
                    'type' => 'string',
                ],
                'bucket_view_begin_button_label' => [
                    'type' => 'string',
                ],
                'bucket_view_continue_button_label' => [
                    'type' => 'string',
                ],
                'extraction_view_heading' => [
                    'type' => 'string',
                ],
                'extraction_view_description' => [
                    'type' => 'string',
                ],
                'extraction_view_back_button_label' => [
                    'type' => 'string',
                ],
                'extraction_view_continue_button_label' => [
                    'type' => 'string',
                ],
                'extraction_view_restart_button_label' => [
                    'type' => 'string',
                ],
                'results_view_heading' => [
                    'type' => 'string',
                ],
                'results_view_description' => [
                    'type' => 'string',
                ],
                'results_view_back_button_label' => [
                    'type' => 'string',
                ],
                'results_view_next_button_label' => [
                    'type' => 'string',
                ],
            ]
        ];
    }

    public function validate(array $arguments): array
    {
        return $arguments;
    }

    public function execute(FunctionCall $call): mixed
    {
        return null;
    }

    public function callback(): \Closure
    {
        return fn () => null;
    }
}
