<?php

namespace App\Models;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ArtifactGenerationStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Complete = 'complete';
    case Failed = 'failed';

    public function getIcon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::InProgress => 'heroicon-o-cog-8-tooth',
            self::Complete => 'heroicon-o-check-circle',
            self::Failed => 'heroicon-o-x-circle',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::InProgress => 'info',
            self::Complete => 'success',
            self::Failed => 'danger',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::InProgress => 'In Progress',
            self::Complete => 'Complete',
            self::Failed => 'Failed',
        };
    }
}
