<?php

namespace Mateffy\Magic\LLM\Models;

use Closure;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Mateffy\Magic\Config\Organization;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\ElElEm;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Mateffy\Magic\LLM\Message\JsonMessage;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Message\MultimodalMessage;
use Mateffy\Magic\LLM\Message\MultimodalMessage\Base64Image;
use Mateffy\Magic\LLM\Message\MultimodalMessage\ContentInterface;
use Mateffy\Magic\LLM\Message\MultimodalMessage\Text;
use Mateffy\Magic\LLM\Message\MultimodalMessage\ToolResult;
use Mateffy\Magic\LLM\Message\MultimodalMessage\ToolUse;
use Mateffy\Magic\LLM\Message\TextMessage;
use Mateffy\Magic\LLM\MessageCollection;
use Mateffy\Magic\LLM\ModelCost;
use Mateffy\Magic\LLM\Models\Apis\UsesAnthropicApi;
use Mateffy\Magic\LLM\Options\ElElEmOptions;
use Mateffy\Magic\Prompt\Prompt;
use Mateffy\Magic\Prompt\Role;

class Ollama extends ElElEm
{
    public const LLAMA_3_1 = 'llama3.1';

    public string $baseUrl = 'http://127.0.0.1:11434/api/';

    public function __construct(
        string $model,
        ElElEmOptions $options = new ElElEmOptions,
    ) {
        parent::__construct(
            organization: new Organization(
                id: 'ollama',
                name: 'Self-hosted AI',
                website: 'https://ollama.com',
                privacyUsedForModelTraining: false,
                privacyUsedForAbusePrevention: false,
            ),
            model: $model,
            options: $options,
        );
    }

    public function getModelCost(): ?ModelCost
    {
        return new ModelCost(
            inputCentsPer1K: 0,
            outputCentsPer1K: 0,
        );
    }

    public static function llama_3_1(): Ollama
    {
        return new Ollama(
            model: Ollama::LLAMA_3_1,
            options: new ElElEmOptions
        );
    }

    public function stream(Prompt $prompt, ?Closure $onMessageProgress = null, ?Closure $onMessage = null, ?Closure $onTokenStats = null): MessageCollection
    {
        $guzzle = new Client([
            'base_uri' => $this->baseUrl,
        ]);

        try {
            $response = $guzzle->post('chat', [
                'json' => [
                    'model' => $this->getModelName(),
                    'messages' => collect($prompt->messages())
                        ->dump()
                        ->map(fn (Message $message) => match ($message::class) {
                            TextMessage::class => [
                                'class' => 'text',
                                'role' => $message->role,
                                'content' => $message->content,
                            ],

                            JsonMessage::class => [
                                'class' => 'json',
                                'role' => $message->role,
                                'content' => json_encode($message->data),
                            ],

        //                    {"role": "assistant", "content": [
        //                        {"type": "tool_use", "id": "toolu_01A09q90qw90lq917835lq9", "name": "get_weather", "input": {"location": "San Francisco, CA"}}
        //                    ]},
                            FunctionInvocationMessage::class => [
                                'class' => 'function_invocation',
                                'role' => $message->role,
                                'content' => [
                                    [
                                        'type' => 'tool_use',
                                        'id' => $message->call->id ?? $message->call->name,
                                        'name' => $message->call->name,
                                        'input' => $message->call->arguments,
                                    ],
                                ],
                            ],

        //                    {"role": "user", "content": [
        //                        {"type": "tool_result", "tool_use_id": "toolu_01A09q90qw90lq917835lq9", "content": "15 degrees"}
        //                    ]},
                            FunctionOutputMessage::class => [
                                'class' => 'function_output',
                                'role' => $message->role,
                                'content' => [
                                    [
                                        'type' => 'tool_result',
                                        'tool_use_id' => $message->call->id ?? $message->call->name,
                                        'content' => [
                                            ['type' => 'text', 'text' => $message->text()],
                                        ]
                                    ],
                                ],
                            ],

                            MultimodalMessage::class => [
                                'class' => 'multimodal',
                                'role' => $message->role,
                                'content' => collect($message->content)
                                    ->reduce(fn (string $combined, ContentInterface $message) => "{$combined}\n\n[" . $message::class . "}]: " . json_encode($message->toArray()), '')
                            ],

                            default => null,
                        })
        //                ->dd()
                        ->filter()
                        ->values()
                        ->prepend(['role' => 'system', 'content' => $prompt->system()])
                        ->toArray(),
//                    'stream' => true,
                    'tools' => collect($prompt->functions())
                        ->map(fn (InvokableFunction $function) => [
                            'type' => 'function',
                            'function' => [
                                'name' => $function->name(),
                                'description' => method_exists($function, 'description') ? $function->description() : '',
                                'parameters' => $function->schema(),
                            ]
                        ]),
                ],
//                'stream' => true,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Invalid status code: '.$response->getStatusCode() . ' Body: '.$response->getBody());
            }

            $rest = $response->getBody()->read(1);
            $message = null;

            while ($chunk = $response->getBody()->read(512)) {
                $jsonChunks = explode("\n", $rest.$chunk);
                $rest = '';

                foreach ($jsonChunks as $jsonChunk) {
                    $json = json_decode($jsonChunk, true, 512);

                    if ($json === null) {
                        $rest .= $jsonChunk;
                        continue;
                    }

                    $content = $json['message']['content'];

                    if (!$message && ($content[0] ?? null) === '{') {
                        $message = new FunctionInvocationMessage(
                            role: Role::Assistant,
                            call: null,
                            partial: ''
                        );
                    } else if (!$message) {
                        $message = new TextMessage(
                            role: Role::Assistant,
                            content: ''
                        );
                    }

                    if ($message instanceof FunctionInvocationMessage) {
                        $message = $message->appendFull($content);
                    } else if ($message instanceof TextMessage) {
                        $message = $message->append($content);
                    }
                }
            }

            return MessageCollection::make([$message]);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function send(Prompt $prompt): MessageCollection
    {
        return $this->stream($prompt);
    }
}
