<?php

namespace Mateffy\Magic\Buckets\Functions;

use App\Models\ExtractionBucket;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Mateffy\Magic\Artifacts\LocalArtifact;
use Mateffy\Magic\Buckets\CloudArtifact;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Message\FunctionCall;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Mateffy\Magic\LLM\Message\MultimodalMessage;
use Mateffy\Magic\LLM\Models\Claude3Family;
use Mateffy\Magic\Magic;
use Mateffy\Magic\Strategies\SimpleStrategy;

class SummarizeFile implements InvokableFunction
{
    use AutoprocessInvokable;

    public static string $name = 'bucket_summarizeFile';

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
        /** @var ?CloudArtifact $artifact */
        $artifact = $this->bucket->cloud_artifacts()
            ->whereFilename($name)
            ->first();

        if (! $artifact) {
            return Magic::error("File not found: {$name}", 'buckets::file_not_found');
        }

        $summary = Magic::extract()
            ->model(Claude3Family::haiku())
            ->schema([
                'type' => 'object',
                'properties' => [
                    'summary' => [
                        'type' => 'string',
                        'description' => 'A summary of the file contents. The summary should give the user a good idea of what the file contains. The user will not be able to ask questions about it, so include all information you consider relevant in the summary.',
                    ],
                ],
                'required' => ['summary'],
            ])
            ->system('<metadata>' . json_encode(Arr::except($artifact->file()->toArray(), ['ai_summary'])) . '</metadata>')
            ->strategy('simple')
            ->artifact($artifact)
            ->stream();

        $artifact->ai_summary = $summary['summary'];
        $artifact->save();

        return $artifact->file()->toArray();
    }
}
