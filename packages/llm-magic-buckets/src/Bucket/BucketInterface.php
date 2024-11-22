<?php

namespace Mateffy\Magic\Bucket;

use Mateffy\Magic\Buckets\ChatFile;
use Mateffy\Magic\Buckets\FileCollection;

interface BucketInterface
{
    public function create(
        string $name,
        string $mime_type,
        ?string $contents,
        ?string $ai_summary = null,
    ): ChatFile;

    public function list(
        string $path = '',
        ?string $name_starts_with = null,
        ?string $name_ends_with = null,
        ?string $name_contains = null,
        ?array $mime_types = null,
        ?int $limit = null,
        ?int $offset = null,
        string $order_by = 'name',
        string $order_direction = 'asc',
        bool $include_deleted = false,
    ): FileCollection;
    public function get(string $path): ?ChatFile;

    public function read(string $path, int $offset = 0, int $limit = null): mixed;
    public function write(string $path, mixed $value, bool $createIfMissing = false): void;

    public function exists(string $path): bool;

    public function delete(string $path): void;

    public function clear(): void;
}
