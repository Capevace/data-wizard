<?php

namespace Mateffy\Magic\Bucket;

use App\Models\ExtractionBucket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Mateffy\Magic\Artifacts\Content\Content;
use Mateffy\Magic\Artifacts\Content\ImageContent;
use Mateffy\Magic\Artifacts\Content\TextContent;
use Mateffy\Magic\Buckets\ChatFile;
use Mateffy\Magic\Buckets\CloudArtifact;
use Mateffy\Magic\Buckets\ContentCollection;
use Mateffy\Magic\Buckets\FileCollection;
use Mateffy\Magic\LLM\Message\MultimodalMessage;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class CloudBucketAPI implements BucketInterface
{
    public function __construct(
        protected ExtractionBucket $bucket,
    )
    {
    }

    /**
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     */
    public function create(string $name, string $mime_type, ?string $contents, ?string $ai_summary = null): ChatFile
    {
        return DB::transaction(function () use ($name, $mime_type, $contents, $ai_summary) {
            $extension = pathinfo($name, PATHINFO_EXTENSION);

            if (empty($extension)) {
                $extension = 'txt';
                $name .= ".{$extension}";
            }

            [$nameWithoutExtension, $extension] = $this->names($name);

            /**
             * @var CloudArtifact $artifact
             */
            $artifact = $this->bucket->cloud_artifacts()->create([
                'name' => $nameWithoutExtension,
                'extension' => $extension,
                'mime_type' => $mime_type,
                'size' => strlen($contents),
                'ai_summary' => $ai_summary,
            ]);

            $artifact
                ->addMediaFromString($contents)
                ->usingName($nameWithoutExtension)
                ->usingFileName($name)
                ->toMediaCollection();

            $artifact->chunks()->create([
                'type' => 'text',
                'text' => $contents,
                'page' => 0,
                'tokens' => strlen($contents),
            ]);

            return ChatFile::fromCloudArtifact($artifact);
        });
    }

    public function list(string $path = '', ?string $name_starts_with = null, ?string $name_ends_with = null, ?string $name_contains = null, ?array $mime_types = null, ?int $limit = null, ?int $offset = null, string $order_by = 'name', string $order_direction = 'asc', bool $include_deleted = false): FileCollection
    {
        $query = $this->bucket->cloud_artifacts()
            ->when($name_starts_with, fn($q) => $q->where('name', 'ilike', "{$name_starts_with}%"))
            ->when($name_ends_with, fn($q) => $q->where('name', 'ilike', "%{$name_ends_with}"))
            ->when($name_contains, fn($q) => $q->where('name', 'ilike', "%{$name_contains}%"))
            ->when($mime_types, fn($q) => $q->whereIn('mime_type', $mime_types))
            ->when($limit, fn($q) => $q->limit($limit))
            ->when($offset, fn($q) => $q->offset($offset))
            ->when($order_by, fn($q) => $q->orderBy($order_by, $order_direction));

        $files = collect($query->get())
            ->map(fn(CloudArtifact $artifact) => ChatFile::fromCloudArtifact($artifact));

        return new FileCollection($files);
    }

    public function get(string $path): ?ChatFile
    {
        /** @var ?CloudArtifact $artifact */
        $artifact = $this->query($path)->first();

        if (! $artifact) {
            return null;
        }

        return ChatFile::fromCloudArtifact($artifact);
    }

    public function read(string $path, int $offset = 0, int $limit = null): mixed
    {
        /** @var ?CloudArtifact $artifact */
        $artifact = $this->query($path)->first();

        return ContentCollection::make($artifact->split($limit))
            ->skipUntil(fn($chunk, int $index) => $index * $limit >= $offset)
            ->take(1)
            ->flatMap(fn(Collection $contents) => $contents
                ->map(fn(Content $content) => match ($content::class) {
                    TextContent::class => MultimodalMessage\Text::make($content->text),
                    ImageContent::class => MultimodalMessage\Base64Image::fromDisk('local', $content->path),
                })
            );
    }

    public function write(string $path, mixed $value, bool $createIfMissing = false): void
    {
        [$nameWithoutExtension, $extension] = $this->names($path);

        $artifact = $this->query($path)->first();

        if (! $artifact) {
            if (! $createIfMissing) {
                return;
            }

            $artifact = $this->bucket->cloud_artifacts()->create([
                'name' => $nameWithoutExtension,
                'extension' => $extension,
                'mime_type' => 'text/plain',
                'size' => 0,
                'ai_summary' => null,
            ]);
        }

        $contents = $value;

        $artifact->chunks()->create([
            'type' => 'text',
            'text' => $contents,
            'page' => 0,
            'tokens' => strlen($contents),
        ]);
    }

    public function exists(string $path): bool
    {
        return $this->query($path)->exists();
    }

    public function delete(string $path): void
    {
        $this->query($path)
            ->first() // Make sure to use the model to trigger the delete events
            ?->delete();
    }

    public function clear(): void
    {
        foreach ($this->bucket->cloud_artifacts as $artifact) {
            $artifact->delete();
        }
    }

    /**
     * @param string $path
     * @return Builder<CloudArtifact>
     */
    protected function query(string $path): Builder
    {
        [$nameWithoutExtension, $extension] = $this->names($path);

        return $this->bucket->cloud_artifacts()
            ->where('name', $nameWithoutExtension)
            ->where('extension', $extension);
    }

    protected function names(string $path): array
    {
        $nameWithoutExtension = pathinfo($path, PATHINFO_FILENAME);
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return [$nameWithoutExtension, $extension];
    }
}
