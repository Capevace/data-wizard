<?php

namespace Capevace\MagicImport\Utils;

use GregHunt\PartialJson\JsonParser;

class PartialJson
{
    public static function parse(?string $json): ?array
    {
        if (! $json) {
            return null;
        }

        try {
            $parser = new JsonParser;

            if ($newData = $parser->parse($json)) {
                if (is_array($newData)) {
                    return $newData;
                }
            }
        } catch (\JsonException $e) {
            // JSON is invalid, so we can't append it
        } catch (\Throwable $e) {
            report($e);
        }

        return null;
    }
}
