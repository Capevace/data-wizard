<?php

namespace App\Filament\Resources\ExtractionRunResource\Actions;

use App\Models\ExtractionRun;
use Filament\Actions\Action;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use League\Csv\Writer;
use SimpleXMLElement;

class DownloadAsXmlAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->name('downloadAsXml')
            ->label('Download as XML')
            ->translateLabel()
            ->icon('bi-filetype-xml')
            ->color('gray')
            ->action(function (ExtractionRun $record) {
                // JSON Schema. Will be an object schema (type=object)
                $data = $record->result_json ?? $record->partial_result_json;

                $xml = $this->convertToXml($data);

                // Convert the SimpleXMLElement to DOMDocument for pretty printing
                $dom = dom_import_simplexml($xml)->ownerDocument;
                $dom->formatOutput = true;

                $formatted_xml = $dom->saveXML();

                return response()->streamDownload(function () use ($formatted_xml) {
                    echo $formatted_xml;
                }, 'data.xml');
            });
    }

    protected function convertToXml(array $data, ?SimpleXMLElement $xml = null, ?string $parent_key = null): SimpleXMLElement
    {
        $xml ??= new SimpleXMLElement('<data/>');

        foreach($data as $key => $value) {
            // Convert the key to abc-def format
            $key = Str::slug($key, '-');

            if (is_array($value)) {
                $child = $xml->addChild($parent_key ?? $key);

                $this->convertToXml($value, $child, Str::singular($key));
            } else {
                $xml->addChild($key, $value);
            }
        }

        return $xml;
    }
}
