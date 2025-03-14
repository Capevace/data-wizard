<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mateffy\Magic\Chat\MessageCollection;
use Mateffy\Magic\Chat\Messages\DataMessage;
use Mateffy\Magic\Chat\Messages\Step\ContentInterface;
use Mateffy\Magic\Chat\Messages\Step\Image;
use Mateffy\Magic\Chat\Messages\ToolCallMessage;
use Mateffy\Magic\Chat\Messages\Message;
use Mateffy\Magic\Chat\Messages\Step;
use Mateffy\Magic\Chat\Messages\Step\Text;
use Mateffy\Magic\Chat\Messages\TextMessage;
use Mateffy\Magic\Chat\Messages\ToolResultMessage;

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

    public function add(Message $message): MessageCollection
    {
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
            Step::class => collect($message->content)
                ->each(fn (ContentInterface $content) => match ($content::class) {
                    Image::class => $this->messages()->create([
                        'type' => Actor\ActorMessageType::Base64Image,
                        'role' => $message->role,
                        'json' => $content->toArray(),
                    ]),
                    Text::class => $this->messages()->create([
                        'type' => Actor\ActorMessageType::Text,
                        'role' => $message->role,
                        'text' => $content->text,
                    ]),
                    Step\ToolUse::class => $this->messages()->create([
                        'type' => Actor\ActorMessageType::FunctionInvocation,
                        'role' => $message->role,
                        'json' => [
                            'call' => $content->call->toArray(),
                        ]
                    ]),
                    Step\ToolResult::class => $this->messages()->create([
                        'type' => Actor\ActorMessageType::FunctionOutput,
                        'role' => $message->role,
                        'json' => [
                            'call' => $content->call->toArray(),
                            'output' => $content->output,
                        ]
                    ]),
                    default => null,
                }),

            ToolCallMessage::class => $this->messages()->create([
                'type' => Actor\ActorMessageType::FunctionInvocation,
                'role' => $message->role,
                'json' => [
                    'call' => $message->call?->toArray(),
                ]
            ]),

            ToolResultMessage::class => $this->messages()->create([
                'type' => Actor\ActorMessageType::FunctionOutput,
                'role' => $message->role,
                'json' => [
                    'call' => $message->call?->toArray(),
                    'output' => $message->output,
                ]
            ]),

            default => null,
        };

        return MessageCollection::wrap($messages)->filter();
    }
}
