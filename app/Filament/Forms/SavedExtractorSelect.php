<?php

namespace App\Filament\Forms;

use App\Models\SavedExtractor;
use Filament\Forms\Components\Select;

class SavedExtractorSelect extends Select
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->columnSpan(1)
            ->required()
            ->label('Extractor')
            ->translateLabel()
            ->searchable()
            ->searchDebounce(200)
            ->options(fn () => SavedExtractor::query()
                ->latest('updated_at')
                ->limit(10)
                ->get()
                ->pluck('label', 'id')
            )
            ->getSearchResultsUsing(fn (?string $search) => SavedExtractor::query()
                ->when($search, fn ($query) => $query->where('label', 'like', "%{$search}%"))
                ->latest('updated_at')
                ->limit(10)
                ->get()
                ->pluck('label', 'id')
            );
    }
}
