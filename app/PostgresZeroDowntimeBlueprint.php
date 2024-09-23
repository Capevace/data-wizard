<?php

namespace App;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\Grammar;
use JsonException;

class PostgresZeroDowntimeBlueprint extends Blueprint
{
    protected string $migrationJsonPath;

    /**
     * @throws JsonException
     */
    public function build(Connection $connection, Grammar $grammar)
    {
        $json = $this->toJson();
        $jsonString = json_encode($json, flags: JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);

        file_put_contents($this->migrationJsonPath, $jsonString);
    }

    public function toJson(): array
    {
        dd($this->commands);

        return [];
    }
}
