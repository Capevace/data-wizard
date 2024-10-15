<?php

namespace Mateffy\Magic\Strategies;

interface Strategy {
    public function run(array $artifacts): array;
}
