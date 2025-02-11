<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mateffy\Magic\Embeddings\OpenAIEmbeddingModel;
use Mateffy\Magic;
use NlpTools\Similarity\CosineSimilarity;
use function Laravel\Prompts\search;
use function Laravel\Prompts\text;

class EmbeddingsCommand extends Command
{
    protected $signature = 'embeddings';

    protected $description = 'Command description';

    public function handle(): void
    {
        $recents = [];
        $cosine = new CosineSimilarity;
        $embeddings = Magic::embeddings()
            ->model(OpenAIEmbeddingModel::text_ada_002());

        while (true) {
            $embedding1Text = search('Embedding 1', options: fn (string $search) => array_filter([$search ?: null, ...$recents]));
            $embedding2Text = search('Embedding 2', options: fn (string $search) => array_filter([$search ?: null, ...$recents]), validate: false);

            $recents[] = $embedding1Text;
            $recents[] = $embedding2Text;

            $embedding1 = $embeddings->input($embedding1Text)->get();
            $embedding2 = $embeddings->input($embedding2Text)->get();

            $embedding1Vectors = $embedding1->vectors;
            $embedding2Vectors = $embedding2->vectors;

            $similarity = $cosine->similarity($embedding1Vectors, $embedding2Vectors);

            $this->info("Similarity: {$similarity}");
        }
    }
}
