<?php

namespace Mateffy\Magic\Buckets;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LLMMagicBucketsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('llm-magic-buckets')
            ->hasMigrations();
    }
}
