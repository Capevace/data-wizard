<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Capevace\MagicImport\LLM\Message\FunctionInvocationMessage;
use Capevace\MagicImport\LLM\Message\FunctionOutputMessage;
use Capevace\MagicImport\LLM\Message\JsonMessage;
use Capevace\MagicImport\LLM\Message\Message;
use Capevace\MagicImport\LLM\Message\MultimodalMessage;
use Capevace\MagicImport\LLM\Message\TextMessage;
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
        $messages = match ($message::class) {
            TextMessage::class => $this->messages()->create([
                'type' => Actor\ActorMessageType::Text,
                'role' => $message->role,
                'text' => $message->content,
            ]),
            JsonMessage::class => $this->messages()->create([
                'type' => Actor\ActorMessageType::Json,
                'role' => $message->role,
                'json' => json_encode($message->data),
            ]),
            MultimodalMessage::class => collect($message->content)
                ->each(fn (\Capevace\MagicImport\LLM\Message\MultimodalMessage\Base64Image|\Capevace\MagicImport\LLM\Message\MultimodalMessage\Text $content) => match ($content::class) {
                    \Capevace\MagicImport\LLM\Message\MultimodalMessage\Base64Image::class => $this->messages()->create([
                        'type' => Actor\ActorMessageType::Base64Image,
                        'role' => $message->role,
                        'json' => $content->toArray(),
                    ]),
                    \Capevace\MagicImport\LLM\Message\MultimodalMessage\Text::class => $this->messages()->create([
                        'type' => Actor\ActorMessageType::Text,
                        'role' => $message->role,
                        'text' => $content->text,
                    ]),
                    default => null,
                }),

            FunctionInvocationMessage::class => $this->messages()->create([
                'type' => Actor\ActorMessageType::FunctionInvocation,
                'role' => $message->role,
                'json' => $message->toArray(),
            ]),

            FunctionOutputMessage::class => $this->messages()->create([
                'type' => Actor\ActorMessageType::FunctionOutput,
                'role' => $message->role,
                'json' => $message->toArray(),
            ]),

            default => null,
        };

        $messages = Collection::wrap($messages)->filter();

        return $messages;
    }
}
