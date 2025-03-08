<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Reader\Word2007;

class TestWordCommand extends Command
{
	protected $signature = 'test:word';

	protected $description = 'Command description';

	public function handle(): void
	{
		$reader = IOFactory::createReader('Word2007');
        $doc = $reader->load('/Users/mat/Downloads/file-sample_1MB.docx');
        /** @var PhpWord $doc */


        /** @var Collection<Section> $sections */
        $sections = collect($doc->getSections());

        foreach ($sections as $section) {
            /** @var Collection<AbstractElement> $elements */
            $elements = collect($section->getElements());

            foreach ($elements as $element) {
                if ($element instanceof TextRun) {
                    $this->info($element->getText());
                }
            }
        }
	}
}
