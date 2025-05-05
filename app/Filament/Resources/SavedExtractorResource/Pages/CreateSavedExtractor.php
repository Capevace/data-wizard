<?php

namespace App\Filament\Resources\SavedExtractorResource\Pages;

use App\Filament\Resources\SavedExtractorResource;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Attributes\Locked;

class CreateSavedExtractor extends CreateRecord
{
    protected static string $resource = SavedExtractorResource::class;

    #[Locked]
    public array $initial_data = [];

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function afterFill()
    {
        $this->form->fill($this->initial_data);
    }
}
