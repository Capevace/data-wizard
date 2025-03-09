<?php

namespace App\Models\ExtractionRun;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Carbon;

enum RunStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Running = 'running';
    case Completed = 'completed';
    case Failed = 'failed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Running => __('Running'),
            self::Completed => __('Completed'),
            self::Failed => __('Failed'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Running => 'warning',
            self::Completed => 'success',
            self::Failed => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Running => 'heroicon-o-cog-8-tooth',
            self::Completed => 'heroicon-o-check',
            self::Failed => 'heroicon-o-exclamation-triangle',
        };
    }

    public function isIconSpinning(): bool
    {
        return $this === self::Running;
    }

    public function formatDate(Carbon $date): string
    {
        return match ($this) {
            default => "{$date->longAbsoluteDiffForHumans()}",
            self::Failed => $date->longAbsoluteDiffForHumans(),
        };
    }

    public function shouldPoll(): bool
    {
        return $this === self::Pending || $this === self::Running;
    }

    public function isDataEditable(): bool
    {
        return $this === self::Completed;
    }
}
