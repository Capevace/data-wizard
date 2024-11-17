<?php

namespace Mateffy\Magic\Buckets\Functions;

use App\Models\ExtractionBucket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Mateffy\Magic\Artifacts\Content\Content;
use Mateffy\Magic\Artifacts\Content\ImageContent;
use Mateffy\Magic\Artifacts\Content\TextContent;
use Mateffy\Magic\Artifacts\LocalArtifact;
use Mateffy\Magic\Buckets\CloudArtifact;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Message\FunctionCall;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Mateffy\Magic\LLM\Message\MultimodalMessage;
use Mateffy\Magic\Magic;

class ReadRawFile implements InvokableFunction
{
    use AutoprocessInvokable;

    public static string $name = 'bucket_readRawFile';

    public function __construct(
        protected ExtractionBucket $bucket,
        protected bool $supportsVision = false
    )
    {
    }

    /**
     * @description $limit Limit the number of tokens (Max: 5000). Use offset to paginate.
     * @throws \JsonException
     */
    public function __invoke(
        FunctionCall $call,
        string $name,
        int $offset = 0,
        int $limit = 5000
    )
    {
        $nameWithoutExtension = pathinfo($name, PATHINFO_FILENAME);
        $extension = pathinfo($name, PATHINFO_EXTENSION);

        /** @var ?CloudArtifact $artifact */
        $artifact = $this->bucket->cloud_artifacts()
            ->where('name', $nameWithoutExtension)
            ->where('extension', $extension)
            ->first();

        if (! $artifact) {
            return Magic::error("File not found: {$name}", 'buckets::file_not_found');
        }

        $artifactMessages = collect($artifact->split($limit))
            ->skipUntil(fn($chunk, int $index) => $index * $limit >= $offset)
            ->take(1)
            ->flatMap(fn(Collection $contents) => $contents
                ->map(fn(Content $content) => match ($content::class) {
                    TextContent::class => MultimodalMessage\Text::make($content->text),
                    ImageContent::class => MultimodalMessage\Base64Image::fromDisk('local', $content->path),
                })
            );

        if (false && $artifactMessages->some(fn($message) => $message instanceof MultimodalMessage\Base64Image)) {
            $messages = [
                FunctionOutputMessage::output($call, [
                    'name' => $artifact->name,
                    'mime_type' => $artifact->mime_type,
                    'size' => $artifact->size,
                    'created_at' => $artifact->created_at->toIso8601String(),
                    'ai_summary' => $artifact->ai_summary,
                ]),
                ...$artifactMessages,
                MultimodalMessage\Text::make('Here is a dwarf for you:'),
                MultimodalMessage\Base64Image::fromPath(public_path('images/dwarf.png')),
            ];

            return MultimodalMessage::user($messages);
        }



//        $isImage = in_array($file->mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

        return [
            'name' => $artifact->name,
            'mime_type' => $artifact->mime_type,
            'size' => $artifact->size,
            'created_at' => $artifact->created_at->toIso8601String(),
            'contents' => $artifactMessages->map(fn($message) => $message->toArray())->all(),
        ];
    }
}
