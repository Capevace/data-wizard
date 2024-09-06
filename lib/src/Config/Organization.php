<?php

namespace Capevace\MagicImport\Config;

class Organization
{
    public function __construct(
        public string $id,
        public string $name,
        public string $website,
        public bool $privacyUsedForModelTraining,
        public bool $privacyUsedForAbusePrevention,
    ) {}
}
