<?php

namespace Capevace\MagicImport\Functions;

use Closure;

interface InvokableFunction
{
    public function name(): string;

    public function schema(): array;

    public function validate(array $data): array;

    public function callback(): Closure;

    public function execute(array $data): mixed;
}
