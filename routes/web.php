<?php

use App\Http\Middleware\AllowEmbeddingMiddleware;
use App\Models\ExtractionBucket;
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

Route::get('/setup', \App\Livewire\Components\Setup::class)
    ->name('setup');

Route::middleware(['auth'])
    ->group(function () {
        Route::get('/', function () {
            return view('welcome');
        });

        Route::get('/files/{fileId}/contents/{path}', function (string $fileId, string $path) {
            $file = \App\Models\File::findOrFail($fileId);
            $artifact = $file->artifact;

            $decodedPath = base64_decode($path);

            if (!\Illuminate\Support\Str::startsWith($decodedPath, ['images', 'pages', 'pages_marked', 'pages_txt', 'source.'])) {
                abort(404);
            }

            /** @var ?\Mateffy\Magic\Extraction\Slices\EmbedSlice $content */
            $content = collect($artifact->getContents())
                ->filter(fn (\Mateffy\Magic\Extraction\Slices\Slice $content) => $content instanceof \Mateffy\Magic\Extraction\Slices\EmbedSlice)
                ->first(fn (\Mateffy\Magic\Extraction\Slices\EmbedSlice $content) => $content->getPath() === $decodedPath);

            if ($content === null) {
                abort(404);
            }

            $contents = $artifact->getEmbedContents($content);

            return response($contents, 200, [
                'Content-Type' => $content->getMimeType(),
            ]);
        })
            ->middleware('signed')
            ->name('files.contents');

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

//        Route::get('/test-chat', \App\Livewire\Components\TestChat::class);



    });

Route::get('/embed/{extractorId}', \App\Livewire\Components\EmbeddedExtractor::class)
    ->middleware([AllowEmbeddingMiddleware::class])
    ->name('embedded-extractor');

Route::get('/full-page/{extractorId}', \App\Livewire\Components\EmbeddedExtractor::class)
    ->name('full-page-extractor');

Route::get('/bucket/{bucketId}/files/{name}/image/{number}', function (string $bucketId, string $name, int $number) {
    $bucket = ExtractionBucket::findOrFail($bucketId);

    if ($name === 'first') {
        $file = $bucket->files()->firstOrFail();
    } else {
        $name = base64_decode($name);
        $file = $bucket->files()->where('name', $name)->firstOrFail();
    }
    /** @var \App\Models\File $file */

    if ($file->artifact === null) {
        abort(404);
    }

    $imagePath = $file->artifact->getEmbedPath("images/image{$number}.jpg");

    return response()->file($imagePath);
})
    ->name('bucket.image');


Route::get('/api/v1/extractions', [\App\Http\Controllers\Extraction\ExtractionController::class, 'index']);
Route::post('/api/v1/extractions', [\App\Http\Controllers\Extraction\ExtractionController::class, 'store']);
Route::get('/api/v1/extractions/{id}', [\App\Http\Controllers\Extraction\ExtractionController::class, 'show']);
//Route::put('/api/v1/extractions/{id}', [\App\Http\Controllers\Extraction\ExtractionController::class, 'update']);
//Route::delete('/api/v1/extractions/{id}', [\App\Http\Controllers\Extraction\ExtractionController::class, 'destroy']);
