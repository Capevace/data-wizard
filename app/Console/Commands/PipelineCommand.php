<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use function Codewithkyrian\Transformers\Pipelines\pipeline;

class PipelineCommand extends Command
{
    protected $signature = 'pipeline';

    protected $description = 'Command description';

    public function handle(): void
    {
        $model = 'TaylorAI/gte-tiny';
        $calculateEmbeddings = pipeline('embeddings', $model);

        $output = $calculateEmbeddings('My name is Kyrian and I live in Onitsha');

        dd($output[0][0]);
    }
}
