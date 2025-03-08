<?php

namespace App\Models\ExtractionBucket;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum BucketCreationSource: string implements HasLabel, HasIcon, HasColor
{
    case App = 'app';
    case Embed = 'embed';
    case Api = 'api';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::App => 'success',
            self::Embed => 'primary',
            self::Api => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::App => 'heroicon-o-document-text',
            self::Embed => 'heroicon-o-document-text',
            self::Api => 'heroicon-o-document-text',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::App => __('Via App'),
            self::Embed => __('Via Extraction Embed'),
            self::Api => __('Via API'),
        };
    }
}
