<?php

namespace App\Livewire\Components\EmbeddedExtractor;

enum ExtractorSteps: string
{
    case Introduction = 'introduction';
    case Bucket = 'bucket';
    case Extraction = 'extraction';
    case Results = 'results';

    public function getLabel(): string
    {
        return match ($this) {
            self::Introduction => 'Introduction',
            self::Bucket => 'Bucket',
            self::Extraction => 'Extraction',
            self::Results => 'Results',
        };
    }

    public function getDescription(): ?string
    {
        return null;
    }
}
