<?php

namespace Mateffy\Magic\Providers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Mateffy\Magic\Providers\ApiKey\ApiKeyProvider;
use Mateffy\Magic\Providers\ApiKey\ApiKeyTokenType;

/**
 * @property-read User $user
 * @property-read ApiKeyProvider $provider
 * @property-read ApiKeyTokenType $type
 * @property-read string $protected_secret
 */
class ApiKey extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'provider',
        'type',
        'secret',
    ];

    protected $hidden = [
        'secret',
    ];

    protected $casts = [
        'provider' => ApiKeyProvider::class,
        'type' => ApiKeyTokenType::class,
        'secret' => 'encrypted',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->id = (string) Str::uuid();
            }
        });

        static::saving(function (self $model) {
            if (! in_array($model->type, $model->provider->getValidTypes())) {
                throw new \InvalidArgumentException("Invalid type {$model->type} for provider {$model->provider}");
            }
        });

        // Add a global scope to only allow the current user to view their own api keys
        static::addGlobalScope('for_user', function (Builder $query) {
            return $query->where('user_id', auth()->id());
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getProtectedSecretAttribute(): string
    {
        $take = intval(strlen($this->secret) / 4);

        return str($this->secret)
            ->take($take)
            ->append(str('*')->repeat(max(strlen($this->secret) - $take, 0)))
            ->toString();
    }
}
