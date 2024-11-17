<?php

namespace App\Filament\Pages;

use App\Models\SavedExtractor;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Attributes\Locked;
use Mateffy\JsonSchema;
use Mateffy\JsonSchema\DynamicData;

class JsonSchemaPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.pages.schema';

    protected static ?string $slug = 'json-schema';

    #[Locked]
    public ?string $id = null;

    public array $data = [];

    public function mount()
    {
        $this->form->fill([
            'name' => 'Test',
            'price' => 100,
            'contacts' => [
                [
                    'name' => 'Lukas',
                    'email' => 'hello@mateffy.me',
                ],
            ],
            'names' => [
                'Lukas',
                'Tom',
            ]
        ]);
    }

    public function submit()
    {
        $this->form->getState();
    }

    public function form(Form $form): Form
    {
        $schema = JsonSchema::from([
            'type' => 'object',
            'properties' => [
                'name' => [
                    'type' => 'string',
                    'description' => 'The name of the product',
                ],
                'price' => [
                    'type' => 'number',
                    'description' => 'The price in EUR',
                ],
                'contacts' => [
                    'type' => 'array',
                    'magic_ui' => [
                        'label' => 'Contacts',
                        'placeholder' => 'e.g. John Doe',
                        'description' => 'The contacts of the product',
                    ],
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => [
                                'type' => 'string',
                                'description' => 'The name of the contact.',
                                'magic_ui' => [
                                    'label' => 'Your Name',
                                    'placeholder' => 'e.g. John Doe',
                                    'description' => 'The name of the contact. Click XYZ... This overwrites the default description for UI specific instructions.',
                                ],
                                "maxLength" => 3,
                            ],
                            'email' => [
                                'type' => 'string',
                                'format' => 'email',
                                'description' => 'The email of the contact',
                            ],
                            'gdpr_consent' => [
                                'type' => 'boolean',
                                'description' => 'GDPR consent',
                            ],
                        ],
                        'required' => ['name', 'email'],
                    ],
                ],
                'names' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                    ],
                ],
            ],
            'required' => ['name', 'price'],
        ]);

        return $form
            ->statePath('data')
            ->schema($schema->toFilamentFormSchema());
    }


    public function table(Table $table): Table
    {
        $schema = JsonSchema::from([
            'type' => 'object',
            'properties' => [
                'label' => [
                    'type' => 'string',
                ],
                'output_instructions' => [
                    'type' => 'string',
                ],
            ],
            'required' => ['label']
        ]);

        return $table
            ->query(SavedExtractor::query())
            ->recordAction('select')
            ->actions([
                Action::make('select')
                    ->label('Select')
                ->action(function (SavedExtractor $extractor) {
                    $this->id = $extractor->id;

                    $this->infolist->record($extractor);
                }),
            ])
            ->columns($schema->toFilamentTableColumns());
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $schema = JsonSchema::from([
            'type' => 'object',
            'properties' => [
                'label' => [
                    'type' => 'string',
                ],
                'output_instructions' => [
                    'type' => 'string',
                ],
            ],
            'required' => ['label']
        ]);

        if (! $this->id) {
            return $infolist;
        }

        $extractor = SavedExtractor::find($this->id);

        return $infolist
            ->record($extractor)
            ->schema($schema->toFilamentInfolistSchema());
    }
}
