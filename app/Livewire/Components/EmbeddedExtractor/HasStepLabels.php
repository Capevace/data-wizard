<?php

namespace App\Livewire\Components\EmbeddedExtractor;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

trait HasStepLabels
{
    #[Locked]
    public WidgetAlignment $horizontalAlignment = WidgetAlignment::Stretch;

    #[Locked]
    public WidgetAlignment $verticalAlignment = WidgetAlignment::Center;

    #[Computed]
    public function config(): EmbeddingConfig
    {
        return new EmbeddingConfig(
            allowDownload: $this->saved_extractor->allow_download ?? true,
            redirectUrl: $this->saved_extractor->redirect_url ?? null,
            logoUrl: null,
            horizontalAlignment: $this->horizontalAlignment,
            verticalAlignment: $this->verticalAlignment,
            outerPaddingClasses: '',
            borderRadiusClasses: '',
            borderClasses: '',
        );
    }

    #[Computed]
    public function introduction_labels(): StepLabels
    {
        return new StepLabels(
            heading: $this->saved_extractor->introduction_view_heading ?? __('default_introduction_view_heading'),
            description: $this->saved_extractor->introduction_view_description ?? __('default_introduction_view_description'),
            backButton: null,
            nextButton: new StepButton(
                label: $this->saved_extractor->introduction_view_next_button_label ?? __('default_introduction_view_next_button_label'),
                icon: 'heroicon-o-arrow-right',
                color: 'primary',
                action: 'nextStep',
            ),
        );
    }

    #[Computed]
    public function bucket_labels(): StepLabels
    {
        return new StepLabels(
            heading: $this->saved_extractor->bucket_view_heading ?? __('default_bucket_view_heading'),
            description: $this->saved_extractor->bucket_view_description ?? __('default_bucket_view_description'),
            backButton: new StepButton(
                label: $this->saved_extractor->bucket_view_back_button_label ?? __('default_bucket_view_back_button_label'),
                icon: 'heroicon-o-arrow-left',
                color: 'gray',
                action: 'backStep',
            ),
            nextButton: new StepButton(
                label: $this->run
                    ? $this->saved_extractor->bucket_view_continue_button_label ?? __('default_bucket_view_continue_button_label')
                    : $this->saved_extractor->bucket_view_begin_button_label ?? __('default_bucket_view_begin_button_label'),
                icon: 'heroicon-o-arrow-right',
                color: 'primary',
                action: $this->run
                    ? 'nextStep'
                    : 'begin',
            ),
        );
    }

    #[Computed]
    public function extraction_labels(): StepLabels
    {
        return new StepLabels(
            heading: $this->saved_extractor->extraction_view_heading ?? __('default_extraction_view_heading'),
            description: $this->saved_extractor->extraction_view_description ?? __('default_extraction_view_description'),
            backButton: new StepButton(
                label: $this->saved_extractor->extraction_view_back_button_label ?? __('default_extraction_view_back_button_label'),
                icon: 'heroicon-o-arrow-left',
                color: 'gray',
                action: 'backStep',
            ),
            nextButton: new StepButton(
                label: $this->saved_extractor->extraction_view_continue_button_label ?? __('default_extraction_view_continue_button_label'),
                icon: 'heroicon-o-arrow-right',
                color: 'primary',
                action: 'finish',
                disabledWhileRunning: true,
            ),
            secondaryButton: new StepButton(
                label: $this->saved_extractor->extraction_view_restart_button_label ?? __('default_extraction_view_restart_button_label'),
                icon: 'heroicon-o-arrow-path-rounded-square',
                color: 'gray',
                action: 'restart',
                disabledWhileRunning: true,
            ),
        );
    }

    #[Computed]
    public function results_labels(): StepLabels
    {
        return new StepLabels(
            heading: $this->saved_extractor->results_view_heading ?? __('default_results_view_heading'),
            description: $this->saved_extractor->results_view_description ?? __('default_results_view_description'),
            backButton: new StepButton(
                label: $this->saved_extractor->results_view_back_button_label ?? __('default_results_view_back_button_label'),
                icon: 'heroicon-o-arrow-left',
                color: 'gray',
                action: 'backStep',
            ),
            nextButton: new StepButton(
                label: $this->saved_extractor->results_view_submit_button_label ?? __('default_results_view_submit_button_label'),
                icon: $this->saved_extractor->results_view_submit_button_icon ?? 'heroicon-o-paper-airplane',
                color: 'primary',
                action: 'submit'
            ),
        );
    }
}
