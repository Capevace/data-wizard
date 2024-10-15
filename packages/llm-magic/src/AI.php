<?php

namespace Mateffy\Magic;

use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\Loop\Loop;
use Closure;
use Illuminate\Support\Arr;
use OpenAI\Responses\StreamResponse;

class AI
{
    public function stream(StreamResponse $stream, Loop $loop) {}

    protected function evaluateAction(string|Closure $action, array $arguments = []): FunctionOutput
    {
        if (is_string($action) && (! in_array(CanBeCalledByAI::class, class_implements($action)) || ! method_exists($action, 'run'))) {
            throw new InvalidFunctionDefinition('Action must implement CanBeCalledByAI and have a run method');
        }

        if ($action instanceof Closure) {
            $contextReflection = new ReflectionMethod($action, '__invoke');
        } else {
            $contextReflection = new ReflectionMethod($action, 'run');
        }

        $argsToSend = [];

        foreach ($contextReflection->getParameters() as $parameter) {
            $parameterName = $parameter->getName();

            if (! $parameter->isOptional() && ! array_key_exists($parameterName, $arguments)) {
                // Is not optional but arg is missing
                throw new Exception("{$parameterName} is not optional but arg is missing");
            }

            $arg = Arr::get($arguments, $parameterName);

            $argsToSend[$parameterName] = $arg;
        }

        if ($action instanceof Closure) {
            return $action(...$argsToSend);
        } else {
            /**
             * @var CanBeCalledByAI $instance
             */
            $instance = app($action);

            $validator = validator($argsToSend, $instance->rules());

            if ($validator->fails()) {
                throw new InvalidAIArguments($validator->errors()->first());
            }

            return $instance->run(...$argsToSend);
        }
    }

    public static function prepareMessageForUI(Message $message, int $index): UIMessage
    {
        return match (get_class($message)) {
            TextMessage::class => new UITextMessage(
                index: $index,
                role: $message->role,
                content: Markdown::parse(
                    $message->role === Message\Role::User
                        ? $message->content = str($message->content)
                            ->stripTags()
                        : $message->content
                )->toHtml(),
            ),
            FunctionCallMessage::class => new UIFunctionCall(
                index: $index,
                role: $message->role,
                name: static::getPublicNameForFunction($message),
                arguments: []
            ),
            FunctionOutputMessage::class => new UIFunctionResult(
                index: $index,
                role: $message->role,
                entities: collect($message->outputs)
                    ->map(fn (FunctionOutput $output) => match ($output->type) {
                        FunctionOutput\Type::Estate => static::getCardForEstate($output),
                        default => null
                    })
                    ->filter()
                    ->values()
                    ->all()
            ),
            ErrorMessage::class => new UIErrorMessage(
                index: $index,
                error: $message->error
            ),
            default => (function () use ($message, $index) {
                report(new Exception('Unknown message type: '.get_class($message)));

                return new UITextMessage(
                    index: $index,
                    role: $message->role,
                    content: 'Es ist ein Fehler aufgetreten. Bitte versuche es erneut.'
                );
            })(),
        };
    }

    public static function getCardForEstate(FunctionOutput\EstateOutput $output): ?UIFunctionResult\EntityCard
    {
        $estate = Estate::find($output->estate['id']);

        if (! $estate) {
            report(new Exception("Estate not found for UIFunctionResult\EntityCard: ".$output->estate['id']));

            return null;
        }

        return new UIFunctionResult\EstateCard(
            id: $estate->id,
            type: 'Objekt',
            name: $estate->name,
            url: EstateSettingsPage::getUrl(['record' => $estate->id]),
            icon: 'bi-building',
            thumbnail: $estate->thumbnail_src !== null
                ? new Image($estate->thumbnail_src)
                : null,
        );
    }

    public static function getPublicNameForFunction(FunctionCallMessage $message)
    {
        return match ($message->name) {
            'findEstateByName' => 'Suche Objekt nach Namen: '.($message->arguments['name'] ?? null)
        };
    }
}
