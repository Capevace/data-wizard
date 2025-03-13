<?php

namespace App\Models;

use App\Models\Actor\ActorMessageType;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Mateffy\Magic\Chat\Messages\Step\Base64Image;
use Mateffy\Magic\Chat\Prompt\Role;
use Mateffy\Magic\Tokens\ImageTokenizer;
use Mateffy\Magic\Tokens\OpenAiImageTokenizer;
use Mateffy\Magic\Tokens\TextTokenizer;

/**
 * @property Role $role
 * @property string $text
 * @property array $json
 * @property Base64Image|null $media
 * @property ActorMessageType $type
 * @property ?string $partial
 */
class ActorMessage extends Model
{
    use HasUuids;

    protected $table = 'actor_messages';

    protected $fillable = [
        'actor_id',
        'role',
        'text',
        'json',
        'type',
        'media_id',
    ];

    protected $attributes = [
        'role' => Role::Assistant,
        'json' => null,
        'text' => null,
    ];

    protected $casts = [
        'json' => 'json',
        'role' => Role::class,
        'type' => ActorMessageType::class,
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(Actor::class);
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function base64Image(): ?Base64Image
    {
        return Base64Image::fromArray($this->json);
    }

    public function data(?string $key = null): mixed
    {
        if ($key === null) {
            return $this->json;
        }

        return Arr::get($this->json ?? [], $key);
    }

    public function calculateTokens(): int
    {
        if ($this->data('type') === 'image') {
            $tokenizer = app(ImageTokenizer::class);

            // TODO: improve the token calculation by actually getting the image width and height
            // For now, we just use the default values, which assigns the same token count to all images.
            // This is good enough for evaluations, as it's equal to all images.

            return $tokenizer->tokenize(width: null, height: null);
        }

        $content = $this->text ?? $this->attributes['json'] ?? '';

        $tokenizer = app(TextTokenizer::class);

        return $tokenizer->tokenize($content);
    }
}
