<?php

namespace Mateffy\Magic\Buckets\Functions;

use App\Models\ExtractionBucket;
use Illuminate\Support\Collection;
use Mateffy\Magic\Buckets\ChatFile;
use Mateffy\Magic\Buckets\CloudArtifact;
use Mateffy\Magic\Functions\InvokableFunction;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ListFiles implements InvokableFunction
{
    use AutoprocessInvokable;

    public static string $name = 'bucket_listFiles';

    public function __construct(protected ExtractionBucket $bucket)
    {
    }

    /**
     * @return Collection<ChatFile>
     */
    public function __invoke(
        bool $include_deleted,
        ?string $name_starts_with = null,
        ?string $name_ends_with = null,
        ?string $name_contains = null,
        ?array $mime_types = null,
        ?int $limit = null,
        ?int $offset = null,
        string $order_by = 'name',
        string $order_direction = 'asc',
    ): Collection
    {
        $query = $this->bucket->cloud_artifacts()
            ->when($name_starts_with, fn($q) => $q->where('name', 'ilike', "{$name_starts_with}%"))
            ->when($name_ends_with, fn($q) => $q->where('name', 'ilike', "%{$name_ends_with}"))
            ->when($name_contains, fn($q) => $q->where('name', 'ilike', "%{$name_contains}%"))
            ->when($mime_types, fn($q) => $q->whereIn('mime_type', $mime_types))
            ->when($limit, fn($q) => $q->limit($limit))
            ->when($offset, fn($q) => $q->offset($offset))
            ->when($order_by, fn($q) => $q->orderBy($order_by, $order_direction));

        return collect($query->get())
            ->map(fn(CloudArtifact $artifact) => ChatFile::fromCloudArtifact($artifact));
    }
}
