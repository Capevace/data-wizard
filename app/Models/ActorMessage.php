<?php

namespace App\Models;

use App\Models\Actor\ActorMessageType;
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
        'file_id',
        'type',
    ];

    protected $casts = [
        'role' => Role::class,
        'type' => ActorMessageType::class,
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(Actor::class);
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
