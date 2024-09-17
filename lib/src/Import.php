<?php

namespace Capevace\MagicImport;

use Capevace\MagicImport\LLM\Message\TextMessage;
use Capevace\MagicImport\Loop\Loop;
use Capevace\MagicImport\Model\Open\GPT4;
use Capevace\MagicImport\Prompt\DataExtractorPrompt;
use Capevace\MagicImport\Prompt\Role;

class Import
{
    public function run()
    {
        //        $extractor = DataExtractor::new('')
        //            ->resource($estate)
        //            ->resource($building)
        //            ->resource($rentable)
        //            ->resources([]);
        //
        //        $extractor->addFile('expose.pdf');
        //        $extractor->addFile('report.pdf');
        //
        //        $resources = $extractor->extract();
        //
        //        // or
        //
        //        $job = $extractor->queue();
        // $job->id to track the job

        $model = new GPT4(maxTokens: 10000);
        $prompt = new DataExtractorPrompt;

        $loop = new Loop(
            model: $model,
            prompt: $prompt,

            onMessageProgress: fn (...$args) => dump('onMessageProgress', ...$args),
            onMessage: fn (...$args) => dump('onMessage', ...$args),
            onFunctionCalled: fn (...$args) => dump('onFunctionCalled', ...$args),
            onFunctionOutput: fn (...$args) => dump('onFunctionOutput', ...$args),
            onFunctionError: fn (...$args) => dump('onFunctionError', ...$args),
            onStep: fn (...$args) => dump('onStep', ...$args),
            onStream: fn (...$args) => dump('onStream', ...$args),
            onEnd: fn (...$args) => dump('onEnd', ...$args),
        );

        //        $text = $extractor->fileToText('expose.pdf');
        $text = file_get_contents(base_path('../magic-import/fixtures/elsenstrasse/expose.txt'));

        $loop->start([
            new TextMessage(
                role: Role::User,
                content: "Extrahiere Daten aus folgendem Text:\n\n{$text}"
            ),
        ]);
    }
}
