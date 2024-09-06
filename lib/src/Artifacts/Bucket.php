<?php

namespace Capevace\MagicImport\Artifacts;

class Bucket
{
    public function __construct(
        /** @var Artifact[] $artifacts */
        protected array $artifacts
    ) {}
}
