<?php

namespace Capevace\MagicImport\Loop\Response\Streamed;

use Capevace\MagicImport\Loop\Response\JsonResponse;
use Capevace\MagicImport\Loop\Response\LLMResponse;
use Capevace\MagicImport\Prompt\Role;
use \GregHunt\PartialJson\JsonParser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StringParser
{
    public function partialParse(string $s): ?array
    {
        $tail = [];
        $inString = false;
        $stringStart = -1;
        $s = str_replace("\r\n", '', $s);

        for ($i = 0; $i < strlen($s); $i++) {
            if ($s[$i] === '"' && ($i === 0 || $s[$i - 1] !== '\\')) {
                $inString = !$inString;
                $stringStart = $inString ? $i : -1;
            } elseif (!$inString && $s[$i] === '{') {
                array_push($tail, '}');
            } elseif (!$inString && $s[$i] === '[') {
                array_push($tail, ']');
            } elseif (!$inString && $s[$i] === '}') {
                unset($tail[array_search('}', $tail, true)]);
                $tail = array_values($tail);
            } elseif (!$inString && $s[$i] === ']') {
                unset($tail[array_search(']', $tail, true)]);
                $tail = array_values($tail);
            }
        }

        if ($inString) {
            $s .= '"';
        }

        $lastCharacter = $this->getNonWhitespaceCharacterOfStringAt($s, strlen($s) - 1);
        if ($lastCharacter['character'] === ',') {
            $s = substr($s, 0, $lastCharacter['index']);
        }

        $tail = array_reverse($tail);
        return json_decode($s . implode('', $tail), true);
    }

    private function getNonWhitespaceCharacterOfStringAt(string $s, int $i): array
    {
        while ($i >= 0 && preg_match('/\s/', $s[$i]) !== 0) {
            $i--;
        }

        if ($i < 0) {
            // If $i is negative, it means no non-whitespace character was found.
            // You can return null or an empty array depending on how you want to handle this case.
            return [
                'character' => '',
                'index' => -1,
            ];
        }

        return [
            'character' => $s[$i],
            'index' => $i,
        ];
    }
}

class PartialJsonResponse implements PartialResponse
{
    protected JsonParser $parser;

    protected array $exceptions = [];

    public function __construct(
        public readonly Role $role,
        public string $content,
        public array $data = [],
    )
    {
        $this->parser = new JsonParser();
    }

    public function append(string $content, string $prefix = '{'): static
    {
        $this->content .= $content;

        $potentialJsonWithStart = '{' . Str::after($this->content, $prefix);
//        $potentialJsonWithEnd = Str::beforeLast('{' . $potentialJsonWithStart, '}') . '}';

        try {
            if ($newData = $this->parser->parse($potentialJsonWithStart)) {
                if (is_array($newData)) {
                    $this->data = $newData;
                }
            }
        } catch (\JsonException $e) {
        } catch (\Exception $e) {
            dump($e);
            report($e);
        }

        return $this;
    }

    public function toResponse(): LLMResponse
    {
        return new JsonResponse($this->data);
    }

    public function role(): Role
    {
        return $this->role;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function copy(): static
    {
        return new static($this->role, $this->content);
    }
}