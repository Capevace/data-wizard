<?php

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

Route::middleware(['auth'])
    ->group(function () {
        Route::get('/', function () {
            return view('welcome');
        });

        Route::get('/stream', \App\Http\Controllers\StreamController::class);
        Route::get('/claude', \App\Http\Controllers\ClaudeStreamController::class);
        Route::get('/api/bucket/{bucket}/runs/{run}/generate', \App\Http\Controllers\JsonStreamController::class)
            ->name('api.bucket.runs.generate');

        Route::get('/api/dataset/{dataset}', \App\Http\Controllers\PreloadDatasetController::class);

        Route::get('/api/dataset/{dataset}/images/{image}', function (string $dataset, string $image) {
            $path = base_path("../magic-import/fixtures/{$dataset}/images/{$image}");

            if (!file_exists($path)) {
                abort(404);
            }

            return response()->file($path);
        });
    });
