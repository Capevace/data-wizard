<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ExtractionRunResource;
use App\Filament\Resources\SavedExtractorResource;
use Illuminate\Support\Arr;
use Mateffy\Magic;
use Mateffy\Magic\Builder\ChatPreconfiguredModelBuilder;
use Mateffy\Magic\Chat\HasChat;
use Mateffy\Magic\Chat\InteractsWithChat;
use Mateffy\Magic\Chat\Tool;
use Mateffy\Magic\Chat\Widgets\Basic\LivewireToolWidget;
use Mateffy\Magic\Chat\Widgets\Basic\ToolWidget;
use Mateffy\Magic\Chat\Widgets\Prebuilt\FilamentToolWidget;
use Mateffy\Magic\Models\Gemini;
use Filament\Pages\Page;

class ChatTest extends Page implements HasChat
{
    use InteractsWithChat;

    protected static ?string $slug = 'chat-test';
    protected static string $view = 'filament.pages.chat';

    public string $input = '';

    public function getMagic(): ChatPreconfiguredModelBuilder
    {
        return static::getMagic()
            ->model(Gemini::flash_2_lite());
    }

    protected static function getTools(): array
    {
        return [
            ...Magic\Chat\Tools\Filament\FilamentTool::crud(
                resource: SavedExtractorResource::class,
                list: SavedExtractorResource\Pages\ListSavedExtractors::class,
                view: SavedExtractorResource\Pages\EditSavedExtractor::class,
                create: SavedExtractorResource\Pages\CreateSavedExtractor::class,
                edit: SavedExtractorResource\Pages\EditSavedExtractor::class,
            ),
            ...Magic\Chat\Tools\Filament\FilamentTool::crud(
                resource: ExtractionRunResource::class,
                list: ExtractionRunResource\Pages\ListExtractionRuns::class,
                view: ExtractionRunResource\Pages\EditExtractionRun::class,
            )
        ];
    }

    public function sendMessage()
    {
        $this->send($this->input);

        $this->input = '';
    }

    public function clear()
    {
        $this->input = '';
        $this->chat_messages = [];
        $this->temporary_messages = [];
    }
}
