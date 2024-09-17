<?php

namespace Capevace\MagicImport\Builder;

use Capevace\MagicImport\Builder\Concerns\HasArtifacts;
use Capevace\MagicImport\Builder\Concerns\HasMessages;
use Capevace\MagicImport\Builder\Concerns\HasModel;
use Capevace\MagicImport\Builder\Concerns\HasSchema;
use Capevace\MagicImport\Builder\Concerns\HasSystemPrompt;
use Capevace\MagicImport\Builder\Concerns\HasTools;
use Capevace\MagicImport\Builder\Concerns\LaunchesBuilderLLM;
use Capevace\MagicImport\Prompt\Prompt;
use Closure;

class ChatPreconfiguredModelBuilder implements LLMBuilder
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
