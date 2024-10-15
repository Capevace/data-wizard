<?php

namespace Mateffy\Magic\Strategies;

use App\Models\Actor\ActorTelemetry;
use Illuminate\Support\Facades\Log;
use Mateffy\Magic\Artifacts\Artifact;
use Mateffy\Magic\Config\Extractor;
use Mateffy\Magic\LLM\Message\DataMessage;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Message\TextMessage;
use Mateffy\Magic\Loop\InferenceResult;
use Mateffy\Magic\Magic;
use Mateffy\Magic\Prompt\ParallelMergerPrompt;
use Mateffy\Magic\Prompt\Role;
use Mateffy\Magic\Prompt\SequentialExtractorPrompt;
use Mateffy\Magic\Prompt\TokenStats;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ParallelStrategy
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

        /** @var ?Closure(Message, string): void */
        protected ?Closure $onMessage = null,

        /** @var ?Closure(ActorTelemetry): void */
        protected ?Closure $onActorTelemetry = null,
    ) {}

    /**
     * @param  Artifact[]  $artifacts
     */
    public function run(array $artifacts): array
    {
        $maxTokens = 20000;

        /** @var Collection<Collection<Artifact>> $batches */
        $batches = collect();

        /** @var Collection<Artifact> $batch */
        $batch = collect();
        $batchTokens = 0;

        foreach ($artifacts as $artifact) {
            [$splitArtifacts, $tokensUsed] = $artifact->split($maxTokens);

            while (count($splitArtifacts) > 0) {
                $artifact = array_shift($splitArtifacts);

                if ($batch->isNotEmpty() && $batchTokens + $artifact->tokens > $maxTokens) {
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

        $datas = [];
        $data = null;

        foreach ($batches as $batch) {
            $data = $this->generate($batch, data: $data) ?? $data;

            if ($this->onDataProgress && $data !== null) {
                ($this->onDataProgress)($data);
            }

            $datas[] = $data;
        }

        $data = $this->merge($datas);

        if ($this->onDataProgress) {
            ($this->onDataProgress)($data);
        }

        return $data;
    }

    protected function generate(Collection $artifacts, ?array $data): ?array
    {
        $threadId = Str::uuid()->toString();
        $prompt = new SequentialExtractorPrompt(extractor: $this->extractor, artifacts: $artifacts->all(), previousData: $data);

        if ($this->onActorTelemetry) {
            ($this->onActorTelemetry)(
                new ActorTelemetry(
                    id: $threadId,
                    model: "{$this->extractor->llm->getOrganization()->id}/{$this->extractor->llm->getModelName()}",
                    system_prompt: $prompt->system(),
                )
            );
        }

        if ($this->onMessage) {
            ($this->onMessage)(new TextMessage(role: Role::System, content: $prompt->system()), $threadId);

            foreach ($prompt->messages() as $message) {
                ($this->onMessage)($message, $threadId);
            }
        }

        $messages = [];
        $lastTokenStats = null;

        $messages = Magic::chat()
            ->model($this->extractor->llm)
            ->prompt($prompt)
            ->onMessageProgress(function (Message $message) use (&$messages, $threadId) {
                //            ($this->onDataProgress)(new InferenceResult(value: $message->toArray(), messages: $messages));

                if ($this->onMessageProgress) {
                    ($this->onMessageProgress)($message, $threadId);
                }
            })
            ->onMessage(function (Message $message) use (&$messages, $threadId) {
                $messages[] = $message;

                //            ($this->onDataProgress)(new InferenceResult(value: $message->toArray(), messages: $messages));

                if ($this->onMessage) {
                    ($this->onMessage)($message, $threadId);
                }
            })
            ->onTokenStats(function (TokenStats $tokenStats) use (&$data, &$lastTokenStats) {
                $lastTokenStats = $tokenStats;

                if ($this->onTokenStats) {
                    ($this->onTokenStats)($this->totalTokenStats?->add($lastTokenStats) ?? $lastTokenStats);
                }
            })
            ->stream();

        $this->totalTokenStats = $this->totalTokenStats?->add($lastTokenStats) ?? $lastTokenStats;

        return $messages->lastData();
    }

    protected function merge(array $datas): ?array
    {
        $threadId = Str::uuid();
        $prompt = new ParallelMergerPrompt(extractor: $this->extractor, datas: $datas);

        if ($this->onActorTelemetry) {
            ($this->onActorTelemetry)(new ActorTelemetry(
                id: $threadId,
                model: "{$this->extractor->llm->getOrganization()->id}/{$this->extractor->llm->getModelName()}",
                system_prompt: $prompt->system(),
            ));
        }

        if ($this->onMessage) {
            ($this->onMessage)(new TextMessage(role: Role::System, content: $prompt->system()), $threadId);

            foreach ($prompt->messages() as $message) {
                ($this->onMessage)($message, $threadId);
            }
        }

        $lastTokenStats = null;
        $strategy = $this;

        $messages = Magic::chat()
            ->model($this->extractor->llm)
            ->prompt($prompt)
            ->onMessageProgress(function (Message $message) use (&$messages, $threadId, $strategy) {
                if ($strategy->onDataProgress && $data = $message->toArray()['data'] ?? null) {
                    ($strategy->onDataProgress)($data, $threadId);
                }

                if ($strategy->onMessageProgress) {
                    ($strategy->onMessageProgress)($message, $threadId);
                }
            })
            ->onMessage(function (Message $message) use (&$messages, $threadId, $strategy) {
                if ($strategy->onDataProgress && $data = $message->toArray()['data'] ?? null) {
                    ($strategy->onDataProgress)($data, $threadId);
                }

                if ($strategy->onMessage) {
                    ($strategy->onMessage)($message, $threadId);
                }
            })
            ->onTokenStats(function (TokenStats $tokenStats) use (&$data, &$lastTokenStats, $strategy) {
                $lastTokenStats = $tokenStats;

                if ($strategy->onTokenStats) {
                    ($strategy->onTokenStats)($strategy->totalTokenStats?->add($lastTokenStats) ?? $lastTokenStats);
                }
            })
            ->stream();

        $this->totalTokenStats = $this->totalTokenStats?->add($lastTokenStats) ?? $lastTokenStats;

        return $messages->lastFunctionOutput()?->call?->arguments ?? $messages->lastData();
    }
}
