<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mateffy\Magic\Embeddings\OpenAIEmbeddingModel;
use Mateffy\Magic;

class AreaEmbeddingsCommand extends Command
{
    protected $signature = 'embeddings:area';

    protected $description = 'Command description';

    public function handle(): void
    {
        $areas = [
            <<<PROMPT
            Total Area: 257 m²
            Usages: office, storage
            Spaces:
                EG: 160 m² office, 97 m² storage
            Features:
                - Ceiling height: 2.7 m
                - Barrier-free
            PROMPT,
            <<<PROMPT
            Total Area: 1500 m²
            Usages: storage, production, office
            Spaces:
                1OG: 100 m² office
                EG: 1000 m² production/storage, 500 m² storage
            Features:
                - Ceiling height: 8.5 m, 2.5 m
                - Cooled halls
            PROMPT,
            <<<PROMPT
            Total Area: 1000 m²
            Usages: office, storage
            Spaces:
                EG: 500 m² office, 500 m² storage
            PROMPT
        ];

        foreach ($areas as $area) {
            $embeddings[] = Magic::embeddings($area, OpenAIEmbeddingModel::text_3_small())
                ->get();
        }

        file_put_contents(storage_path('embeddings.json'), json_encode($embeddings, JSON_PRETTY_PRINT));
    }
}
