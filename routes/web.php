<?php

use App\Http\Middleware\AllowEmbeddingMiddleware;
use App\Models\SavedExtractor;
use Illuminate\Support\Facades\Log;
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
Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth'])
    ->group(function () {
        Route::get('/claude', \App\Http\Controllers\ClaudeStreamController::class);
        Route::get('/api/bucket/{bucket}/runs/{run}/generate', \App\Http\Controllers\JsonStreamController::class)
            ->name('api.bucket.runs.generate');

        Route::get('/api/dataset/{dataset}', \App\Http\Controllers\PreloadDatasetController::class);

        Route::get('/api/dataset/{dataset}/images/{image}', function (string $dataset, string $image) {
            $path = base_path("../magic-import/fixtures/{$dataset}/images/{$image}");

            if (! file_exists($path)) {
                abort(404);
            }

            return response()->file($path);
        });

        Route::get('/iframe-test', function () {
            // Layout: app
            return view('livewire.iframe-test');
        })
            ->name('iframe-test');
    });

Route::get('/embed/{extractorId}', \App\Livewire\Components\EmbeddedExtractor::class)
    ->middleware([AllowEmbeddingMiddleware::class])
    ->name('embedded-extractor');
