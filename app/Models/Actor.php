<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Support\Facades\Log;
use Mateffy\Magic\LLM\Message\DataMessage;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Mateffy\Magic\LLM\Message\JsonMessage;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Message\MultimodalMessage;
use Mateffy\Magic\LLM\Message\TextMessage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Actor extends Model
{
    use UsesUuid;

    protected $table = 'actors';

    protected $fillable = [
        'system_prompt',
        'extraction_run_id',
        'model',
    ];

    public function extractionRun(): BelongsTo
    {
        return $this->belongsTo(ExtractionRun::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ActorMessage::class, 'actor_id');
    }

    /**
     * @return Collection<ActorMessage>
     *
     * @throws \JsonException
     */
    public function add(Message $message): Collection
    {
        Log::info('Adding message', ['message' => $message]);
        $messages = match ($message::class) {
            TextMessage::class => $this->messages()->create([
                'type' => Actor\ActorMessageType::Text,
                'role' => $message->role,
                'text' => $message->content,
            ]),
            DataMessage::class => $this->messages()->create([
                'type' => Actor\ActorMessageType::Json,
                'role' => $message->role,
                'json' => json_encode($message->data()),
            ]),
            MultimodalMessage::class => collect($message->content)
                ->each(fn (MultimodalMessage\ContentInterface $content) => match ($content::class) {
                    \Mateffy\Magic\LLM\Message\MultimodalMessage\Base64Image::class => $this->messages()->create([
                        'type' => Actor\ActorMessageType::Base64Image,
                        'role' => $message->role,
                        'json' => $content->toArray(),
                    ]),
                    \Mateffy\Magic\LLM\Message\MultimodalMessage\Text::class => $this->messages()->create([
                        'type' => Actor\ActorMessageType::Text,
                        'role' => $message->role,
                        'text' => $content->text,
                    ]),
                    default => null,
                }),

            FunctionInvocationMessage::class => $this->messages()->create([
                'type' => Actor\ActorMessageType::FunctionInvocation,
                'role' => $message->role,
                'json' => [
                    'call' => $message->call?->toArray(),
                ]
            ]),

            FunctionOutputMessage::class => $this->messages()->create([
                'type' => Actor\ActorMessageType::FunctionOutput,
                'role' => $message->role,
                'json' => [
                    'call' => $message->call?->toArray(),
                    'output' => $message->output,
                ]
            ]),

            default => null,
        };

        $messages = Collection::wrap($messages)->filter();

        return $messages;
    }
}
