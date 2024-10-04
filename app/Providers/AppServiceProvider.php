<?php

namespace App\Providers;

use App\Models\ExtractionBucket;
use App\Models\ExtractionRun;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \URL::forceScheme('https');

        // Allow admin access to Laravel Pulse Dashboard
        if ($email = config('app.admin_email')) {
            Gate::define('viewPulse', fn (User $user) => $user->email === $email);
        }

        Route::model('bucket', ExtractionBucket::class);
        Route::model('run', ExtractionRun::class);

        Livewire::component('embedded-extractor', \App\Livewire\Components\EmbeddedExtractor::class);
        Livewire::component('streamable-message', \App\Livewire\Components\StreamableMessage::class);
    }
}
