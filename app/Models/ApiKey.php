<?php

namespace App\Models;

use App\Models\ApiKey\ApiKeyProvider;
use App\Models\ApiKey\ApiKeyTokenType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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
