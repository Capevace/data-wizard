<?php

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Message\FunctionCall;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Message\TextMessage;
use Mateffy\Magic\LLM\Models\Claude3Family;
use Mateffy\Magic\Magic;
use Mateffy\Magic\Prompt\TokenStats;

test('the application returns a successful response', function () {
    $messages = Magic::chat()
        ->model(Claude3Family::haiku())
        ->system('You are a chatbot. When outputting the final calculation result, output it as <result>x</result>.')
        ->tools([
            'add' => fn(int $a, int $b) => $a + $b,
            'multiply' => fn(int $a, int $b) => $a * $b,
            'subtract' => fn(int $a, int $b) => $a - $b,
            'divide' => fn(int $a, int $b) => $a / $b,
        ])
        ->messages([
            TextMessage::user('Please calculate (2 * 3) - (4 / 2)'),
        ])
        ->stream(onMessage: fn (Message $message) => dump($message->text()));

    $text = $messages->firstText();

    expect($text)->toContain('<result>4</result>');
});

test('test if ending early works', function () {
    $messages = Magic::chat()
        ->model(Claude3Family::haiku())
        ->system('You are a chatbot.')
        ->tools([
            'sayLouder' => fn(string $text) => Magic::end('WORKS'),
        ])
        ->messages([
            TextMessage::user('Hello, how are you?'),
            TextMessage::assistant('I am fine, thank you.'),
            TextMessage::user('Say it louder!'),
        ])
        ->stream();

    $output = $messages->firstFunctionOutput();
    $text = $messages->firstText();

    expect($output->text())->toEqual('WORKS');
    expect($text)->toBeNull();
});

test('test if it can handle class functions', function () {
    $classFunction = new class implements InvokableFunction {
        public function name(): string
        {
            return 'add';
        }

        public function schema(): array
        {
            return [
                'type' => 'object',
                'properties' => [
                    'a' => [
                        'type' => 'number',
                        'description' => 'The first number to add',
                    ],
                    'b' => [
                        'type' => 'number',
                        'description' => 'The second number to add',
                    ],
                ],
                'required' => ['a', 'b'],
            ];
        }

        public function validate(array $arguments): array
        {
            return $arguments;
        }

        public function execute(FunctionCall $call): mixed
        {
            return $call->arguments['a'] + $call->arguments['b'];
        }
    };

    $messages = Magic::chat()
        ->model(Claude3Family::haiku())
        ->system('You are a chatbot. When outputting the final calculation result, output it as <result>x</result>.')
        ->tools([
            'add' => $classFunction,
        ])
        ->messages([
            TextMessage::user('Please calculate 2 + 2'),
        ])
        ->stream();

    $output = $messages->firstFunctionOutput();
    $text = $messages->firstText();

    expect($output->output)->toEqual(4);
    expect($text)->toContain('<result>4</result>');
});

test('test if docblocks are used as descriptions', function () {
    $messages = Magic::chat()
        ->model(Claude3Family::haiku())
        ->system('You are a chatbot.')
        ->tools([
            /**
             * @description The secret is 'magic'
             */
            'reportSecret' => fn(string $secret) => $secret,
        ])
        ->messages([
            TextMessage::user('Please report the secret'),
        ])
        ->stream();

    $output = $messages->firstFunctionOutput();
    $text = $messages->firstText();

    expect($output->text())->toEqual('magic');
    expect($text)->not->toBeNull();
});

test('it reports token counts', function () {
    $tokenStats = null;

    Magic::chat()
        ->model(Claude3Family::haiku())
        ->system('You are a chatbot. You job is to output "yes." exactly, and nothing else.')
        ->messages([
            TextMessage::user('Please output "yes."'),
        ])
        ->onTokenStats(function (TokenStats $stats) use (&$tokenStats) {
            $tokenStats = $stats;
        })
        ->stream();

    expect($tokenStats)->not->toBeNull();
    expect($tokenStats->tokens)->toEqual(37);
    expect($tokenStats->cost?->inputCentsPer1K)->toEqual(Claude3Family::haiku()->getModelCost()->inputCentsPer1K);
    expect($tokenStats->cost?->outputCentsPer1K)->toEqual(Claude3Family::haiku()->getModelCost()->outputCentsPer1K);
});


