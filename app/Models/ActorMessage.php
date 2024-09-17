<?php

namespace App\Models;

use App\Models\Actor\ActorMessageType;
use Capevace\MagicImport\LLM\Message\MultimodalMessage\Base64Image;
use Capevace\MagicImport\Prompt\Role;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
