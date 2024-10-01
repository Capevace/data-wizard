<?php

namespace Mateffy\Magic\Strategies;

use App\Models\Actor\ActorTelemetry;
use Illuminate\Support\Str;
use Mateffy\Magic\Artifacts\Artifact;
use Mateffy\Magic\Config\Extractor;
use Mateffy\Magic\LLM\Message\JsonMessage;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\Loop\InferenceResult;
use Mateffy\Magic\Loop\Response\JsonResponse;
use Mateffy\Magic\Prompt\ExtractorPrompt;
use Mateffy\Magic\Prompt\TokenStats;
use Closure;

class SimpleStrategy
{
    public function __construct(
        protected Extractor $extractor,

        /** @var ?Closure(array): void */
        protected Closure $onDataProgress,

        /** @var ?Closure(TokenStats): void */
        protected ?Closure $onTokenStats = null,

        /** @var ?Closure(Message): void */
        protected ?Closure $onMessageProgress = null,

        /** @var ?Closure(Message): void */
        protected ?Closure $onMessage = null,

        /** @var ?Closure(ActorTelemetry): void */
        protected ?Closure $onActorTelemetry = null,
    ) {}

    /**
     * @param  Artifact[]  $artifacts
     */
    public function run(array $artifacts): InferenceResult
    {
        $threadId = Str::uuid();
        $prompt = new ExtractorPrompt(extractor: $this->extractor, artifacts: $artifacts);

        if ($this->onActorTelemetry) {
            ($this->onActorTelemetry)(new ActorTelemetry(
                id: $threadId,
                model: "{$this->extractor->llm->getOrganization()->id}/{$this->extractor->llm->getModelName()}",
                system_prompt: $prompt->system(),
            ));
        }

        $messages = [];

        $onMessageProgress = function (Message $message) use (&$messages) {
            if ($data = $message->toArray()['data'] ?? null) {
                ($this->onDataProgress)($data);
            }

            if ($this->onMessageProgress) {
                ($this->onMessageProgress)($message);
            }
        };

        $onMessage = function (Message $message) use (&$messages) {
            $messages[] = $message;

            if ($data = $message->toArray()['data'] ?? null) {
                ($this->onDataProgress)($data);
            }

            if ($this->onMessage) {
                ($this->onMessage)($message);
            }
        };

        $responses = $this->extractor->llm->stream(
            prompt: $prompt,
            onMessageProgress: $onMessageProgress,
            onMessage: $onMessage,
            onTokenStats: $this->onTokenStats
        );

        /** @var ?JsonMessage $jsonResponse */
        $jsonResponse = collect($responses)->first(fn ($response) => $responses instanceof JsonMessage);

        return new InferenceResult(value: $jsonResponse?->data, messages: $messages);
    }
}
