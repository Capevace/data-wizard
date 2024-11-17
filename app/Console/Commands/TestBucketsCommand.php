<?php

namespace App\Console\Commands;

use App\Models\ExtractionBucket;
use Illuminate\Console\Command;
use Laravel\Octane\Exceptions\DdException;
use Mateffy\Magic\Buckets\Functions\CreateFile;
use Mateffy\Magic\Buckets\Functions\ListFiles;
use Mateffy\Magic\Buckets\Functions\ReadRawFile;
use Mateffy\Magic\Buckets\Functions\SummarizeFile;
use Mateffy\Magic\LLM\Message\FunctionCall;

class TestBucketsCommand extends Command
{
    protected $signature = 'test:buckets';

    protected $description = 'Command description';

    /**
     * @throws DdException
     * @throws \JsonException
     */
    public function handle(): void
    {
        $bucket = ExtractionBucket::create();

        $file = (new CreateFile($bucket))(
            'test.txt',
            'text/plain',
            contents: 'Hello, world!',
            ai_summary: 'This is a test file.'
        );

        $files = (new ListFiles($bucket))();

        $contents = (new ReadRawFile($bucket))(
            call: new FunctionCall(
                name: 'test',
                arguments: [
                    'name' => 'test.txt',
                    'offset' => 0,
                    'limit' => 5000,
                ],
                id: 'test',
            ),
            name: 'test.txt',
        );

        $summarize = (new SummarizeFile($bucket))(
            call: new FunctionCall(
                name: 'test',
                arguments: [
                    'name' => 'test.txt',
                    'offset' => 0,
                    'limit' => 5000,
                ],
                id: 'test',
            ),
            name: 'test.txt',
        );

        dd($files, $contents, $summarize);
    }
}
