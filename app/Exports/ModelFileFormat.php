<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Model;

interface ModelFileFormat
{
    public function toData(): array;
    public function toModelData(): array;
    public function toJson(): string;
    public function create(): Model;
    public function update(Model $model): Model;

    public static function fromData(array $data): self;
    public static function fromModel(Model $model): self;
    public static function fromJson(string $json): self;

    public static function import(string $json, ?Model $update = null): Model;
    public static function export(Model $model): string;
}
