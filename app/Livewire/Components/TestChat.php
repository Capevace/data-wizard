<?php

namespace App\Livewire\Components;

use App\Livewire\Components\Concerns\HasChat;
use App\Livewire\Components\Concerns\InteractsWithChat;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Blade;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Component;
use Mateffy\Magic\LLM\LLM;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Models\Claude3Family;
use Mateffy\Magic\Magic;

class TestChat extends Component implements HasForms, HasChat
{
    use InteractsWithForms;
    use InteractsWithChat {
        InteractsWithChat::renderChatMessage as _renderChatMessage;
        InteractsWithChat::getSystemPrompt as _getSystemPrompt;
    }

    public string $text = 'Are there any free office spaces in the near the Brandenburger Tor?';

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
        return self::_getSystemPrompt() . "\n\n" .
            'You can output information in a table using the outputTable tool. Do this for example for search results.' .
            "\n\n" .
            'You can also output a form using the outputForm tool. You can do this to show the user a form to fill in, which will be submitted to you when the form is submitted, allowing you to run tools on the data.';
    }

    protected function getTools(): array
    {
        return [
            'ande' => function (int $how_much_do_i_love_her) {
                return Magic::end([
                    'how_much_do_i_love_her' => $how_much_do_i_love_her,
                ]);
            },
            'sendMail' => fn (string $to, string $subject, string $body) => Magic::end([
                'to' => $to,
                'subject' => $subject,
                'body' => $body,
            ]),
            'createEstate' => fn (string $name, string $address, bool $runLocationAnalysis) => Magic::end([
                'name' => $name,
                'address' => $address,
                'runLocationAnalysis' => $runLocationAnalysis,
            ]),
            'lookupLocation' => fn (string $query) => [
                'latitude' => 52.5167,
                'longitude' => 13.3833,
            ],
            'outputTable' => fn (array $headerColumns, array $rows) => Magic::end([
                'headerColumns' => $headerColumns,
                'rows' => $rows,
            ]),
            'outputForm' => fn (array $jsonSchemaObject, ?array $initialData = null) => Magic::end([
                'schema' => $jsonSchemaObject,
                'initialData' => $initialData,
                'actions' => [
                    'submit' => 'Submit',
                ]
            ]),
            'queryAvailableRentables' => function (array|string $location, array $areas) {
                sleep(2);

                return [
                    'location' => $location,
                    'rentables' => [
                        [
                            'id' => '1234',
                            'name' => 'Office 1',
                            'description' => 'This is an office',
                            'address' => '123 Main St, New York, NY 10001',
                            'images' => [
                                'https://example.com/image1.jpg',
                                'https://example.com/image2.jpg',
                            ],
                            'area' => 100,
                            'features' => ['balcony', 'terrace'],
                            'floorplans' => [
                                'https://example.com/floorplan1.jpg',
                                'https://example.com/floorplan2.jpg',
                            ],
                            'areas' => [
                                [
                                    'id' => '5678',
                                    'name' => 'Office Space',
                                    'type' => 'office',
                                    'area' => 100,
                                    'features' => ['balcony', 'terrace'],
                                    'images' => [
                                        'https://example.com/image3.jpg',
                                        'https://example.com/image4.jpg',
                                    ]
                                ],
                            ],
                        ],
                        [
                            'id' => '9876',
                            'name' => 'Office Space 2',
                            'description' => 'This is an office',
                            'address' => '123 Main St, New York, NY 10001',
                            'images' => [
                                'https://example.com/image3.jpg',
                                'https://example.com/image4.jpg',
                            ],
                            'area' => 200,
                            'features' => ['balcony', 'terrace'],
                            'floorplans' => [
                                'https://example.com/floorplan1.jpg',
                                'https://example.com/floorplan2.jpg',
                            ],
                            'areas' => [
                                [
                                    'id' => '5678',
                                    'name' => 'Office Space',
                                    'type' => 'office',
                                    'area' => 100,
                                    'features' => ['balcony', 'terrace'],
                                    'images' => [
                                        'https://example.com/image3.jpg',
                                        'https://example.com/image4.jpg',
                                    ]
                                ],
                            ],
                        ],
                    ]
                ];
            },
        ];
    }

    public static function renderChatMessage(Message $message, bool $streaming = false, bool $isCurrent = false): ?string
    {
        if ($message instanceof \Mateffy\Magic\LLM\Message\FunctionInvocationMessage && $message->call->name === 'queryAvailableRentables') {
            if ($isCurrent) {
                return 'Searching...';
            }

            return null;
        } else if ($message instanceof \Mateffy\Magic\LLM\Message\FunctionInvocationMessage && $message->call->name === 'outputTable') {
            return null;
        }

        if ($message instanceof \Mateffy\Magic\LLM\Message\FunctionOutputMessage && $message->call->name === 'outputForm') {
            return Blade::render(<<<'BLADE'
                <form class="w-full py-1 mb-5" x-data="{ data: {} }" wire:submit.prevent="sendForm(data)">
                    <div class="grid grid-cols-1 gap-2" >
                        <x-resource.json-schema :schema="$schema" state-path="data" />
                    </div>

                    <nav class="flex justify-end py-3">
                        <x-filament::button
                            type="submit"
                            color="primary"
                        >
                            Submit
                        </x-filament::button>
                    </nav>
                </form>
            BLADE, ['schema' => $message->output['schema'][0]]);
        }

        if ($message instanceof \Mateffy\Magic\LLM\Message\FunctionOutputMessage && $message->call->name === 'outputTable') {
            return Blade::render(<<<BLADE
                <table class="table table-auto w-full text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-800 rounded-lg px-5 py-3 shadow-sm  mb-5">
                    <thead>
                        <tr>
                            @foreach(\$headerColumns as \$headerColumn)
                                <th class="px-4 py-2">{{ \$headerColumn }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(\$rows as \$row)
                            <tr>
                                @foreach(\$row as \$cell)
                                    <td class="px-4 py-2">{{ \$cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            BLADE, ['headerColumns' => $message->output['headerColumns'], 'rows' => $message->output['rows']]);
        }

        return self::_renderChatMessage($message, $streaming, $isCurrent);
    }

    public function render()
    {
        return view('livewire.test-chat');
    }
}
