<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Model;
use JsonException;

trait BaseModelFileFormat
{
    public static function getModel()
    {
        return static::$model;
    }

    /**
     * @throws JsonException
     */
    public function toJson(): string
    {
        return json_encode($this->toData(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * @throws JsonException
     */
    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return self::fromData($data);
    }

    public function create(): Model
    {
        $model = static::getModel();

        return $model::create($this->toModelData());
    }

    public function update(Model $model): Model
    {
        $model->update($this->toModelData());

        return $model;
    }

    /**
     * @throws JsonException
     */
    public static function import(string $json, ?Model $update = null): Model
    {
        if ($update) {
            return self::fromJson($json)->update($update);
        }

        return self::fromJson($json)->create();
    }

    /**
     * @throws JsonException
     */
    public static function export(Model $model): string
    {
        return self::fromModel($model)->toJson();
    }
}
