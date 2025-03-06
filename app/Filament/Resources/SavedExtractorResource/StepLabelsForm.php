<?php

namespace App\Filament\Resources\SavedExtractorResource;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\View;

class StepLabelsForm
{
    public static function schema(string $prefix, bool $optionalStep = false, array $buttons = []): array
    {
        return [
            Section::make('Heading')
                ->hiddenLabel()
                ->translateLabel()
                ->icon('bi-fonts')
                ->description(__('The heading is shown at the top of the page.'))
                ->aside()
                ->schema([
                    TextInput::make("{$prefix}_view_heading")
                        ->label("Heading")
                        ->translateLabel()
                        ->placeholder(__("default_{$prefix}_view_heading")),
                ]),

            Section::make('Description')
                ->hiddenLabel()
                ->translateLabel()
                ->icon('bi-text-left')
                ->description(__('The description text should instruct the user what this is about, and what they should do next.'))
                ->aside()
                ->schema([
                    RichEditor::make("{$prefix}_view_description")
                        ->label("Description")
                        ->translateLabel()
                        ->placeholder(str(__("default_{$prefix}_view_description"))->replace('</p>', "\n")->stripTags()),
                ]),

            Section::make('Visibility')
                ->translateLabel()
                ->icon('heroicon-o-eye')
                ->aside()
                ->hidden(fn () => !$optionalStep)
                ->schema([
                    Toggle::make("{$prefix}_view_visible")
                        ->label("Visible")
                        ->translateLabel()
                        ->default(true)
                ]),

            Section::make('Button Labels')
                ->translateLabel()
                ->icon('bi-input-cursor-text')
                ->description(__('Button labels can be customized to better integrate with your application.'))
                ->aside()
                ->hidden(fn () => empty($buttons))
                ->schema($buttons),

//            View::make("components.content.image")
//                ->viewData([
//                    "src" => "/images/ui/results.png",
//                ]),
        ];
    }
}
