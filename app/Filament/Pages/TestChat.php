<?php

namespace App\Filament\Pages;

use App\Livewire\Components\Concerns\HasChat;
use App\Livewire\Components\Concerns\InteractsWithChat;
use App\Livewire\Components\Concerns\ToolWidget;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Mateffy\Magic\LLM\LLM;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Mateffy\Magic\LLM\Models\Claude3Family;
use Mateffy\Magic\Magic;

class TestChat extends Page implements HasForms, HasChat
{
    use InteractsWithForms;
    use InteractsWithChat {
        InteractsWithChat::renderChatMessage as _renderChatMessage;
        InteractsWithChat::getSystemPrompt as _getSystemPrompt;
    }

    protected static string $view = 'filament.pages.test-chat';

    protected static ?string $slug = 'test-chat';

    public string $text = 'find office spaces near brandenburger tor please';

    public array $estates = [
        ['name' => 'Office building ABC', 'address' => '123 Main St, New York, NY 10001'],
        ['name' => 'Elsenstrasse', 'address' => 'Elsenstrasse 67, Berlin']
    ];

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
        return self::_getSystemPrompt() . "\n\n" .
            'You can output information in a table using the outputTable tool. Do this for example for search results.' .
            "\n\n" .
            'You can also output a form using the outputForm tool. You can do this to show the user a form to fill in, which will be submitted to you when the form is submitted, allowing you to run tools on the data.';
    }

    protected static function getToolWidgets(): array
    {
        return [
//            'sendMail' => ToolWidget::livewire(SendMail::class),
            'createEstate' => ToolWidget::confirmation(text: 'Are you sure you want to create an estate?'),
            'queryAvailableRentables' => ToolWidget::map(
                center: fn (?FunctionOutputMessage $output) => $output?->output['center'] ?? [52.5167, 13.3833],
                markers: fn (?FunctionOutputMessage $output) => collect($output?->output['rentables'] ?? [])
                    ->map(fn (array $rentable) => [
                        'coordinates' => $rentable['coordinates'] ?? [52.5167, 13.3833],
                        'label' => $rentable['name'],
                    ]),
                loading: ToolWidget::loading(
                    loading: 'Finding locations...',
                ),
                useOutput: true,
            ),
            'lookupLocation' => ToolWidget::map(useOutput: true),
            'outputTable' => ToolWidget::table(
                description: 'This is a table',
                icon: 'heroicon-o-table-cells',
                color: 'warning',
            ),
            'outputVideo' => ToolWidget::youtube(),
        ];
    }

    protected function getTools(): array
    {
        return [
            /**
             * @description The rows object parameter is a key-value pair of column slug and column value.
             * @type $columns {"type": "object", "additionalProperties": {"type": "string"}}
             * @description $columns Columns is an object with a column slug as the key and a column label as the value. For example: {"name": "Name", "area": "Area (m2)"}
             * @type $rows {"type": "object"}
             * @description $rows An object with key-value pairs of column slug and column value. For example: {"name": "Example Area", "area": 123.12}
             */
            'outputTable' => fn (
                string $label,
                string $description,
                ?string $heroicon,
                array $columns,
                array $rows
            ) => Magic::end([
                'label' => $label,
                'description' => $description,
                'icon' => $heroicon,
                'headerColumns' => $columns,
                'rows' => $rows,
            ]),
            /**
             * @description This is a function that takes an integer and returns an object with a property how_much_do_i_love_her
             * @type $how_much_do_i_love_her {"type": "object", "properties": {"how_much_do_i_love_her": {"type": "integer"}}}
             */
            'ande' => function (array $how_much_do_i_love_her) {
                return Magic::end([
                    'how_much_do_i_love_her' => $how_much_do_i_love_her,
                ]);
            },
            'sendMail' => fn (string $to, string $subject, string $body) => Magic::end([
                'to' => $to,
                'subject' => $subject,
                'body' => $body,
            ]),
            /**
             * @description You can output a video by providing an embeddable URL to the outputVideo tool. We need the full URL, including https://. The following base URLs are supported: youtube.com, youtu.be, vimeo.com, open.spotify.com and player.twitch.tv.
             */
            'outputVideo' => fn (string $url) => Magic::end([
                'url' => $url,
            ]),
//            'findEstate' => fn (string $search) => [
//                'estates' => collect($this->estates)
//                    ->filter(function (array $estate) use ($search) {
//                        $score = 0;
//
//                        // Search with similar_text
//                        similar_text($search, $estate['name'], $score);
//
//                        if ($score >= 50) {
//                            return true;
//                        }
//
//                        similar_text($search, $estate['address'], $score);
//
//                        if ($score >= 50) {
//                            return true;
//                        }
//
//                        return false;
//                    })
//                    ->all()
//            ],
            'createEstate' => fn (string $name, string $address, bool $runLocationAnalysis) => Magic::end([
                'name' => $name,
                'address' => $address,
                'runLocationAnalysis' => $runLocationAnalysis,
            ]),
            'lookupLocation' => function (string $query) {
                sleep(2);

                return [
                    'center' => [52.5167, 13.3833],
                    'zoom' => 13,
                    'markers' => [
                        [
                            'coordinates' => [52.5167, 13.3833],
                            'label' => 'Brandenburger Tor',
                            'color' => '#ff0000',
                        ],
                    ],
                ];
            },

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
                    'center' => [52.5167, 13.3833],
                    'rentables' => [
                        [
                            'id' => '1234',
                            'name' => 'Bürofläche 1',
                            'description' => 'Die Bürofläche ist eine große, leicht zugängliche, mit einem breiten Raum und einer großen Ausgabe für den Arbeitsplatz.',
                            'address' => 'Am Borsigturm 100, 13507 Berlin',
                            // We use locations around the center, but not exactly the center
                            'coordinates' => [52.516732, 13.3838],
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
                            'address' => 'Friedrichstraße 100, 10117 Berlin',
                            'coordinates' => [52.5163, 13.38325],
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

//    /**
//     * @throws Throwable
//     */
//    public static function renderChatMessage(Message $message, bool $streaming = false, bool $isCurrent = false): string|ToolWidget|null
//    {
//        if ($message instanceof FunctionOutputMessage) {
//            return null;
//        }
//
//        if ($message instanceof FunctionInvocationMessage && $widget = Arr::get(static::getToolWidgets(), $message->call->name)) {
//            return $widget;
////
////            if ($widget && $tool = Arr::get($this->magic->tools, $message->call->name)) {
////                $output = MessageCollection::make($this->chat_messages)->lastFunctionOutput(fn (FunctionOutputMessage $output) => $message->call->id && $message->call->id === $output->call->id);
////
////                return $widget->render(
////                    tool: $tool,
////                    invocation: $message,
////                    output: $output
////                );
////            }
//        }
//
//
//        if ($message instanceof FunctionInvocationMessage && $message->call->name === 'queryAvailableRentables') {
//            if ($isCurrent) {
//                return 'Searching...';
//            }
//
//            return null;
//        } else if ($message instanceof FunctionInvocationMessage && $message->call->name === 'outputTable') {
//            return null;
//        }
//
//        if ($message instanceof FunctionOutputMessage && $message->call->name === 'outputForm') {
//            return Blade::render(<<<'BLADE'
//                <form class="w-full py-1 mb-5" x-data="{ data: {} }" wire:submit.prevent="sendForm(data)">
//                    <div class="grid grid-cols-1 gap-2" >
//                        <x-resource.json-schema :schema="$schema" state-path="data" />
//                    </div>
//
//                    <nav class="flex justify-end py-3">
//                        <x-filament::button
//                            type="submit"
//                            color="primary"
//                        >
//                            Submit
//                        </x-filament::button>
//                    </nav>
//                </form>
//            BLADE, ['schema' => $message->output['schema'][0]]);
//        }
//
//        if ($message instanceof FunctionOutputMessage && $message->call->name === 'createEstate') {
//            return Blade::render(<<<'BLADE'
//                <form class="w-full py-1 mb-5" x-data="{ data: @js($output) }" wire:submit.prevent="sendForm(data)">
//                    <div class="grid grid-cols-1 gap-2" >
//                        <x-resource.json-schema
//                            :schema="[
//                                'type' => 'object',
//                                'properties' => [
//                                    'name' => [
//                                        'type' => 'string',
//                                        'description' => 'The name of the property',
//                                        'magic_ui' => [
//                                            'component' => 'text',
//                                            'label' => 'Name of the property',
//                                            'hint' => 'The name of the property',
//                                        ],
//                                    ],
//                                    'address' => [
//                                        'type' => 'string',
//                                        'description' => 'The address of the estate',
//                                    ],
//                                    'runLocationAnalysis' => [
//                                        'type' => 'boolean',
//                                        'description' => 'Run the location analysis',
//                                        'magic_ui' => [
//                                            'component' => 'toggle',
//                                            'label' => 'Run location analysis',
//                                            'hint' => 'Run the location analysis',
//                                        ],
//                                    ],
//                                ],
//                            ]"
//                            state-path="data"
//                        />
//                    </div>
//
//                    <nav class="flex justify-end py-3">
//                        <x-filament::button
//                            type="submit"
//                            color="success"
//                        >
//                            Cancel
//                        </x-filament::button>
//                        <x-filament::button
//                            type="submit"
//                            color="success"
//                        >
//                            Submit
//                        </x-filament::button>
//                    </nav>
//                </form>
//            BLADE, ['output' => $message->output]);
//        }
//
//        if ($message instanceof FunctionOutputMessage && $message->call->name === 'outputTable') {
//            return Blade::render(<<<BLADE
//                <table class="table table-auto w-full text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-800 rounded-lg px-5 py-3 shadow-sm  mb-5">
//                    <thead>
//                        <tr>
//                            @foreach(\$headerColumns as \$headerColumn)
//                                <th class="px-4 py-2">{{ \$headerColumn }}</th>
//                            @endforeach
//                        </tr>
//                    </thead>
//                    <tbody>
//                        @foreach(\$rows as \$row)
//                            <tr>
//                                @foreach(\$row as \$cell)
//                                    <td class="px-4 py-2">{{ \$cell }}</td>
//                                @endforeach
//                            </tr>
//                        @endforeach
//                    </tbody>
//                </table>
//            BLADE, ['headerColumns' => $message->output['headerColumns'], 'rows' => $message->output['rows']]);
//        }
//
//        return self::_renderChatMessage($message, $streaming, $isCurrent);
//    }
}
