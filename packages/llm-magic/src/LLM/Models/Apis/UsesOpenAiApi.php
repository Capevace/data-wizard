<?php

namespace Mateffy\Magic\LLM\Models\Apis;

use Closure;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Exceptions\InvalidRequest;
use Mateffy\Magic\LLM\Exceptions\UnknownInferenceException;
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
use Mateffy\Magic\LLM\Models\Decoders\OpenAiResponseDecoder;
use Mateffy\Magic\Prompt\Prompt;
use Mateffy\Magic\Prompt\TokenStats;
use OpenAI\Laravel\Facades\OpenAI;

trait UsesOpenAiApi
{
    protected function getOpenAiApiKey(): string
    {
        return config('magic-extract.apis.openai.token');
    }

    protected function getOpenAiOrganization(): ?string
    {
        return config('magic-extract.apis.openai.organization_id');
    }

    protected function getOpenAiBaseUri(): ?string
    {
        return 'api.openai.com/v1';
    }

    /**
     * @throws UnknownInferenceException
     */
    public function stream(Prompt $prompt, ?Closure $onMessageProgress = null, ?Closure $onMessage = null, ?Closure $onTokenStats = null): MessageCollection
    {
        $messages = collect($prompt->messages())
            ->flatMap(callback: fn (Message $message) => match ($message::class) {
                TextMessage::class => [
                    [
                        'role' => $message->role,
                        'content' => $message->content,
                    ],
                ],
                JsonMessage::class => [
                    [
                        'role' => $message->role,
                        'content' => json_encode($message->data),
                    ],
                ],
                FunctionInvocationMessage::class => [
                    [
                        'role' => $message->role,
                        'tool_calls' => [
                            [
                                'id' => $message->call->id,
                                'type' => 'function',
                                'function' => [
                                    'name' => $message->call->name,
                                    'arguments' => json_encode($message->call->arguments, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                                ]
                            ]
                        ],
                        'content' => json_encode($message->call->arguments),
                        'tool_call_id' => $message->call->id,
                    ],
                ],
                FunctionOutputMessage::class => [
                    [
                        'role' => 'tool',
                        'content' => $message->text(),
                        'tool_call_id' => $message->call->id,
                    ],
                ],
                MultimodalMessage::class => (function () use ($message) {
                    $tools = collect($message->content)
                        ->filter(fn (ContentInterface $message) => $message instanceof ToolUse || $message instanceof ToolResult)
                        ->map(fn (ContentInterface $message) => match ($message::class) {
                            ToolUse::class => [
                                'role' => 'assistant',
                                'tool_calls' => [
                                    [
                                        'id' => $message->call->id,
                                        'type' => 'function',
                                        'function' => [
                                            'name' => $message->call->name,
                                            'arguments' => json_encode($message->call->arguments, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                                        ]
                                    ]
                                ],
                                'content' => json_encode($message->call->arguments),
                                'tool_call_id' => $message->call->id,
                            ],
                            ToolResult::class => [
                                'role' => 'tool',
                                'content' => json_encode($message->output, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                                'tool_call_id' => $message->call->id,
                            ],
                        })
                        ->values();

                    $content = collect($message->content)
                        ->filter(fn (ContentInterface $message) => !($message instanceof ToolUse || $message instanceof ToolResult))
                        ->values();

                    return array_filter([
                        ...$tools,
                        $content->isNotEmpty()
                            ? [
                                'role' => $message->role,
                                'content' => $content
                                    ->map(fn (ContentInterface $message) => match ($message::class) {
                                        Text::class => [
                                            'type' => 'text',
                                            'text' => $message->text,
                                        ],
                                        Base64Image::class => [
                                            'type' => 'image_url',
                                            'image_url' => [
                                                'url' => "data:{$message->mime};base64,{$message->imageBase64}"
                                            ]
                                        ]
                                    })
                                    ->toArray(),
                            ]
                            : null,
                    ]);
                })(),

                default => null,
            })
            ->filter()
            ->values()
            ->toArray();

//        {
//            "type": "function",
//            "function": {
//                "name": "get_delivery_date",
//                "description": "Get the delivery date for a customer's order. Call this whenever you need to know the delivery date, for example when a customer asks 'Where is my package'",
//                "parameters": {
//                    "type": "object",
//                    "properties": {
//                        "order_id": {
//                            "type": "string",
//                            "description": "The customer's order ID.",
//                        },
//                    },
//                    "required": ["order_id"],
//                    "additionalProperties": False,
//                },
//            }
//        }

        $tools = collect($prompt->functions())
            ->map(fn (InvokableFunction $function) => [
                'type' => 'function',
                'function' => [
                    'name' => $function->name(),
                    'description' => method_exists($function, 'description') ? $function->description() : "The {$function->name()} function",
                    'parameters' => $function->schema(),
                ]
            ])
            ->toArray();

        if (method_exists($prompt, 'forceFunction') && ($fn = $prompt->forceFunction())) {
//            $toolchoice = ['type' => 'tool', 'name' => $fn->name()];
//            {"type": "function", "function": {"name": "my_function"}}
            $toolchoice = ['type' => 'function', 'function' => ['name' => $fn->name()]];
        } else {
            $toolchoice = null;
        }

        try {


            $openai = \OpenAI::factory()
                ->withApiKey($this->getOpenAiApiKey())
                ->withOrganization($this->getOpenAiOrganization())
                ->withBaseUri($this->getOpenAiBaseUri())
                ->make();

            $stream = $openai
                ->chat()
                ->createStreamed(array_filter([
                    'model' => str($this->model)
                        ->after('openai/')
                        ->after('google/'),
                    'messages' => $messages,
                    'tools' => count($tools) > 0 ? $tools : null,
                    'toolchoice' => $toolchoice,
                ]));

            $cost = $this->getModelCost();

            $decoder = new OpenAiResponseDecoder(
                $stream,
                $onMessageProgress,
                $onMessage,
                onTokenStats: fn (TokenStats $stats) => $onTokenStats
                    ? $onTokenStats($cost
                        ? $stats->withCost($cost)
                        : $stats
                    )
                    : $stats,
                json: $prompt->shouldParseJson()
            );

            return MessageCollection::make($decoder->process());
        } catch (\Throwable $e) {
            dd($e);
            throw new UnknownInferenceException($e->getMessage(), previous: $e);
        }
//
//
//
//        if ($stream->usage && $onTokenStats) {
//            $onTokenStats(new TokenStats(
//                tokens: $response->usage->totalTokens,
//                inputTokens: $response->usage->promptTokens,
//                outputTokens: 0
//            ));
//        }
    }

    public function send(Prompt $prompt): MessageCollection
    {
        // TODO: Implement send() method.
    }
}
