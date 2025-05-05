<?php

use App\Http\Controllers\FileThumbnailController;
use App\Http\Controllers\InternalArtifactEmbedController;
use App\Http\Controllers\LlmArtifactEmbedController;
use App\Http\Middleware\AllowEmbeddingMiddleware;
use App\Livewire\Components\EmbeddedExtractor;
use App\Livewire\Components\Setup;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// The setup route is used to set up the application for the first time.
// When this is opened after setting up (aka. a user exists), it will redirect to the admin dashboard.
Route::get('/setup', Setup::class)
    ->name('setup');

Route::get('/', function () {
    if (!config('landing.enable')) {
        return redirect()->route('filament.app.auth.login');
    }

    return response(view('pages.landing'))
        //Add cache headers to the response so Cloudflare can cache the response
        ->header('Cache-Control', 'public, max-age=3600')
        ->header('Expires', now()->addDay()->toRfc7231String())
        ->header('Vary', 'Accept-Encoding');
})
    ->name('landing');

Route::get('/legal', function () {
    if (!config('landing.enable')) {
        return redirect()->route('filament.app.auth.login');
    }

    return view('pages.legal');
})
    ->middleware(['cache.headers'])
    ->name('landing');

Route::middleware(['auth'])
    ->group(function () {
        // Render the "marketing" page. Since the "product" has not launched and it's just a part of the BA thesis,
        // this requires authentication for now.
        Route::get('/welcome', function () {
            return view('welcome');
        });

        // A route that can return embed data for internal/backend exact paths.
        Route::get('/files/{fileId}/contents/{path}', InternalArtifactEmbedController::class)
            ->middleware('signed')
            ->name('files.contents');
    });

// A route that can return embed data for LLM generated artifact IDs. (requires a artifactId query parameter)
Route::get('/runs/{runId}/artifacts', LlmArtifactEmbedController::class)
    ->name('runs.artifacts.embed');

// A route that can return a thumbnail of a file.
Route::get('/files/{fileId}/thumbnail', FileThumbnailController::class)
    ->middleware('signed')
    ->name('files.thumbnail');

// The embedded extractor route is used to show the extractor in an iframe.
//Route::get('/embed/{extractorId}', EmbeddedExtractor::class)
//    ->middleware([AllowEmbeddingMiddleware::class])
//    ->name('embedded-extractor');
//
//// The full page extractor route is used to show the extractor in a full page.
//Route::get('/full-page/{extractorId}', EmbeddedExtractor::class)
//    ->name('full-page-extractor');

Route::get('/embed/{extractorId}', function (string $extractorId) {
    return view('pages.embed', [
        'extractorId' => $extractorId,
    ])
        ->layout('components.layouts.app');
})
    ->middleware([AllowEmbeddingMiddleware::class])
    ->name('embedded-extractor');

// The full page extractor route is used to show the extractor in a full page.
Route::get('/full-page/{extractorId}', EmbeddedExtractor::class)
    ->name('full-page-extractor');
