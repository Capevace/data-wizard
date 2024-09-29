<?php

namespace App;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Database\Schema\ForeignKeyDefinition;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Support\Fluent;
use JsonException;

class PostgresZeroDowntimeBlueprint extends Blueprint
{
    protected string $migrationJsonPath;

    public function __construct(protected PGRoll $roll, $table, Closure $callback = null, $prefix = '')
    {
        parent::__construct($table, $callback, $prefix);
    }

    /**
     * @throws JsonException
     */
    public function build(Connection $connection, Grammar $grammar)
    {
        $json = $this->toJson();

        dump($json);

        parent::build($connection, $grammar);
//        $json = $this->toJson();
//        $jsonString = json_encode($json, flags: JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
//
//        file_put_contents($this->migrationJsonPath, $jsonString);
    }

    public function toJson(): array
    {
        $addedColumns = collect($this->getAddedColumns());

        $modifiedColumns = collect($this->getChangedColumns());

        /** @var ?Fluent|ForeignKeyDefinition $command */
        $command = $this->commands[0] ?? null;

        if ($command && $command->get('name') === 'create') {
            $table = $this->getTable();
            dump($this->commands, $modifiedColumns);

            $foreignKeys = collect($this->commands)
                ->filter(fn ($command) => $command instanceof ForeignKeyDefinition)
                ->map(fn (ForeignKeyDefinition $foreignKeyDefinition) => $foreignKeyDefinition->toArray())
                ->filter(fn (array $foreignKey) => $foreignKey['on'] === $table);

            $findForeignKey = fn (ColumnDefinition $column) => collect($foreignKeys)
                ->filter(fn (array $foreignKey) => collect($foreignKey['columns'])->contains($column->toArray()['name']))
                ->first();

            return $this->roll->createTable(
                name: $table,
                columns: [
                    ...$addedColumns
                        ->map(fn(ColumnDefinition $columnDefinition) => $this->roll->columnFromDefinition($columnDefinition, foreignKey: $findForeignKey($columnDefinition))),
                    ...$modifiedColumns
                        ->map(fn(ColumnDefinition $columnDefinition) => $this->roll->columnFromDefinition($columnDefinition, foreignKey: $findForeignKey($columnDefinition)))
                ]
            );
        } else {
            dump($this->commands, $modifiedColumns);
            return [];
        }
    }
}


class PGRoll
{
    public function migration(string $name, array $operations): array
    {
        return [
            'name' => $name,
            'operations' => $operations
        ];
    }

    public function createTable(string $name, array $columns): array
    {
        return [
            'create_table' => [
                'name' => $name,
                'columns' => $columns
            ]
        ];
    }

    public function columnFromDefinition(ColumnDefinition $column, ?ForeignKeyDefinition $foreignKey = null, bool $useAlterNameColumn = false): array
    {
        $data = $column->toArray();

        if ($data['type'] === 'string' && $data['length'] === null) {
            $data['type'] = 'text';
        } elseif ($data['type'] === 'string' && $data['length'] !== null) {
            $data['type'] = "varchar({$data['length']})";
        }

        $references = null;

        if ($foreign = $foreignKey?->toArray()) {
            $references = $this->references(
                table: (string) $foreign['on'],
                column: (string) $foreign['references'],
                indexName: (string) $foreign['index'],
                onDelete:$foreign['onDelete'] ?? null,
                onUpdate: $foreign['onUpdate'] ?? null,
            );
        }

        return $this->column(
            name: $data['name'],
            type: $data['type'],
            comment: $data['comment'] ?? null,
            nullable: $data['nullable'] ?? false,
            unique: $data['unique'] ?? false,
            primary: $data['pk'] ?? false,
            default: $data['default'] ?? null,
//            check: $data['check'] ?? null,
            references: $references,
            useAlterNameColumn: $useAlterNameColumn,
        );
    }

    public function column(
        string  $name,
        string  $type,
        ?string $comment = null,
        bool    $nullable = true,
        bool    $unique = false,
        bool    $primary = false,
        mixed   $default = null,
        ?array  $check = null,
        ?array  $references = null,
        bool    $useAlterNameColumn = false,
    ): array
    {
        $column = [
            'type' => $type,
            'nullable' => $nullable,
            'unique' => $unique,
            'pk' => $primary,
        ];

        if ($useAlterNameColumn) {
            $column['name'] = $name;
        } else {
            $column['column'] = $name;
        }

        if ($comment !== null) {
            $column['comment'] = $comment;
        }

        if ($default !== null) {
            $column['default'] = $default;
        }

        if ($check !== null) {
            $column['check'] = $check;
        }

        if ($references !== null) {
            $column['references'] = $references;
        }

        return $column;
    }

    public function references(string $table, string $column, string $indexName, ?string $onDelete = null, ?string $onUpdate = null): array
    {
        $references = [
            'table' => $table,
            'column' => $column,
            'name' => $indexName,
        ];

        if ($onDelete !== null) {
            $references['onDelete'] = $onDelete;
        }

        if ($onUpdate !== null) {
            $references['onUpdate'] = $onUpdate;
        }

        return $references;
    }

    public function addColumn(string $table, array $column, ?string $up = null): array
    {
        return [
            'add_column' => [
                'table' => $table,
                'column' => $column,
                'up' => $up,
            ]
        ];
    }

    public function alterColumn(string $table, string $column, array $changes): array
    {
        return [
            'alter_column' => array_merge(
                ['table' => $table, 'column' => $column],
                $changes
            )
        ];
    }

    public function dropColumn(string $table, string $column, ?string $down = null): array
    {
        return [
            'drop_column' => [
                'table' => $table,
                'column' => $column,
                'down' => $down,
            ]
        ];
    }

    public function createIndex(string $table, string $name, array $columns): array
    {
        return [
            'create_index' => [
                'table' => $table,
                'name' => $name,
                'columns' => $columns,
            ]
        ];
    }

    public function dropIndex(string $name): array
    {
        return [
            'drop_index' => [
                'name' => $name,
            ]
        ];
    }

    public function dropTable(string $name): array
    {
        return [
            'drop_table' => [
                'name' => $name,
            ]
        ];
    }

    public function renameTable(string $from, string $to): array
    {
        return [
            'rename_table' => [
                'from' => $from,
                'to' => $to,
            ]
        ];
    }

    public function rawSql(string $up, ?string $down = null, bool $onComplete = false): array
    {
        $sql = [
            'sql' => [
                'up' => $up,
            ]
        ];

        if ($down !== null) {
            $sql['sql']['down'] = $down;
        }

        if ($onComplete) {
            $sql['sql']['onComplete'] = true;
        }

        return $sql;
    }

    public function renameConstraint(string $table, string $from, string $to): array
    {
        return [
            'rename_constraint' => [
                'table' => $table,
                'from' => $from,
                'to' => $to,
            ]
        ];
    }

    public function setReplicaIdentity(string $table, string $type, ?string $index = null): array
    {
        $identity = [
            'type' => $type,
        ];

        if ($type === 'index' && $index !== null) {
            $identity['index'] = $index;
        }

        return [
            'set_replica_identity' => [
                'table' => $table,
                'identity' => $identity,
            ]
        ];
    }
}
