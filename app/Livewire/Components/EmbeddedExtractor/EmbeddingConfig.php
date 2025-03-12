<?php

namespace App\Livewire\Components\EmbeddedExtractor;

class EmbeddingConfig
{
    public function __construct(
        public bool $allowDownload = true,
        public ?string $redirectUrl = null,

        public ?string $title = null,
        public ?string $logoUrl = null,
        public WidgetAlignment $logoAlignment = WidgetAlignment::Start,

        public string $bodyBackgroundColor = 'transparent',
        public string $bodyBackgroundColorDark = 'transparent',

        public ?string $maxWidth =  null, //'40rem',

        public WidgetAlignment $horizontalAlignment = WidgetAlignment::Center,
        public WidgetAlignment $verticalAlignment = WidgetAlignment::Center,

        public string $outerPaddingClasses = 'p-4',
        public string $backgroundClasses = 'bg-gray-200 dark:bg-gray-900',
        public string $borderRadiusClasses = 'rounded-lg',
        public string $borderClasses = 'border border-gray-300 dark:border-gray-700',
        public string $paddingClasses = 'p-4',
        public string $shadowClasses = 'shadow-sm',
    )
    {
    }
}
