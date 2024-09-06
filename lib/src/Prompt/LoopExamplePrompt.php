<?php

namespace Capevace\MagicImport\Prompt;

use Capevace\MagicImport\Functions\Add;
use Capevace\MagicImport\Functions\Finish;
use Capevace\MagicImport\Functions\InvokableFunction;
use Capevace\MagicImport\Functions\OutputText;
use Capevace\MagicImport\Prompt\Message\Message;
use Capevace\MagicImport\Prompt\Message\TextMessage;
use JsonException;

class LoopExamplePrompt implements Prompt
{
    public function __construct(
        /**
         * LLMs like Mistral don't support functions out of the box.
         */
        protected bool $manuallyIncludeFunctions = true
    ) {}

    /**
     * @throws JsonException
     */
    public function system(): string
    {
        $functions = json_encode(
            array_map(fn (InvokableFunction $function) => $function->schema(), $this->functions()),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );

        $schema = json_encode(
            $this->schema(),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );

        return <<<PROMPT
        You are a calculator that can add numbers.
        You can use the following functions:
        {$functions}
        
        You can output two things:
        - Output text.
        - Call functions. 
        
        Output a single line of JSON that matches the following JSON schema:
        {$schema}
        
        If you want to call functions, ONLY output the json object. No additional text or markdown formatting.
        PROMPT;
    }

    /**
     * @return Message[]
     *
     * @throws JsonException
     */
    public function messages(): array
    {
        return [
            new TextMessage(role: Role::System, content: $this->system()),
        ];
    }

    public function schema(): array
    {
        return [
            'type' => 'array',
            'items' => [
                'anyOf' => [
                    [
                        'type' => 'object',
                        'description' => 'Output text.',
                        'properties' => [
                            'text' => [
                                'type' => 'string',
                            ],
                        ],
                    ],
                    [
                        'type' => 'object',
                        'description' => 'Call one of the available functions.',
                        'properties' => [
                            'function_call' => [
                                'type' => 'string',
                            ],
                            'arguments' => [
                                'type' => 'object',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function functions(): array
    {
        return [
            new Add,
            new OutputText,
            //            new Finish
        ];
    }
}
