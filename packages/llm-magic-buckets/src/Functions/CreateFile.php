<?php

namespace Mateffy\Magic\Buckets\Functions;

use App\Models\ExtractionBucket;
use Illuminate\Support\Facades\DB;
use Mateffy\Magic\Buckets\CloudArtifact;
use Mateffy\Magic\Functions\InvokableFunction;

class CreateFile implements InvokableFunction
{
    use AutoprocessInvokable;

    public static string $name = 'bucket_createFile';

    public function __construct(protected ExtractionBucket $bucket)
    {
    }

    public function __invoke(
        string $name,
        string $mime_type,
        ?string $contents,
        ?string $ai_summary = null,
    )
    {
        return DB::transaction(fn() => $this->runInTransaction(
            name: $name,
            mime_type: $mime_type,
            contents: $contents,
            ai_summary: $ai_summary,
        ));
    }

    protected function runInTransaction(
        string $name,
        string $mime_type,
        ?string $contents,
        ?string $ai_summary = null,
    ): array
    {
        $extension = pathinfo($name, PATHINFO_EXTENSION);

        if (empty($extension)) {
            $extension = 'txt';
            $name .= ".{$extension}";
        }

        $nameWithoutExtension = pathinfo($name, PATHINFO_FILENAME);

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

        $file = $artifact
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

        return [
            'name' => $file->name,
            'extension' => $file->extension,
            'mime_type' => $file->mime_type,
            'size' => $file->size,
            'created_at' => $file->created_at,
            'ai_summary' => $ai_summary,
        ];
    }
}
