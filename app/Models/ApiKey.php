<?php

namespace App\Models;

use App\Models\ApiKey\ApiKeyProvider;
use App\Models\ApiKey\ApiKeyTokenType;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read User $user
 * @property-read ApiKeyProvider $provider
 * @property-read ApiKeyTokenType $type
 * @property-read string $protected_secret
 */
class ApiKey extends Model
{
    use UsesUuid;

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

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (self $model) {
            if (!in_array($model->type, $model->provider->getValidTypes())) {
                throw new \InvalidArgumentException("Invalid type {$model->type} for provider {$model->provider}");
            }
        });

        // Add a global scope to only allow the current user to view their own api keys
        static::addGlobalScope('for_user', function (Builder $query) {
            return $query->where('user_id', auth()->id());
        });
    }
}
