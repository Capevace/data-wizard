<?php

namespace App\Console\Commands;

use Blaspsoft\Doxswap\Facades\Doxswap;
use Illuminate\Console\Command;
use PhpOffice\PhpWord\IOFactory;

class TestConverterCommand extends Command
{
	protected $signature = 'test:convert';

	protected $description = 'Command description';

	public function handle(): void
	{
//        $convertedFile = Doxswap::convert('file-sample_1MB.docx', 'pdf');
        $convertedFile = Doxswap::convert('test.xlsx', 'pdf');
        dd($convertedFile);
	}
}
