<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mateffy\Magic\Artifacts\LocalArtifact;
use Mateffy\Magic\LLM\Message\FunctionCall;
use Mateffy\Magic\LLM\Message\ToolCallMessage;
use Mateffy\Magic\LLM\Message\Step;
use Mateffy\Magic\LLM\Message\TextMessage;
use Mateffy\Magic\LLM\Models\Claude3Family;
use Mateffy\Magic;
use Mateffy\Magic\Prompt\Role;
use function Laravel\Prompts\spin;

class FloorplanCommand extends Command
{
    protected $signature = 'floorplan';

    protected $description = 'Command description';

    public function handle(): void
    {
        $points = collect();

        $messages = spin(fn () => Magic::chat()
            ->model(Claude3Family::sonnet_3_5_computer_use())
            ->tools([
                'computer' => fn (string $action, ?array $coordinate) => dump(match ($action) {
                    'screenshot' => Step\Base64Image::fromPath('/Users/mat/Downloads/fl00r.png'),
                    'left_click', 'mouse_move' => "Success! {$action} " . $points->push($coordinate),
                    default => dd($action),
                }),
            ])
            ->system('You are a floorplan digitizer. You have the ability to mark areas on a floorplan using your computer use abilities. The full screen is filled with the floorplan image and you need to click the exact pixels of the corners one by one. You can only use the `screenshot` and `mouse_down` actions. Trust your instinct! You do not need to verify every single click, because we need to save some tokens. You can use the `computer` tool multiple times in your output! Start with the top right corner.')
            ->messages([
                Step::user([
                    Step\Text::make('Select the corners of the master bedroom.'),
//                    Step\Base64Image::fromPath('/Users/mat/Downloads/fl00r.png'),
                ]),
                new ToolCallMessage(
                    role: Role::Assistant,
                    call: new FunctionCall(
                        name: 'computer',
                        arguments: ['action' => 'screenshot'],
                        id: 'tool_use_1'
                    ),
                ),
                Step::user([
                    Step\ToolResult::output(new FunctionCall(
                        name: 'computer',
                        arguments: ['action' => 'screenshot'],
                        id: 'tool_use_1'
                    ), Step\Base64Image::fromPath('/Users/mat/Downloads/fl00r.png')),
                ]),
            ])
            ->stream(),
            'Loading floorplan...',
        );

        dd($messages, $points);
    }
}
