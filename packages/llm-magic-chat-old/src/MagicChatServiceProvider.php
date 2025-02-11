<?php

namespace Mateffy\Magic\Chat;

use Livewire\Livewire;
use Mateffy\Magic\Chat\Livewire\FakeLivewire;
use Mateffy\Magic\Chat\Livewire\StreamableMessage;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MagicChatServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('llm-magic-chat')
            ->hasRoutes('web')
            ->hasViews('llm-magic');
    }

    public function register()
    {
        parent::register();

        Livewire::component('llm-magic.streamable-message', StreamableMessage::class);
        Livewire::component('llm-magic.fake-livewire', FakeLivewire::class);
    }
}
