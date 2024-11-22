<?php

namespace Mateffy\Magic\Chat;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Session;
use Mateffy\Magic\Artifacts\Artifact;
use Mateffy\Magic\Builder\ChatPreconfiguredModelBuilder;
use Mateffy\Magic\Chat\Livewire\StreamableMessage;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\Functions\MagicFunction;
use Mateffy\Magic\LLM\ElElEm;
use Mateffy\Magic\LLM\LLM;
use Mateffy\Magic\LLM\Message\FunctionCall;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Message\MultimodalMessage;
use Mateffy\Magic\LLM\Message\MultimodalMessage\Text;
use Mateffy\Magic\LLM\MessageCollection;
use Mateffy\Magic\LLM\Models\Claude3Family;
use Mateffy\Magic\Magic;
use Mateffy\Magic\Prompt\Role;
use Mateffy\Magic\Prompt\TokenStats;

/**
 * @property-read ChatPreconfiguredModelBuilder $magic
 */
trait InteractsWithChat
{
    #[Locked]
    public string $conversationId;

    #[Session]
    public string $llm_model = 'openai/gpt-4o-mini';

    #[Session('wtf')]
    public array $chat_messages = [];

    protected array $temporary_messages = [];

    public function updatedModel()
    {
        if (!Magic::models()->keys()->contains($this->model)) {
            $this->model = 'openai/gpt-4o-mini';
        }
    }

    public function mountInteractsWithChat()
    {
        $this->conversationId = Str::uuid()->toString();
    }

    public function continueAfterInterrupt(?array $data = null): void
    {
        $chat_messages = MessageCollection::make($this->chat_messages);
        $last = $chat_messages->last();

        if ($last instanceof FunctionInvocationMessage) {
            $this->delayHandle();
        }
    }

    public function cancelAfterInterrupt(): void
    {
        $last = $this->chat_messages->last();

        if ($last instanceof FunctionInvocationMessage) {
            $this->chat_messages->push(FunctionOutputMessage::canceled($last->call));

            $this->delayStart();
        }
    }

    public function getChatMessages(): MessageCollection
    {
        return MessageCollection::make($this->chat_messages);
    }

    public function renderMessage(Message $message): string|ToolWidget|null
    {
        $result = static::renderChatMessage(message:$message);

        if ($result instanceof ToolWidget && $message instanceof FunctionInvocationMessage) {
            if (!array_key_exists($message->call->name, $this->magic->tools)) {
                report(new \Exception("Tool not found: {$message->call->name}"));
                return null;
            }

            return $result->render(
                tool: $message->schema
                    ? new MagicFunction(
                        name: $message->call->name,
                        schema: $message->schema,
                        callback: fn () => null, // Never called, as we're only rendering here
                    )
                    : $this->magic->tools[$message->call->name],
                invocation: $message,
                output: MessageCollection::make($this->chat_messages)
                    ->firstFunctionOutput(fn (FunctionOutputMessage $output) => $message->call->id && $output->call->id === $message->call->id),
            );
        } else if ($result instanceof ToolWidget) {
            report(new \Exception('ToolWidget without invocation: ' . json_encode(['message' => $message, 'result' => $result])));

            return null;
        }

        return $result;
    }

    public static function renderChatMessage(Message $message, bool $streaming = false, bool $isCurrent = false): string|ToolWidget|null
    {
        if ($message instanceof FunctionOutputMessage) {
            return null;
        }

        if ($message instanceof FunctionInvocationMessage) {
            if ($widget = Arr::get(static::getToolWidgets(), $message->call->name)) {
                return $widget;
            }

            return ToolWidget::loading();
        }

        return view('llm-magic::components.chat.message', [
            'message' => $message,
            'streaming' => $streaming,
            'isCurrent' => $isCurrent,
            'plain' => $message->role !== Role::User,
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
            ->model($this->getLLM())
            ->system($this->getSystemPrompt())
            ->messages($this->chat_messages)
            ->onMessageProgress(fn (Message $message) => $this->onMessageProgress($message))
            ->onMessage(fn (Message $message) => $this->onMessage($message))
            ->onTokenStats(fn (TokenStats $stats) => $this->onTokenStats($stats))
            ->interrupt(function (FunctionCall $call) {
                $widget = Arr::get(static::getToolWidgets(), $call->name);

                if ($widget?->interrupts()) {
                    return true;
                }

                return false;
            })
            ->tools($this->getTools());
    }

    public function delayStart(): void
    {
        // Todo: Instead of this, we should now start a job and open a websocket connection to receive updates for streaming
        $this->js('
            setTimeout(() => $wire.start(), 1000);
        ');

        $this->dispatch('startPolling')->to(StreamableMessage::class);
    }

    public function delayHandle(): void
    {
        // Todo: Instead of this, we should now start a job and open a websocket connection to receive updates for streaming
        $this->js('
            $wire.$dispatch("startPolling");
            setTimeout(() => $wire.handle(), 1000);
        ');
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

        $this->delayStart();
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

        $this->delayStart();
    }

    public function start(): void
    {
//        dd($this->chat_messages, $this->magic->messages);
        $this->magic->stream();

        $this->chat_messages = $this->magic->messages;

        $this->dispatch('stopPolling')->to(StreamableMessage::class);
        $this->temporary_messages = [];
    }

    public function handle(): void
    {
        $last = array_pop($this->chat_messages);

        $message = new FunctionInvocationMessage(
            role: $last->role,
            call: new FunctionCall(
                name: $last->call->name,
                arguments: $data ?? $last->call->arguments,
                id: $last->call->id,
            ),
            partial: $last->partial,
        );

        unset($this->magic);

        $messages = $this->magic->handleMessages(MessageCollection::make([$message]), ignoreInterrupts: true);

        $this->chat_messages = $this->magic->messages;

        unset($this->magic);

        $this->dispatch('stopPolling')->to(StreamableMessage::class);
        $this->temporary_messages = [];
    }

    public function resetChat(): void
    {
        $this->chat_messages = [];

        unset($this->magic);

        $this->dispatch('stopPolling')->to(StreamableMessage::class);
        $this->temporary_messages = [];

        $this->dispatch('resetChat')->self();
    }

    protected function onMessageProgress(Message $message): void
    {
        $index = max(0, count($this->temporary_messages) - 1);
        $this->temporary_messages[$index] = $message;

        $this->saveStreamableState();
    }

    protected function onMessage(Message $message): void
    {
        $index = count($this->temporary_messages) - 1;
        $this->temporary_messages[$index] = $message;
        $this->temporary_messages[] = null;

        $this->saveStreamableState();
    }

    protected function onTokenStats(TokenStats $stats): void
    {
    }

    protected function saveStreamableState(): void
    {
        $messages = collect($this->temporary_messages)
            ->filter()
            ->map(function (Message $message) {
                // Inject the schema into the function invocation messages, so the StreamableMessage component can render tool widgets
                if ($message instanceof FunctionInvocationMessage && $tool = Arr::get($this->magic->tools, $message->call->name)) {
                    /** @var InvokableFunction $tool */

                    return new FunctionInvocationMessage(
                        role: $message->role,
                        call: $message->call,
                        partial: $message->partial,
                        schema: $tool->schema()
                    );
                }

                return $message;
            })
//			->dump($this->conversationId)
            ->all();

		Log::info($this->conversationId, [
            'messages1' => $messages,
        ]);

        StreamableMessage::put($this->conversationId, $messages);
    }



    protected function getLLM(): LLM
    {
        return ElElEm::fromString($this->llm_model);
    }

    protected function getSystemPrompt(): string
    {
        return 'You are a helpful chatbot. You are given some tools to use when appropriate. Give shorter, more concise answers than you would normally do.';
    }
    protected function getTools(): array
    {
        return [];
    }

    protected static function getToolWidgets(): array
    {
        return [];
    }
}
