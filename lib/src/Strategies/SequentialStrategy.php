<?php

namespace Mateffy\Magic\Strategies;

use App\Models\Actor\ActorTelemetry;
use Mateffy\Magic\Artifacts\Artifact;
use Mateffy\Magic\Config\Extractor;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\Loop\InferenceResult;
use Mateffy\Magic\Loop\Response\JsonResponse;
use Mateffy\Magic\Prompt\SequentialExtractorPrompt;
use Mateffy\Magic\Prompt\TokenStats;
use Closure;
use Illuminate\Support\Collection;

class SequentialStrategy
{
    protected ?TokenStats $totalTokenStats = null;

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
        $maxTokens = 50000;

        /** @var Collection<Collection<Artifact>> $batches */
        $batches = collect();

        /** @var Collection<Artifact> $batch */
        $batch = collect();
        $batchTokens = 0;

        foreach ($artifacts as $artifact) {
            [$splitArtifacts, $tokensUsed] = $artifact->split($maxTokens);

            while (count($splitArtifacts) > 0) {
                $artifact = array_shift($splitArtifacts);

                if ($batchTokens + $artifact->tokens > $maxTokens) {
                    $batches->push($batch);
                    $batch = collect();
                    $batchTokens = 0;
                }

                $batch->push($artifact);
                $batchTokens += $artifact->tokens;
            }
        }

        if ($batch->isNotEmpty()) {
            $batches->push($batch);
        }

        $data = null;

        foreach ($batches as $batch) {
            $data = $this->generate($batch, $data);

            if ($this->onDataProgress && $data !== null) {
                ($this->onDataProgress)($data);
            }

            dump($data);
        }

        return new InferenceResult(value: $data, messages: []);
    }

    protected function generate(Collection $artifacts, ?array $data): array
    {
        $prompt = new SequentialExtractorPrompt(extractor: $this->extractor, artifacts: $artifacts->all(), previousData: $data);

        if ($this->onActorTelemetry) {
            ($this->onActorTelemetry)(new ActorTelemetry(
                model: "{$this->extractor->llm->getOrganization()->id}/{$this->extractor->llm->getModelName()}",
                system_prompt: $prompt->system(),
            ));
        }

        $messages = [];

        $onMessageProgress = function (Message $message) use (&$messages) {
            //            ($this->onDataProgress)(new InferenceResult(value: $message->toArray(), messages: $messages));

            if ($this->onMessageProgress) {
                ($this->onMessageProgress)($message);
            }
        };

        $onMessage = function (Message $message) use (&$messages) {
            $messages[] = $message;

            //            ($this->onDataProgress)(new InferenceResult(value: $message->toArray(), messages: $messages));

            if ($this->onMessage) {
                ($this->onMessage)($message);
            }
        };

        $result = $this->extractor->llm->stream(
            prompt: $prompt,
            onMessageProgress: $onMessageProgress,
            onMessage: $onMessage,
            onTokenStats: function (TokenStats $tokenStats) use (&$data) {
                $this->totalTokenStats = $this->totalTokenStats?->add($tokenStats) ?? $tokenStats;

                if ($this->onTokenStats) {
                    ($this->onTokenStats)($this->totalTokenStats);
                }
            }
        );

        /** @var JsonResponse|null $response */
        $response = collect($result)
            ->first(fn ($response) => $response instanceof JsonResponse);

        return $response?->data ?? $data;
    }
}
