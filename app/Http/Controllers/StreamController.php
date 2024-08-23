<?php

namespace App\Http\Controllers;

use Capevace\MagicImport\Model\Anthropic\Claude3;
use Capevace\MagicImport\Model\Open\GPT4;
use Capevace\MagicImport\Prompt\ClaudeExtractorPrompt;
use Capevace\MagicImport\Prompt\DataExtractorPrompt;
use Capevace\MagicImport\Prompt\FunctionBasedExtractorPrompt;

use Capevace\MagicImport\Loop\Loop;
use Capevace\MagicImport\Prompt\Message\MultimodalMessage;
use Capevace\MagicImport\Prompt\Message\TextMessage;
use Capevace\MagicImport\Prompt\Role;
use Illuminate\Support\Facades\Log;

class StreamController extends Controller
{
    public function __invoke()
    {
        return response()->stream(
            function () {
                $model = new Claude3(maxTokens: 4096);
                $prompt = new ClaudeExtractorPrompt();

                echo view('magic.streamer')->render();


                $log = fn ($message) => function ($obj) use ($message) {
                    $json = json_encode([
                        'message' => $message,
                        'obj' => $obj
                    ], JSON_PRETTY_PRINT);

                    $json = base64_encode($json);

                    echo <<<HTML
                    <script>window.packet = "{$json}";</script>
                    HTML;


                    if(ob_get_level() > 0) {
                        ob_flush();
                    }

                    flush();
                };

                $loop = new Loop(
                    model: $model,
                    prompt: $prompt,

                    stream: true,
                    manualFunctions: false,

                    onMessageProgress: $log("onMessageProgress"),
                    onMessage: $log("onMessage"),
                    onFunctionCalled: $log("onFunctionCalled"),
                    onFunctionOutput: $log("onFunctionOutput"),
                    onFunctionError: $log("onFunctionError"),
                    onStep: $log("onStep"),
                    onStream: $log("onStream"),
                    onEnd: $log("onEnd")
                );

                $text = file_get_contents(
                    base_path("../magic-import/fixtures/elsenstrasse/expose-no-images.txt")
                );

                set_time_limit(120);

                $images = collect(range(1, 20))
                    ->map(fn($i) => MultimodalMessage\Base64Image::fromPath(
                        base_path("../magic-import/fixtures/elsenstrasse/slides_marked/slide{$i}.jpg")
                    ));

                $loop->start([
                    new MultimodalMessage(
                        role: Role::User,
                        content: [
                            ...$images,
                            new MultimodalMessage\Text("Extrahiere Daten aus folgendem Text:\n\n{$text}"),
                        ]
                    )
                ]);
            },
            200,
            [
                'Cache-Control' => 'no-cache',
                // not text/event-stream
                'Content-Type' => 'text/html',
                'X-Accel-Buffering' => 'no',
                'X-Livewire-Stream' => true,
            ]
        );

    }
}
