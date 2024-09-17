<?php

namespace Capevace\MagicImport\Loop;

use Capevace\MagicImport\Functions\ExtractData;
use Capevace\MagicImport\Functions\InvokableFunction;
use Capevace\MagicImport\Functions\OutputText;
use Capevace\MagicImport\LLM\Message\FunctionInvocationMessage;
use Capevace\MagicImport\LLM\Message\FunctionOutputMessage;
use Capevace\MagicImport\LLM\Message\Message;
use Capevace\MagicImport\LLM\Message\TextMessage;
use Capevace\MagicImport\Model\LLM;
use Capevace\MagicImport\Prompt\Prompt;
use Capevace\MagicImport\Prompt\Role;
use Carbon\CarbonImmutable;
use Closure;
use Exception;
use JsonException;

class Loop
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
    public function performStep(array $messages): LoopStep
    {
        foreach ($messages as $message) {
            if ($message instanceof FunctionInvocationMessage) {
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

                    $output = $this->invokeFunction($function, $message->call->arguments);
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

    public function invokeFunction(InvokableFunction $function, array $parameters): mixed
    {
        $parameters = $function->validate($parameters);

        return $function->execute($parameters);
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function callLLM(): void
    {
        if ($this->stream) {
            $stream = $this->model->createStreamedHttpRequest(
                messages: $this->serializedMessages(),
                functions: $this->prompt->functions()
            );

            $decoderClass = $this->model->decoder();

            $decoder = new $decoderClass(
                $stream,
                onMessageProgress: $this->onMessageProgress,
                onMessage: $this->onMessage,
            );
        } else {
            $results = $this->model->createHttpRequest(
                messages: $this->serializedMessages(),
                functions: $this->prompt->functions()
            );

            $decoderClass = $this->model->decoder();

            $decoder = new $decoderClass(
                $results,
                onMessageProgress: $this->onMessageProgress,
                onMessage: $this->onMessage,
            );
        }

        $messages = $decoder->process();
        $this->steps[] = $this->performStep($messages);

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
        if (! $this->manualFunctions) {
            return collect($this->messages())
                ->map(fn (Message $message) => match ($message::class) {
                    default => $message,
                    FunctionOutputMessage::class => new TextMessage(role: Role::User, content: 'OUTPUT: '.json_encode($message->output, JSON_THROW_ON_ERROR)),
                })
                ->all();
        }

        return collect($this->messages())
            ->map(fn (Message $message) => match ($message::class) {
                TextMessage::class => $message,
                FunctionOutputMessage::class => new TextMessage(role: Role::User, content: 'OUTPUT: '.json_encode($message->output, JSON_THROW_ON_ERROR)),
                default => new TextMessage(role: Role::Assistant, content: json_encode($message->toArray(), JSON_THROW_ON_ERROR)),
            })
            ->all();
    }
}
