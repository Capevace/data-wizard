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

Route::middleware(['auth'])
    ->group(function () {
        // Render the "marketing" page. Since the "product" has not launched and it's just a part of the BA thesis,
        // this requires authentication for now.
        Route::get('/', function () {
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
Route::get('/embed/{extractorId}', EmbeddedExtractor::class)
    ->middleware([AllowEmbeddingMiddleware::class])
    ->name('embedded-extractor');

// The full page extractor route is used to show the extractor in a full page.
Route::get('/full-page/{extractorId}', EmbeddedExtractor::class)
    ->name('full-page-extractor');


Route::get('test', function () {
    return 'test';
})->middleware('auth:sanctum');
