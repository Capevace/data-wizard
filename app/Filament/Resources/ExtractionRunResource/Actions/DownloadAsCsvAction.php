<?php

namespace App\Filament\Resources\ExtractionRunResource\Actions;

use App\Models\ExtractionRun;
use Filament\Actions\Action;
use Illuminate\Support\Arr;
use League\Csv\JsonConverter;
use League\Csv\Writer;

class DownloadAsCsvAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->name('downloadAsCsv')
            ->label('Download as CSV')
            ->translateLabel()
            ->icon('bi-filetype-csv')
            ->color('gray')
            ->action(function (ExtractionRun $record) {
                // JSON Schema. Will be an object schema (type=object)
                $schema = $record->target_schema;

                assert($schema !== null, 'Schema must be set');
                assert($schema['type'] === 'object', 'Schema must be an object schema');

                $properties = collect($schema['properties']);

                $data = $record->result_json ?? $record->partial_result_json;

                // Create a CSV writer instance
                $csv = Writer::createFromString('');

                // If the schema is like this ({ products: [] }), then we can be nice and flatten it to only contain the products
                if ($properties->containsOneItem() && Arr::get($properties->first(), 'type') === 'array') {
                    $key = $properties->keys()->first();
                    $property = $properties->first()['items'];

                    if ($property['type'] === 'object') {
                        $schema = $property['properties'];
                    } else {
                        $schema = [
                            $key => $property
                        ];
                    }

                    $data = Arr::get($data, $key, []);
                }

                // Insert the CSV header based on the schema
                $header = array_keys($schema);
                $csv->insertOne($header);

                // Insert the data rows
                foreach ($data as $key => $row) {
                    $csv->insertOne(array_map(fn ($column) => Arr::get($row, $column, ''), $header));
                }

                // Output the CSV to the user
                $content =$csv->toString();

                return response()->streamDownload(function () use ($content) {
                    echo $content;
                }, 'data.csv');
            });
    }
}
