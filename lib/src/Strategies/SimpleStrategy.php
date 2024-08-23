<?php

namespace Capevace\MagicImport\Strategies;

use App\Models\Actor\ActorTelemetry;
use Capevace\MagicImport\Artifacts\Artifact;
use Capevace\MagicImport\Config\Extractor;
use Capevace\MagicImport\LLM\LLM;
use Capevace\MagicImport\Loop\InferenceResult;
use Capevace\MagicImport\Prompt\ExtractorPrompt;
use Capevace\MagicImport\Prompt\Message\Message;
use Capevace\MagicImport\Prompt\TokenStats;
use Closure;
use Illuminate\Support\Facades\Log;

class SimpleStrategy
{
    public function __construct(
        protected Extractor $extractor,

        /** @var ?Closure(InferenceResult): void */
        protected Closure $onDataProgress,

        /** @var ?Closure(TokenStats): void */
        protected ?Closure $onTokenStats = null,

        /** @var ?Closure(Message): void */
        protected ?Closure $onMessageProgress = null,

        /** @var ?Closure(Message): void */
        protected ?Closure $onMessage = null,

        /** @var ?Closure(ActorTelemetry): void */
        protected ?Closure $onActorTelemetry = null,
    )
    {
    }

    /**
     * @param Artifact[] $artifacts
     */
    public function run(array $artifacts): InferenceResult
    {
        $prompt = new ExtractorPrompt(extractor: $this->extractor, artifacts: $artifacts);

        if ($this->onActorTelemetry) {
            ($this->onActorTelemetry)(new ActorTelemetry(
                model: "{$this->extractor->llm->getOrganization()->id}/{$this->extractor->llm->getModelName()}",
                system_prompt: $prompt->system(),
            ));
        }

        $messages = [];

        $onMessageProgress = function (Message $message) use (&$messages) {
            ($this->onDataProgress)(new InferenceResult(value: $message->toArray(), messages: $messages));

            if ($this->onMessageProgress) {
                ($this->onMessageProgress)($message);
            }
        };

        $onMessage = function (Message $message) use (&$messages) {
            $messages[] = $message;

            ($this->onDataProgress)(new InferenceResult(value: $message->toArray(), messages: $messages));

            if ($this->onMessage) {
                ($this->onMessage)($message);
            }
        };

        $message = $this->extractor->llm->stream(
            prompt: $prompt,
            onMessageProgress: $onMessageProgress,
            onMessage: $onMessage,
            onTokenStats: $this->onTokenStats
        );

        return new InferenceResult(value: $message, messages: $messages);
    }
}
