<?php

namespace Mateffy\Magic\Strategies;

use Mateffy\Magic\Loop\InferenceResult;

interface Strategy {
    public function run(array $artifacts): InferenceResult;
}
