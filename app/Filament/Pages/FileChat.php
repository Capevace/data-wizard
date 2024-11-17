<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ExtractionBucketResource;
use App\Filament\Resources\ExtractionBucketResource\Pages\ListExtractionBucketsInline;
use App\Models\ExtractionBucket;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Number;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Mateffy\Magic\Buckets\Functions\CreateFile;
use Mateffy\Magic\Buckets\Functions\ListFiles;
use Mateffy\Magic\Buckets\Functions\ReadRawFile;
use Mateffy\Magic\Buckets\Functions\SummarizeFile;
use Mateffy\Magic\Chat\FilamentToolWidget;
use Mateffy\Magic\Chat\HasChat;
use Mateffy\Magic\Chat\InteractsWithChat;
use Mateffy\Magic\Chat\ToolWidget;
use Mateffy\Magic\LLM\LLM;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Mateffy\Magic\LLM\Models\Claude3Family;
use Mateffy\Magic\Magic;

class FileChat extends Page implements HasForms, HasChat
{
    use InteractsWithForms;
    use InteractsWithChat {
        InteractsWithChat::renderChatMessage as _renderChatMessage;
        InteractsWithChat::getSystemPrompt as _getSystemPrompt;
    }

    protected static string $view = 'filament.pages.test-chat';

    protected static ?string $slug = 'file-chat';

    protected static ?string $title = 'File Chat';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public string $text = 'create a file with Hello World in it';

    #[Session]
    #[Locked]
    public string $bucketId;

    public function mount()
    {
        if (!isset($this->bucketId)) {
            $this->bucketId = ExtractionBucket::create()->id;
        }
    }

    #[Computed]
    public function bucket(): ExtractionBucket
    {
        return ExtractionBucket::findOrFail($this->bucketId);
    }

    public function getHeader(): ?View
    {
        return view('components.empty');
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('')
            ->schema([
                Textarea::make('text')
                    ->required()
                    ->label('Text')
                    ->translateLabel()
                    ->hiddenLabel()
                    ->placeholder('Enter your text here')
                    ->autosize()
                    ->extraInputAttributes([
                        'class' => 'h-full',
                        'style' => 'min-height: 6rem',
                        'x-on:keydown.enter.prevent.stop' => '$wire.send($wire.text); $wire.text = \'\'',
                        'x-on:keydown.shift.enter.prevent' => '$wire.text += \'\n\'',
                    ]),
            ]);
    }

    protected function getLLM(): LLM
    {
        return Claude3Family::haiku();
    }

    protected function getSystemPrompt(): string
    {
        $prompt = self::_getSystemPrompt();

        return <<<PROMPT
            $prompt

        PROMPT;
    }

    protected static function getToolWidgets(): array
    {
        return [
            ListFiles::$name => ToolWidget::table(
                label: 'Files in this bucket',
                description: fn (?FunctionOutputMessage $output) => $output
                    ? "There are {$output->output->count()} files in this bucket."
                    : null,
                icon: 'heroicon-o-table-cells',
                color: 'warning',
                columns: fn () => [
                    'name' => 'Name',
                    'mime_type' => 'Mime Type',
                    'size' => 'Size',
                    'ai_summary' => 'AI Summary',
                ],
                rows: fn (?FunctionOutputMessage $output) => $output?->output
            ),
            SummarizeFile::$name => ToolWidget::loading(
                done: fn (FunctionOutputMessage $output) => "{$output->output['name']} – {$output->output['ai_summary']}",
                doneIcon: 'heroicon-o-document-text',
                doneIconColor: 'gray',
            ),
            CreateFile::$name => ToolWidget::loading(
                done: fn (FunctionOutputMessage $output) => "{$output->output['name']}.{$output->output['extension']} – " . Number::fileSize($output->output['size']),
                doneIcon: 'heroicon-o-document-plus',
                doneIconColor: 'gray',
                variant: 'detailed',
            ),
        ];
    }

    protected function getTools(): array
    {
        return [
            new CreateFile($this->bucket),
            new ListFiles($this->bucket),
            new SummarizeFile($this->bucket),
            new ReadRawFile($this->bucket),
        ];
    }

    #[On('resetChat')]
    public function onResetChat()
    {
        $this->bucketId = ExtractionBucket::create()->id;

        unset($this->bucket);
    }
}
