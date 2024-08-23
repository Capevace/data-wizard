<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Str;

class ClaudeStreamController extends Controller
{
    public function __invoke()
    {
        return response()->stream(
            function () {
                $apiKey = getenv('ANTHROPIC_API_KEY');

                $client = new Client([
                    'base_uri' => 'https://api.anthropic.com',
                    'headers' => [
                        'anthropic-version' => '2023-06-01',
                        'anthropic-beta' => 'messages-2023-12-15',
                        'content-type' => 'application/json',
                        'x-api-key' => $apiKey,
                    ],
                ]);

                $data = [
                    'model' => 'claude-3-opus-20240229',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => 'Output some JSON examples please',
                        ],
                    ],
                    'max_tokens' => 4000,
                    'stream' => true,
                ];

                $request = new Request('POST', '/v1/messages', [], json_encode($data));

                $response = $client->send($request, ['stream' => true]);

                $body = $response->getBody();

                $unfinished = null;
                $events = collect();

                $messages = collect();
                $message = null;

                while (!$body->eof()) {
                    $output = $body->read(1024);

                    if ($unfinished !== null) {
                        $output = $unfinished . $output;
                        $unfinished = null;
                    }

                    $newEvents = str($output)
                        ->explode("\n\n");

                    $lastEvent = $newEvents->pop();

                    if (!Str::endsWith($lastEvent, "\n\n")) {
                        $unfinished = $lastEvent;
                    } else {
                        $newEvents->push($lastEvent);
                    }

                    $newParsedEvents = collect();

                    foreach ($newEvents as $event) {
                        [$event, $data] = str($event)
                            ->explode("\n");

                        $event = Str::after($event, 'event: ');
                        $data = json_decode(Str::after($data, 'data: '), true);

                        $newParsedEvents->push([$event, $data]);
                    }

                    foreach ($newParsedEvents as [$event, $data]) {
                        // End the previous message, if one exists
                        if ($event === 'message_start' && $message !== null) {
                            $messages->push($message);
                            $message = null;
                        }

                        if ($event === 'message_start') {
                            $message = '';
                        } elseif ($event === 'content_block_start') {
                            $part = $data['content_block']['text'];
                            echo $part;

                            $message .= $part;
                        } elseif ($event === 'content_block_delta') {
                            $part = $data['delta']['text'];
                            echo $part;

                            $message .= $part;
                        }
                    }

                    flush();
                }

                if ($message !== null) {
                    $messages->push($message);
                }

                dd($messages);
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
