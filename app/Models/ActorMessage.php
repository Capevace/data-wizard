<?php

namespace App\Models;

use App\Models\Actor\ActorMessageType;
use Illuminate\Support\Arr;
use Mateffy\Magic\LLM\Message\MultimodalMessage\Base64Image;
use Mateffy\Magic\Prompt\Role;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    use HasUuids, HasUuids;

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

    public function data(?string $key = null): ?array
    {
        if ($key === null) {
            return $this->json;
        }

        return Arr::get($this->json ?? [], $key);
    }
}
