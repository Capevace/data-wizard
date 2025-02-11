<?php

namespace Mateffy\Magic\Chat\Livewire;

use App\Filament\Resources\ExtractionBucketResource;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Attributes\Locked;
use Livewire\Component;

class FakeLivewire extends Component implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    #[Locked]
    public string $resource;

    public static function table(Table $table): Table
    {
        $resource = $table->getLivewire()->resource;

        return $resource::table($table)
            ->query(fn () => $resource::getModel()::query());
    }
	
	public function form(Form $form): Form
	{
		return $this->resource::form($form);
	}
}
