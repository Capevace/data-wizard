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

        Route::get('/runs/{runId}/artifacts', function (string $runId) {
            if (!request()->hasValidSignatureWhileIgnoring(['artifactId'])) {
                abort(404);
            }

            $artifactId = request()->query('artifactId');

            $run = \App\Models\ExtractionRun::findOrFail($runId);

            $path = str($artifactId)->after('/');
            $check1 = $check2 = $path->toString();

            // sometimes, AIs will just rename .jpeg to .jpg as if stuff just doesn't matter anymore.
            // rather than throw an error, we just allow both and check both... it's a bit of a mess.
            if ($path->endsWith(['.jpeg', '.jpg'])) {
                $check1 = $path->beforeLast('.')->append('.jpeg')->toString();
                $check2 = $path->beforeLast('.')->append('.jpg')->toString();
            }

            $artifact = \Mateffy\Magic\Extraction\DiskArtifact::tryFromArtifactId($artifactId);

            /** @var \Mateffy\Magic\Extraction\Slices\EmbedSlice $slice */
            $slice = $artifact->getContents()
                ->first(fn (\Mateffy\Magic\Extraction\Slices\Slice $content) => $content instanceof \Mateffy\Magic\Extraction\Slices\EmbedSlice && ($content->getPath() === $check1 || $content->getPath() === $check2));

            if ($slice === null) {
                abort(404);
            }

            $contents = $artifact->getRawEmbedContents($slice);

            return response($contents, 200, [
                'Content-Type' => $slice->getMimeType()
            ]);
        })
            ->where('artifactId', '.*')
            ->name('runs.artifacts.embed');

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

            $contents = $artifact->getRawEmbedContents($content);

            return response($contents, 200, [
                'Content-Type' => $content->getMimeType(),
            ]);
        })
            ->middleware('signed')
            ->name('files.contents');
    });

Route::get('/embed/{extractorId}', \App\Livewire\Components\EmbeddedExtractor::class)
    ->middleware([AllowEmbeddingMiddleware::class])
    ->name('embedded-extractor');

Route::get('/full-page/{extractorId}', \App\Livewire\Components\EmbeddedExtractor::class)
    ->name('full-page-extractor');


Route::get('/api/v1/extractions', [\App\Http\Controllers\Extraction\ExtractionController::class, 'index']);
Route::post('/api/v1/extractions', [\App\Http\Controllers\Extraction\ExtractionController::class, 'store']);
Route::get('/api/v1/extractions/{id}', [\App\Http\Controllers\Extraction\ExtractionController::class, 'show']);
//Route::put('/api/v1/extractions/{id}', [\App\Http\Controllers\Extraction\ExtractionController::class, 'update']);
//Route::delete('/api/v1/extractions/{id}', [\App\Http\Controllers\Extraction\ExtractionController::class, 'destroy']);
