<?php

namespace Mateffy\Magic\Builder;

use Closure;
use Mateffy\Magic\Builder\Concerns\HasArtifacts;
use Mateffy\Magic\Builder\Concerns\HasMessages;
use Mateffy\Magic\Builder\Concerns\HasModel;
use Mateffy\Magic\Builder\Concerns\HasSchema;
use Mateffy\Magic\Builder\Concerns\HasSystemPrompt;
use Mateffy\Magic\Builder\Concerns\HasTools;
use Mateffy\Magic\Builder\Concerns\LaunchesBuilderLLM;
use Mateffy\Magic\LLM\PreconfiguredModelLaunchInterface;
use Mateffy\Magic\Prompt\Prompt;

class ChatPreconfiguredModelBuilder implements PreconfiguredModelLaunchInterface
{
    use HasArtifacts;
    use HasMessages;
    use HasModel;
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
                return $this->builder->systemPrompt;
            }

            public function messages(): array
            {
                return $this->builder->messages;
            }

            public function functions(): array
            {
                return $this->builder->tools;
            }
        };
    }

    public function stream(?Closure $onMessageProgress = null, ?Closure $onMessage = null): array
    {
        $prompt = $this->build();

        return $this->model->stream($prompt, $onMessageProgress, $onMessage);
    }

    public function send(): array
    {
        $prompt = $this->build();

        return $this->model->send($prompt);
    }
}
