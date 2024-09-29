<?php

namespace App\Filament\Resources\SavedExtractorResource;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\View;

class StepLabelsForm
{
    public static function schema(string $prefix, bool $optionalStep = false, array $buttons = []): array
    {
        return [
            Group::make()
                ->schema([
                    TextInput::make("{$prefix}_view_heading")
                        ->label("Heading")
                        ->translateLabel()
                        ->placeholder(__("default_{$prefix}_view_heading")),
                    RichEditor::make("{$prefix}_view_description")
                        ->label("Description")
                        ->translateLabel()
                        ->placeholder(str(__("default_{$prefix}_view_description"))->replace('</p>', "\n")->stripTags()),
                ]),

            Group::make()
                ->schema([
                    Toggle::make("{$prefix}_view_visible")
                        ->label("Visible")
                        ->translateLabel()
                        ->default(true)
                        ->hidden(!$optionalStep),

                    ...$buttons,
                ]),

            View::make("components.content.image")
                ->viewData([
                    "src" => "/images/ui/results.png",
                ]),
        ];
    }
}
