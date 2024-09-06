<?php

namespace App\Console\Commands;

use HelgeSverre\Extractor\Facades\Text;
use Illuminate\Console\Command;

class RunCommand extends Command
{
    protected $signature = 'run';

    protected $description = 'Command description';

    public function handle(): void
    {
        $sample = Text::text(file_get_contents('fixtures/elsenstrasse/expose.txt'));
        $schema = file_get_contents('../magic-import/schema-alt.json');

        $prompt = <<<TXT
        You need to carry out data extraction from the provided document and transform it into a structured JSON format.

        You will be provided JSON schemas for multiple resources that can be extracted.
        Try to see which resources are relevant and return these as a JSON array.
        Only fill the fields that are in the schema, do not add your own. Also, only fill in information that is directly knowable from the documents provided.
        Return all resources in a 1D-array and use JSON-LD references to include them inside resource properties. Do not nest data. E.g. all buildings and rentables are defined in the root array, and later referenced by their ID. Go by smallest possible data unit to largest (e.g. start with address, end with estate). Make sure to include the type of the resource (https://schema.immo/Building), but skip the \$schema redefinition.

        $schema
        TXT;

        echo $prompt;
    }
}
