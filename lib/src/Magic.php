<?php

namespace Mateffy\Magic;

use Mateffy\Magic\Builder\ChatPreconfiguredModelBuilder;
use Mateffy\Magic\Builder\ExtractionLLMBuilder;
use Mateffy\Magic\Functions\MagicReturnFunction;
use Mateffy\Magic\LLM\Exceptions\UnableToActAsFunction;
use Mateffy\Magic\LLM\Exceptions\UnknownInferenceException;
use Mateffy\Magic\LLM\LLM;
use Mateffy\Magic\LLM\Message\TextMessage;
use Mateffy\Magic\LLM\Models\Claude3Family;
use Mateffy\Magic\Loop\EndConversation;
use Mateffy\Magic\Memory\MagicMemory;
use ReflectionException;

class Magic
{
    public static function extract(): ExtractionLLMBuilder
    {
        return new ExtractionLLMBuilder;
    }

    public static function chat(): ChatPreconfiguredModelBuilder
    {
        return new ChatPreconfiguredModelBuilder;
    }

    public static function end(mixed $output): EndConversation
    {
        return new EndConversation($output);
    }

    /**
     * @param string $description
     * @param 'string'|'number'|'boolean'|'array'|'object' $type
     * @param array|null $schema
     * @param LLM|string|null $llm
     * @return mixed
     * @throws UnableToActAsFunction
     * @throws UnknownInferenceException
     * @throws ReflectionException
     */
    public static function function(string $description, string $type, ?array $schema = null, LLM|string|null $llm = null): mixed
    {
        $messages = Magic::chat()
            ->model($llm ?? self::getDefaultModel())
            ->system(<<<PROMPT
            You are a function that returns a value of type {$type}.

            For example, you may be asked to return the year of release of a song as a number.
            You can return this data by calling the `returnValue` function.

            If the task you are given is not possible for you to do or answer, you can call the `fail` function with an error message.
            PROMPT)
            ->tools([
                'returnValue' => new MagicReturnFunction($type, $schema),
                'fail' => fn (string $error) => Magic::end($error),
            ])
            ->messages([
                TextMessage::user($description)
            ])
            ->stream();

        $output = $messages
            ->firstFunctionOutput();

        if ($output->call->name === 'returnValue') {
            return $output->output;
        } elseif ($output->call->name === 'fail') {
            throw new UnableToActAsFunction($output->output);
        } else {
            throw new UnknownInferenceException('LLM did not return a valid response: '.json_encode($output));
        }
    }

    public static function ask(string $question, LLM|string|null $llm = null): string
    {
        $messages = Magic::chat()
            ->model($llm ?? self::getDefaultModel())
            ->system("You are an expert question answerer. You are concise and try to keep it short, but you fill it with high quality information in response to the question. You don't need to do all the chatbot assistant messaging (saying sorry, giving alternatives, etc.). If you can't answer a question, just state so concisely, and do not give other information or ask a question that wasn't asked. If you can, answer as compactly as possible. Avoid using multiple lines of text unless necessary.")
            ->messages([
                TextMessage::user($question)
            ])
            ->stream();

        return $messages->firstText();
    }

    public static function memory(): MagicMemory
    {
        return new MagicMemory;
    }

    public static function getDefaultModel(): LLM
    {
        return Claude3Family::haiku();
    }
}
