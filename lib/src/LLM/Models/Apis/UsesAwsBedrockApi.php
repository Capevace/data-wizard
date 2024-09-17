<?php

namespace Capevace\MagicImport\LLM\Models\Apis;

use Aws\BedrockRuntime\BedrockRuntimeClient;
use Capevace\MagicImport\Functions\InvokableFunction;
use Capevace\MagicImport\LLM\Message\Message;
use Capevace\MagicImport\LLM\Models\Decoders\ClaudeResponseDecoder;
use Capevace\MagicImport\Model\Exceptions\InvalidRequest;
use Capevace\MagicImport\Model\Exceptions\TooManyTokensForModelRequested;
use Capevace\MagicImport\Prompt\ExtractorPrompt;
use Capevace\MagicImport\Prompt\Prompt;
use Capevace\MagicImport\Prompt\TokenStats;
use Closure;
use OpenAI\Responses\Chat\CreateResponse;
use Psr\Http\Message\StreamInterface;

trait UsesAwsBedrockApi
{
    protected function getApiToken(): string
    {
        return config('magic-extract.apis.aws.token');
    }

    /**
     * @throws InvalidRequest
     * @throws TooManyTokensForModelRequested
     */
    protected function createStreamedHttpRequest(Prompt $prompt): \Psr\Http\Message\StreamInterface
    {
        $config = [
            'inferenceConfig' => [
                'maxTokens' => $this->getOptions()->maxTokens,
            ],
            'modelId' => $this->getModelName(),
            'system' => [
                ['text' => $prompt->system()],
            ],
            'messages' => collect($prompt->messages())
                ->map(fn (Message $message) => $message->toArray())
                ->values()
                ->toArray(),
            'toolConfig' => [
                'tools' => collect($prompt->functions())
                    ->map(fn (InvokableFunction $function) => [
                        'toolSpec' => [
                            'name' => $function->name(),
                            'description' => '',
                            'input_schema' => $function->schema(),
                        ],
                    ]),
            ],
        ];

        if ($prompt instanceof ExtractorPrompt && ($fn = $prompt->forceFunction())) {
            $config['toolChoice'] = [
                'tool' => [
                    'name' => $fn->name(),
                ],
            ];
        }

        $bedrockClient = new BedrockRuntimeClient([
            'region' => config('magic-extract.apis.aws.region', 'eu-central-1'),
            'version' => 'latest',
            'profile' => 'default',
        ]);
        $bedrockClient->converseStreamAsync($config);
    }

    public function stream(Prompt $prompt, ?Closure $onMessageProgress = null, ?Closure $onMessage = null, ?Closure $onTokenStats = null): array
    {
        $stream = $this->createStreamedHttpRequest($prompt);

        $cost = $this->getModelCost();

        $decoder = new ClaudeResponseDecoder(
            $stream,
            $onMessageProgress,
            $onMessage,
            onTokenStats: fn (TokenStats $stats) => $onTokenStats($cost
                    ? $stats->withCost($cost)
                    : $stats
            )
        );

        return $decoder->process();
    }

    protected function createHttpRequest(Prompt $prompt): StreamInterface|CreateResponse|null
    {
        throw new \Exception('Not implemented');
    }

    public function send(Prompt $prompt): array
    {
        throw new \Exception('Not implemented');
    }
}
