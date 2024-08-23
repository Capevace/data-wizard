<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Capevace\MagicImport\Prompt\Message\FunctionInvocationMessage;
use Capevace\MagicImport\Prompt\Message\FunctionOutputMessage;
use Capevace\MagicImport\Prompt\Message\JsonMessage;
use Capevace\MagicImport\Prompt\Message\Message;
use Capevace\MagicImport\Prompt\Message\MultimodalMessage;
use Capevace\MagicImport\Prompt\Message\TextMessage;
use Capevace\MagicImport\Prompt\Role;
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
     * @throws \JsonException
     */
    public function add(Message $message): Collection
    {
        $messages = match ($message::class) {
            TextMessage::class => $this->messages()->create([
                'type' => Actor\ActorMessageType::Text,
                'role' => Role::tryFrom($message->role) ?? Role::Assistant,
                'text' => $message->content,
            ]),
            JsonMessage::class => $this->messages()->create([
                'type' => Actor\ActorMessageType::Json,
                'role' => Role::tryFrom($message->role) ?? Role::Assistant,
                'json' => $message->data,
            ]),
            MultimodalMessage::class => collect($message->content)
                ->each(fn(MultimodalMessage\Base64Image|MultimodalMessage\Text $image) => match ($image::class) {
                    MultimodalMessage\Base64Image::class => $this->messages()->create([
                        'type' => Actor\ActorMessageType::Base64Image,
                        'role' => Role::tryFrom($message->role) ?? Role::Assistant,
                        'json' => $image->toArray()
                    ]),
                    MultimodalMessage\Text::class => $this->messages()->create([
                        'type' => Actor\ActorMessageType::Text,
                        'role' => Role::tryFrom($message->role) ?? Role::Assistant,
                        'json' => $image->toArray()
                    ]),
                    default => null,
                }),

            FunctionInvocationMessage::class => $this->messages()->create([
                'type' => Actor\ActorMessageType::FunctionInvocation,
                'role' => Role::tryFrom($message->role) ?? Role::Assistant,
                'json' => $message->toArray()
            ]),

            FunctionOutputMessage::class => $this->messages()->create([
                'type' => Actor\ActorMessageType::FunctionOutput,
                'role' => Role::tryFrom($message->role) ?? Role::Assistant,
                'json' => $message->toArray()
            ]),

            default => null,
        };

        $messages = Collection::wrap($messages);

        return $messages;
    }
}
