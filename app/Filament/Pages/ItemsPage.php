<?php

namespace App\Filament\Pages;

use App\Models\SavedExtractor;
use App\Models\SmartCollection;
use App\Models\SmartCollectionItem;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Mateffy\JsonSchema;
use Mateffy\JsonSchema\DynamicData;

/**
 * @property-read SmartCollection $smart_collection
 */
class ItemsPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.pages.items';

    protected static ?string $slug = 'collection/{collectionId}/items';

    public string $collectionId;

    #[Computed]
    public function smart_collection(): SmartCollection
    {
        return SmartCollection::findOrFail($this->collectionId);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->smart_collection->items())
            ->headerActions([
                CreateAction::make()
                    ->slideOver()
                    ->form(fn () => $this->smart_collection->json_schema->toFilamentFormSchema())
                    ->mutateFormDataUsing(fn (array $data) => [
                        'title' => Arr::get($data, 'title') ?? Arr::get($data, 'name') ?? Arr::get($data, 'label') ?? '',
                        'data' => $data
                    ])
                    ->using(fn (array $data) => $this->smart_collection->items()->create($data)),
            ])
            ->recordAction('edit')
            ->actions([
                EditAction::make()
                    ->slideOver()
                    ->fillForm(fn (SmartCollectionItem $record) => $record->data)
                    ->form(fn () => $this->smart_collection->json_schema->toFilamentFormSchema())
                    ->mutateFormDataUsing(fn (array $data) => [
                        'title' => Arr::get($data, 'title') ?? Arr::get($data, 'name') ?? Arr::get($data, 'label') ?? '',
                        'data' => $data
                    ])
                    ->using(fn (SmartCollectionItem $record, array $data) => $record->update($data)),
            ])
            ->columns($this->smart_collection->json_schema->toFilamentTableColumns('data'));
    }

    public static function getNavigationItems(): array
    {
        return SmartCollection::query()
            ->latest('updated_at')
            ->get()
            ->map(fn (SmartCollection $smart_collection) => NavigationItem::make($smart_collection->title)
                ->icon($smart_collection->icon)
                ->isActiveWhen(fn () => request()->routeIs(ItemsPage::getRouteName()) && request()->route('collectionId') === $smart_collection->id)
                ->url(ItemsPage::getUrl(['collectionId' => $smart_collection->id]))
            )
            ->toArray();
    }
}
