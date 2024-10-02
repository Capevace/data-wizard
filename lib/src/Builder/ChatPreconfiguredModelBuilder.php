<?php

namespace Mateffy\Magic\Builder;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Mateffy\Magic\Builder\Concerns\HasArtifacts;
use Mateffy\Magic\Builder\Concerns\HasMessages;
use Mateffy\Magic\Builder\Concerns\HasModel;
use Mateffy\Magic\Builder\Concerns\HasModelCallbacks;
use Mateffy\Magic\Builder\Concerns\HasSchema;
use Mateffy\Magic\Builder\Concerns\HasSystemPrompt;
use Mateffy\Magic\Builder\Concerns\HasTools;
use Mateffy\Magic\Builder\Concerns\LaunchesBuilderLLM;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Mateffy\Magic\LLM\MessageCollection;
use Mateffy\Magic\LLM\PreconfiguredModelLaunchInterface;
use Mateffy\Magic\Prompt\Prompt;

class ChatPreconfiguredModelBuilder
{
    use HasArtifacts;
    use HasMessages;
    use HasModel;
    use HasModelCallbacks;
    use HasSchema;
    use HasSystemPrompt;
    use HasTools;
    use LaunchesBuilderLLM;

    public function build(): Prompt
    {
        return new class($this) implements Prompt
        {
            public function __construct(protected ChatPreconfiguredModelBuilder $builder) {}

            public function system(): ?string
            {
                return $this->builder->systemPrompt ?? 'You are a helpful chatbot.';
            }

            public function messages(): array
            {
                return $this->builder->messages;
            }

            public function functions(): array
            {
                return array_values($this->builder->tools);
            }

            public function shouldParseJson(): bool
            {
                return false;
            }
        };
    }

    public function stream(): MessageCollection
    {
        $prompt = $this->build();

        $messages = MessageCollection::make($this->model->stream(
            prompt: $prompt,
            onMessageProgress: $this->onMessageProgress,
            onMessage: $this->onMessage,
            onTokenStats: $this->onTokenStats
        ));

        $continue = true;

        foreach ($messages as $message) {
            if ($message instanceof FunctionInvocationMessage) {
                if ($fn = $this->tools[$message->call->name] ?? null) {
                    /** @var InvokableFunction $fn */

                    $message->call->arguments = $fn->validate($message->call->arguments);

                    try {
                        $output = $fn->execute($message->call);

                        if ($output instanceof FunctionOutputMessage) {
                            $outputMessage = $output;
                        } else {
                            $outputMessage = FunctionOutputMessage::output(call: $message->call, output: $output);
                        }
                    } catch (\Throwable $e) {
                        $outputMessage = FunctionOutputMessage::error(call: $message->call, message: $e->getMessage());
                    }

                    $messages->push($outputMessage);

                    if ($continue && !$outputMessage->endConversation) {
                        $messages
                            ->push(
                                ...$this
                                    ->addMessage($message)
                                    ->addMessage($outputMessage)
                                    ->onMessageProgress($this->onMessageProgress)
                                    ->onMessage($this->onMessage)
                                    ->onTokenStats($this->onTokenStats)
                                    ->stream()
                            );
                    }
                }
            }
        }

        return $messages;
    }

    public function send(): MessageCollection
    {
        $prompt = $this->build();

        return MessageCollection::make($this->model->send($prompt));
    }
}
