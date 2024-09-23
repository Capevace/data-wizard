<?php

namespace Mateffy\Magic\Loop;

use Mateffy\Magic\Functions\ExtractData;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\Functions\OutputText;
use Mateffy\Magic\LLM\LLM;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Message\TextMessage;
use Mateffy\Magic\Prompt\Prompt;
use Mateffy\Magic\Prompt\Role;
use Carbon\CarbonImmutable;
use Closure;
use Exception;
use JsonException;

class NewLoop
{
    /** @var Message[] */
    public array $messages = [];

    /** @var LoopStep[] */
    public array $steps = [];

    public function __construct(
        public LLM $model,
        public Prompt $prompt,

        public bool $stream = true,
        public bool $manualFunctions = false,

        public ?Closure $onMessageProgress = null,
        public ?Closure $onMessage = null,
        public ?Closure $onFunctionCalled = null,
        public ?Closure $onFunctionOutput = null,
        public ?Closure $onFunctionError = null,
        public ?Closure $onStep = null,
        public ?Closure $onStream = null,
        public ?Closure $onEnd = null,
    ) {}

    /**
     * @param  Message[]  $messages
     *
     * @throws JsonException
     */
    public function start(array $messages = []): void
    {
        $this->steps[] = new LoopStep(
            messages: [
                ...$this->prompt->messages(),
                ...$messages,
            ],
            initiatedByUser: true,
            timestamp: CarbonImmutable::now()
        );

        $this->callLLM();
    }

    public function push(Message $response)
    {
        $this->responses[] = $response;

        $this->onMessage?->call($this, $response->toMessage());
    }

    /**
     * @param  Message[]  $messages
     *
     * @throws Exception
     */
    public function processMessages(array $messages): LoopStep
    {
        foreach ($messages as $message) {
            if ($message instanceof FunctionInvocationMessage) {
                /** @var InvokableFunction|null $function */
                $function = collect($this->prompt->functions())
                    ->first(fn (InvokableFunction $function) => $function->name() === $message->call->name);

                if ($message->call->name === (new ExtractData)->name()) {
                    $this->onEnd?->call($this, $message);

                    break;
                }

                try {
                    if ($function === null) {
                        throw new Exception("Function {$message->call->name} not found");
                    }

                    $this->onFunctionCalled?->call($this, $message);

                    $parameters = $function->validate($message->call->arguments);
                    $output = $function->execute($parameters);
                } catch (Exception $e) {
                    $output = $e->getMessage();

                    $this->onFunctionError?->call($this, $message, $e);

                    if (app()->hasDebugModeEnabled()) {
                        $messages[] = new TextMessage(role: Role::Assistant, content: 'DEBUG Error: '.$e->getMessage());
                    }
                }

                // Add function output to messages
                $messages[] = new FunctionOutputMessage(role: Role::Assistant, call: $message->call, output: $output);
            }
        }

        // Log a new loop step
        return new LoopStep(
            messages: $messages,
            initiatedByUser: true,
            timestamp: CarbonImmutable::now()
        );
    }

    public function shouldCallLLM(): bool
    {
        /** @var LoopStep|null $lastStep */
        $lastStep = collect($this->steps)->last();

        if ($lastStep === null) {
            return true;
        }

        /** @var Message|null $lastMessage */
        $lastMessage = collect($lastStep->messages)->last();

        // If there are no messages, we should call LLM
        if ($lastMessage === null) {
            return true;
        }

        // If the last message was a function output, we should call LLM so it can react to it
        return ($lastMessage instanceof FunctionOutputMessage) && ($lastMessage->call->name !== (new OutputText)->name());
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function callLLM(): void
    {
        //        $prompt = $this->prompt->withMessages($this->serializedMessages());
        $prompt = $this->prompt;

        if ($this->stream) {
            $messages = $this->model->stream(
                prompt: $prompt,
                onMessageProgress: $this->onMessageProgress,
                onMessage: $this->onMessage,
            );
        } else {
            $messages = $this->model->send(prompt: $prompt);
        }

        $this->steps[] = $this->processMessages($messages);

        if ($this->shouldCallLLM()) {
            $this->callLLM();
        } else {
            $this->onEnd?->call($this);
        }
    }

    /**
     * @return Message[]
     *
     * @throws JsonException
     */
    public function messages(): array
    {
        return collect($this->steps)
            ->map(fn (LoopStep $step) => $step->messages)
            ->flatten()
            ->all();
    }

    /**
     * @return Message[]
     *
     * @throws JsonException
     */
    public function serializedMessages(): array
    {
        return collect($this->messages())
            ->map(fn (Message $message) => match ($message::class) {
                TextMessage::class => $message,
                FunctionOutputMessage::class => new TextMessage(role: Role::User, content: 'OUTPUT: '.json_encode($message->output, JSON_THROW_ON_ERROR)),
                default => new TextMessage(role: Role::Assistant, content: json_encode($message->toArray(), JSON_THROW_ON_ERROR)),
            })
            ->all();
    }
}
