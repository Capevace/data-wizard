<?php

namespace App\Console\Commands;

use HelgeSverre\Extractor\Facades\Text;
use Illuminate\Console\Command;
use Mateffy\Magic\LLM\Message\TextMessage;
use Mateffy\Magic\LLM\Models\Claude3Family;
use Mateffy\Magic;

class RunCommand extends Command
{
    protected $signature = 'run';

    protected $description = 'Command description';

    public function handle(): void
    {
        // leftover = (581 % 17) = 3
        // each = (581 - leftover) / 17 = 34

        $answer = Magic::chat()
            ->model(Claude3Family::haiku())
            ->system('You are a calculator. Always use tools to perform calculations, NEVER guess the answer.')
            ->messages([
                TextMessage::user('If I have 581 apples and I need to give them to 17 elephants, how many apples will each elephant get? An apple is not divisible, so how many elephants will get an extra apple?'),
            ])
            ->tools([
                'add' => fn (float $a, float $b) => $a + $b,
                'subtract' => fn (float $a, float $b) => $a - $b,
                'multiply' => fn (float $a, float $b) => $a * $b,
                'divide' => fn (float $a, float $b) => $a / $b,
                'modulo' => fn (float $a, float $b) => $a % $b,
            ])
            ->stream()
            ->formattedText();

        $this->info($answer);
    }
}
