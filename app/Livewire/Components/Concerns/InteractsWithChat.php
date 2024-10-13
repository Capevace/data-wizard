<?php

namespace App\Livewire\Components\Concerns;

use App\Livewire\Components\StreamableMessage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Session;
use Mateffy\Magic\Artifacts\Artifact;
use Mateffy\Magic\Builder\ChatPreconfiguredModelBuilder;
use Mateffy\Magic\LLM\LLM;
use Mateffy\Magic\LLM\Message\FunctionCall;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Message\MultimodalMessage;
use Mateffy\Magic\LLM\Message\MultimodalMessage\Base64Image;
use Mateffy\Magic\LLM\Message\MultimodalMessage\Text;
use Mateffy\Magic\LLM\Message\TextMessage;
use Mateffy\Magic\LLM\MessageCollection;
use Mateffy\Magic\LLM\Models\Claude3Family;
use Mateffy\Magic\Magic;
use Mateffy\Magic\Prompt\TokenStats;

/**
 * @property-read ChatPreconfiguredModelBuilder $magic
 */
trait InteractsWithChat
{
    #[Locked]
    public string $conversationId;


    #[Session]
    public array $chat_messages = [];

    protected array $temporary_messages = [];

    public function mountInteractsWithChat()
    {
        $this->conversationId = Str::uuid()->toString();

//        $lookupCall = new FunctionCall(
//            name: 'lookupLocation',
//            arguments: ['query' => 'Brandenburger Tor'],
//            id: 'lookupLocation',
//        );
//
//        $queryCall = new FunctionCall(
//            name: 'queryAvailableRentables',
//            arguments: [
//                'location' => [
//                    'type' => 'Point',
//                    'coordinates' => [
//                        52.5167,
//                        13.3833,
//                    ],
//                    'radius_in_meters' => 1000,
//                ],
//                'areas' => [
//                    ['usage' => 'office']
//                ]
//            ],
//            id: 'queryAvailableRentables',
//        );
//
//        $this->chat_messages = [
//            TextMessage::user('Are there any free office spaces in the near the Brandenburger Tor?'),
//            FunctionInvocationMessage::call($lookupCall),
//            FunctionOutputMessage::output($lookupCall, [
//                'latitude' => 52.5167,
//                'longitude' => 13.3833,
//            ]),
//            FunctionInvocationMessage::call($queryCall),
//            FunctionOutputMessage::output($queryCall, [
//                'location' => ['latitude' => 52.5167, 'longitude' => 13.3833],
//                'rentables' => [
//                    [
//                        'id' => '1234',
//                        'name' => 'Office 1',
//                        'description' => 'This is an office',
//                        'address' => '123 Main St, New York, NY 10001',
//                        'images' => [
//                            'https://example.com/image1.jpg',
//                            'https://example.com/image2.jpg',
//                        ],
//                        'area' => 100,
//                        'features' => ['balcony', 'terrace'],
//                        'floorplans' => [
//                            'https://example.com/floorplan1.jpg',
//                            'https://example.com/floorplan2.jpg',
//                        ],
//                        'areas' => [
//                            [
//                                'id' => '5678',
//                                'name' => 'Office Space',
//                                'type' => 'office',
//                                'area' => 100,
//                                'features' => ['balcony', 'terrace'],
//                                'images' => [
//                                    'https://example.com/image3.jpg',
//                                    'https://example.com/image4.jpg',
//                                ]
//                            ],
//                        ],
//                    ],
//                ]
//            ]),
//            TextMessage::assistant('Yes, there is an office space close to the Brandenburger Tor. It is located at 123 Main St, New York, NY 10001. The space is 100 square meters and has balcony and a terrace.'),
//        ];
    }
    public function getChatMessages(): MessageCollection
    {
        return MessageCollection::make($this->chat_messages);
    }

    public static function renderChatMessage(Message $message, bool $streaming = false, bool $isCurrent = false): ?string
    {
        return view('components.chat.message', [
            'message' => $message,
            'streaming' => $streaming,
            'isCurrent' => $isCurrent,
        ])->render();
    }

    public function getChatStatePath(): string
    {
        return 'chat_state';
    }

    #[Computed]
    public function magic(): ChatPreconfiguredModelBuilder
    {
        return Magic::chat()
            ->model(Claude3Family::sonnet_3_5())
            ->system($this->getSystemPrompt())
            ->messages($this->chat_messages)
            ->onMessageProgress(fn (Message $message) => $this->onMessageProgress($message))
            ->onMessage(fn (Message $message) => $this->onMessage($message))
            ->onTokenStats(fn (TokenStats $stats) => $this->onTokenStats($stats))
            ->tools($this->getTools());
    }

    public function send(string $text, array $files = []): void
    {
        $chat = $this->magic;

        // Upload files
        /** @var Artifact[] $artifacts */
        $artifacts = [];

        /** @var array<MultimodalMessage\ContentInterface> $messageContent */
        $messageContent = [
            Text::make($text)
        ];

        foreach ($artifacts as $artifact) {
            foreach ($artifact->getContents() as $content) {
                $messageContent[] = $artifact->getBase64Image($content);
            }
        }

        $message = MultimodalMessage::user($messageContent);
        $this->chat_messages[] = $message;
        $chat->addMessage($message);

        // Todo: Instead of this, we should now start a job and open a websocket connection to receive updates for streaming
        $this->js('
            $wire.$dispatch("startPolling");
            setTimeout(() => $wire.start(), 1000);
        ');
    }

    public function sendForm(array $data): void
    {
        $chat = $this->magic;

        /** @var array<MultimodalMessage\ContentInterface> $messageContent */
        $messageContent = [
            Text::make("Form submitted with data: " . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)),
        ];

        $message = MultimodalMessage::user($messageContent);
        $this->chat_messages[] = $message;
        $chat->addMessage($message);

        // Todo: Instead of this, we should now start a job and open a websocket connection to receive updates for streaming
        $this->js('
            $wire.$dispatch("startPolling");
            setTimeout(() => $wire.start(), 1000);
        ');
    }

    public function start(): void
    {
//        dd($this->chat_messages, $this->magic->messages);
        $this->magic->stream();

        $this->dispatch('stopPolling')->to(StreamableMessage::class);
        $this->temporary_messages = [];
    }

    protected function onMessageProgress(Message $message): void
    {
        $index = max(0, count($this->temporary_messages) - 1);
        $this->temporary_messages[$index] = $message;

        StreamableMessage::put($this->conversationId, $this->temporary_messages);
    }

    protected function onMessage(Message $message): void
    {
        $index = count($this->temporary_messages) - 1;
        $this->temporary_messages[$index] = $message;
        $this->temporary_messages[] = null;

        StreamableMessage::put($this->conversationId, $this->temporary_messages);

        $this->chat_messages[] = $message;
    }

    protected function onTokenStats(TokenStats $stats): void
    {
    }

    protected function getLLM(): LLM
    {
        throw new \Exception('Not implemented');
    }

    protected function getSystemPrompt(): string
    {
        return 'You are a helpful chatbot. You are given some tools to use when appropriate. Give shorter, more concise answers than you would normally do.';
    }
    protected function getTools(): array
    {
        return [];
    }
}
