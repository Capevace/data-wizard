<?php

namespace Capevace\MagicImport\Loop;

use Capevace\MagicImport\Functions\ExtractData;
use Capevace\MagicImport\Functions\InvokableFunction;
use Capevace\MagicImport\Functions\OutputText;
use Capevace\MagicImport\LLM\LLM;
use Capevace\MagicImport\Loop\Response\FunctionCallResponse;
use Capevace\MagicImport\Loop\Response\LLMResponse;
use Capevace\MagicImport\Prompt\Message\FunctionOutputMessage;
use Capevace\MagicImport\Prompt\Message\Message;
use Capevace\MagicImport\Prompt\Message\TextMessage;
use Capevace\MagicImport\Prompt\Prompt;
use Capevace\MagicImport\Prompt\Role;
use Carbon\CarbonImmutable;
use Closure;
use Exception;
use JsonException;

class NewLoop
{
    /** @var LLMResponse[] */
    public array $responses = [];

    /** @var LoopStep[] */
    public array $steps = [];

    public function __construct(
        public LLM    $model,
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
    )
    {
    }

    /**
     * @param Message[] $messages
     * @throws JsonException
     */
    public function start(array $messages = []): void
    {
        $this->steps[] = new LoopStep(
            messages: [
                ...$this->prompt->messages(),
                ...$messages
            ],
            initiatedByUser: true,
            timestamp: CarbonImmutable::now()
        );

        $this->callLLM();
    }

    public function push(LLMResponse $response)
    {
        $this->responses[] = $response;

        $this->onMessage?->call($this, $response->toMessage());
    }

    /**
     * @throws Exception
     * @param LLMResponse[] $responses
     */
    public function processMessages(array $responses): LoopStep
    {
        /** @var Message[] $messages */
        $messages = array_map(fn(LLMResponse $response) => $response->toMessage(), $responses);

        foreach ($responses as $response) {
            if ($response instanceof FunctionCallResponse) {
                /** @var InvokableFunction|null $function */
                $function = collect($this->prompt->functions())
                    ->first(fn(InvokableFunction $function) => $function->name() === $response->functionCall->name);

                if ($response->functionCall->name === (new ExtractData())->name()) {
                    $this->onEnd?->call($this, $response);

                    break;
                }

                try {
                    if ($function === null) {
                        throw new Exception("Function {$response->functionCall->name} not found");
                    }

                    $this->onFunctionCalled?->call($this, $response);

                    $parameters = $function->validate($response->functionCall->arguments);
                    $output = $function->execute($parameters);
                } catch (Exception $e) {
                    $output = $e->getMessage();

                    $this->onFunctionError?->call($this, $response, $e);

                    if (app()->hasDebugModeEnabled()) {
                        $messages[] = new TextMessage(role: Role::Assistant, content: 'DEBUG Error: ' . $e->getMessage());
                    }
                }

                // Add function output to messages
                $messages[] = new FunctionOutputMessage(role: Role::Assistant, call: $response->functionCall, output: $output);
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
        $prompt = $this->prompt->withMessages($this->serializedMessages());

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
     * @throws JsonException
     */
    public function messages(): array
    {
        return collect($this->steps)
            ->map(fn(LoopStep $step) => $step->messages)
            ->flatten()
            ->all();
    }

    /**
     * @return Message[]
     * @throws JsonException
     */
    public function serializedMessages(): array
    {
        return collect($this->messages())
            ->map(fn(Message $message) => match ($message::class) {
                TextMessage::class => $message,
                FunctionOutputMessage::class => new TextMessage(role: Role::User, content: 'OUTPUT: ' . json_encode($message->output, JSON_THROW_ON_ERROR)),
                default => new TextMessage(role: Role::Assistant, content: json_encode($message->toArray(), JSON_THROW_ON_ERROR)),
            })
            ->all();
    }
}
