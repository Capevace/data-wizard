<?php

namespace App\Http\Controllers;

use App\Models\ExtractionRun;
use Mateffy\Magic\Extraction\Artifacts\DiskArtifact;
use Mateffy\Magic\Extraction\Slices\EmbedSlice;
use Mateffy\Magic\Extraction\Slices\Slice;

/**
 * A route that takes LLM generated Artifact IDs (artifact:<ID>/images/image1.jpg)
 * and returns the raw image data to show in the browser or fetch from the outside.
 */
class LlmArtifactEmbedController extends Controller
{
	public function __invoke(string $runId)
	{
//        if (!request()->hasValidSignatureWhileIgnoring(['artifactId'])) {
//            abort(404);
//        }

        $artifactId = request()->query('artifactId');

        // Make sure the run exists
        ExtractionRun::findOrFail($runId);

        $path = str($artifactId)->after('/');
        $pathWithoutExtension = str($path->toString())
            ->beforeLast('.')
            // We don't want to return the marked versions here, as the images will be user facing.
            // As the LLM may sometimes still return the marked versions, we make sure to replace them with the original.
            ->replace('images_marked', 'images')
            ->toString();

        $artifact = DiskArtifact::tryFromArtifactId($artifactId);

        /** @var EmbedSlice $slice */
        $slice = $artifact->getContents()
            ->first(fn (Slice $content) => $content instanceof EmbedSlice && str($content->getPath())->startsWith($pathWithoutExtension));

        if ($slice === null) {
            abort(404);
        }

        $contents = $artifact->getRawEmbedContents($slice);

        return response($contents, 200, [
            'Content-Type' => $slice->getMimeType()
        ]);
	}
}
