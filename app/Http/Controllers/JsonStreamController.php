<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Models\Actor\ActorTelemetry;
use App\Models\ExtractionBucket;
use App\Models\ExtractionRun;
use App\Models\File;
use Capevace\MagicImport\Artifacts\ArtifactGenerationStatus;
use Capevace\MagicImport\Artifacts\LocalArtifact;
use Capevace\MagicImport\Config\Extractor;
use Capevace\MagicImport\Functions\ExtractData;
use Capevace\MagicImport\LLM\ElElEm;
use Capevace\MagicImport\LLM\Models\BedrockClaude3Family;
use Capevace\MagicImport\LLM\Models\Claude3Family;
use Capevace\MagicImport\Loop\InferenceResult;
use Capevace\MagicImport\Model\Anthropic\Claude3Haiku;
use Capevace\MagicImport\Model\Exceptions\LLMException;
use Capevace\MagicImport\Model\Exceptions\RateLimitExceeded;
use Capevace\MagicImport\Model\Exceptions\UnknownInferenceException;
use Capevace\MagicImport\Prompt\ClaudeExtractorPrompt;
use Capevace\MagicImport\Prompt\Message\FunctionInvocationMessage;
use Capevace\MagicImport\Prompt\Message\FunctionOutputMessage;
use Capevace\MagicImport\Prompt\Message\JsonMessage;
use Capevace\MagicImport\Prompt\Message\Message;
use Capevace\MagicImport\Prompt\Message\MultimodalMessage;
use Capevace\MagicImport\Prompt\Message\TextMessage;
use Capevace\MagicImport\Prompt\OutputFixedJson;
use Capevace\MagicImport\Prompt\Role;
use Capevace\MagicImport\Prompt\TokenStats;
use Capevace\MagicImport\Strategies\SimpleStrategy;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Swaggest\JsonSchema\Schema;

class JsonStreamController extends Controller
{
    public function __invoke(Request $request, ExtractionBucket $bucket, ExtractionRun $run)
    {
        $model = $request->query('model', 'fake');

        set_time_limit(120);

        /** @var File $file */
        $file = $bucket->files->first();

        if (!$file?->artifact || $file->artifact_status !== ArtifactGenerationStatus::Complete) {
            abort(404, 'Dataset not found or missing expose.txt file');
        }

        $schema = json_decode(json_encode([
            'type' => 'object',
            'properties' => [
                'estates' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'name' => ['type' => 'string'],
                            'buildings' => (new ExtractData)->schema()
                        ]
                    ]
                ]
            ]
        ]));

        $extractor = new Extractor(
            id: 'estate',
            title: 'Estate',
            description: null,
            allowedTypes: [
                'images',
                'documents'
            ],
            llm: ElElEm::fromString(ElElEm::id('anthropic', Claude3Family::SONNET_3_5)),
            schema: Schema::import($schema),
            strategy: 'simple',
        );

        $artifacts = [$file->artifact];

        return response()->stream(
            function () use ($extractor, $artifacts, $run) {
                try {
                    $actor = null;
                    $strategy = new SimpleStrategy(
                        extractor: $extractor,
                        onDataProgress: function (InferenceResult $result) {
                            $this->sendServerSentEvent('onDataProgress', $result->value);
                        },
                        onTokenStats: function (TokenStats $tokenStats) {
                            $this->sendServerSentEvent('onTokenStats', $tokenStats->toArray());
                        },
                        onActorTelemetry: function (ActorTelemetry $telemetry) use (&$actor, $run) {
                            if (!$actor) {
                                $actor = $run->actors()->create($telemetry->toDatabase());
                            } else {
                                $actor->update($telemetry->toDatabase());
                            }
                        },
                        onMessage: fn (Message $message) => $actor->add($message),
                    );

                    // Long-running task: executes the strategy and does multiple LLM calls
                    $result = $strategy->run(artifacts: $artifacts);

                    $this->sendServerSentEvent('onData', $result->value);
                } catch (\Exception $e) {
                    report($e);

                    $this->sendErrorEvent($e);
                }
            },
            200,
            [
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'text/event-stream',
                'X-Accel-Buffering' => 'no',
                'X-Livewire-Stream' => true,
            ]
        );
    }

    /**
     * @throws RateLimitExceeded
     * @throws UnknownInferenceException
     */
    public function startExtraction(string $dataset)
    {
        $path = base_path("../magic-import/fixtures/{$dataset}");
        $text = file_get_contents("{$path}/expose.txt");

        $fileCount = count(glob("{$path}/pages_marked/*.jpg"));
        $end = min(20, $fileCount);
        $images = collect(range(1, $end))
            ->map(fn($i) => MultimodalMessage\Base64Image::fromPath(
                base_path("../magic-import/fixtures/{$dataset}/pages_marked/page{$i}.jpg")
            ))
            ->all();

        $model = new Claude3Haiku(maxTokens: 4096);
        $prompt = new ClaudeExtractorPrompt(text: $text, images: $images);

        $model->stream(
            $prompt,
            onMessageProgress: $this->makeSSECallback('onMessageProgress'),
            onMessage: $this->makeSSECallback('onMessage'),
            onTokenStats: $this->makeSSECallback('onTokenStats'),
        );
    }

    public function startDryRunExtraction(string $dataset)
    {
        $model = new Claude3Haiku(maxTokens: 4096);

        $json = file_get_contents(base_path("../magic-import/fixtures/{$dataset}/expose-sonnet.json"));
        $prompt = new OutputFixedJson(json: $json);

        $model->stream(
            $prompt,
            onMessageProgress: $this->makeSSECallback('onMessageProgress'),
            onMessage: $this->makeSSECallback('onMessage'),
            onTokenStats: $this->makeSSECallback('onTokenStats'),
        );
    }

    protected function makeSSECallback(string $type): Closure
    {
        $self = $this;

        return function (Message|TokenStats $message) use ($type, $self) {
            $self->sendServerSentEvent($type, $message->toArray());
        };
    }

    public function sendServerSentEvent(string $type, array $data): void
    {
        echo "event: {$type}\n";

        try {
            $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

            echo "data: {$json}\n\n";
        } catch (\Exception $e) {
            echo "data: {\"error\": \"{$e->getMessage()}\"}\n\n";
        }

        if(ob_get_level() > 0) {
            ob_flush();
        }

        flush();
    }

    protected function sendErrorEvent(LLMException|\Exception $e): void
    {
        if ($e instanceof LLMException) {
            $this->sendServerSentEvent('onErrorMessage', ['error' => ['class' => Str::afterLast($e::class, '\\'), 'title' => $e->getTitle(), 'message' => $e->getMessage()]]);
        } else {
            $this->sendServerSentEvent('onErrorMessage', ['error' => ['class' => Str::afterLast($e::class, '\\'), 'message' => $e->getMessage()]]);
        }
    }
}


//                                 {
//                                     "estates": [
//                                         {
//                                             "name": "Estate 1",
//                                             "address": {
//                                                 "street": "Main St",
//                                                 "number": "123",
//                                                 "city": "Springfield",
//                                                 "postal_code": "62701"
//                                             },
//                                             "images": ["/images/image1.jpg"],
//                                             "buildings": [
//                                                 {
//                                                     "name": "Building A",
//                                                     "address": {
//                                                         "street": "Main St",
//                                                         "number": "123",
//                                                         "city": "Springfield",
//                                                         "postal_code": "62701"
//                                                     },
//                                                     "images": ["/images/image3.jpg"],
//                                                     "spaces": [
//                                                         {
//                                                             "name": "Space 1",
//                                                             "description": "A nice office space",
//                                                             "type": "office",
//                                                             "area": 500,
//                                                             "floor": 1,
//                                                             "rent_per_m2": 20,
//                                                             "rent_total": 10000,
//                                                             "images": ["/images/image1.jpg"],
//                                                             "features": ["air-conditioning", "elevators", "parking-garage"]
//                                                         },
//                                                         {
//                                                             "name": "Space 2",
//                                                             "description": "A retail space",
//                                                             "type": "retail",
//                                                             "area": 1000,
//                                                             "floor": 1,
//                                                             "rent_per_m2": 30,
//                                                             "rent_total": 30000,
//                                                             "images": ["/images/image2.jpg"],
//                                                             "features": ["close-to-public-transport", "handicap-accessible"]
//                                                         }
//                                                     ]
//                                                 }
//                                             ]
//                                         }
//                                     ]
//                                 }
