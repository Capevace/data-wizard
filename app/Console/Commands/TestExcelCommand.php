<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Mateffy\Magic\Extraction\SpreadsheetConverter;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Reader\Word2007;

class TestExcelCommand extends Command
{
	protected $signature = 'test:excel';

	protected $description = 'Command description';

	public function handle(): void
	{
        $converter = app(SpreadsheetConverter::class);

        $data = $converter->convertToTextSlices(storage_path('app/public/sample.xlsx'));

        dd($data->first());
	}
}
