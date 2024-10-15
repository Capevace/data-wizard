<?php

namespace Mateffy\Magic\Config;

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
