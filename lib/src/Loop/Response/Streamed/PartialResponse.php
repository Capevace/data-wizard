<?php

namespace Capevace\MagicImport\Loop\Response\Streamed;

use Capevace\MagicImport\Loop\Response\LLMResponse;
use Capevace\MagicImport\Prompt\Role;

interface PartialResponse
{
    public function role(): Role;

    public function append(string $content): static;

    public function toResponse(): LLMResponse;

    public function copy(): static;
}
