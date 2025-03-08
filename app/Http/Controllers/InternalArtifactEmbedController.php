<?php

namespace App\Http\Controllers;

use Mateffy\Magic\Extraction\Slices\EmbedSlice;
use Mateffy\Magic\Extraction\Slices\Slice;

/**
 * A controller used in the internal/backend app to serve artifact embed contents.
 * This requires exact paths so is not well suited for the LLM generated artifact IDs.
 */
class InternalArtifactEmbedController extends Controller
{
	public function __invoke(string $fileId, string $path)
	{
		$file = \App\Models\File::findOrFail($fileId);
        $artifact = $file->artifact;

        $decodedPath = base64_decode($path);

        if (!\Illuminate\Support\Str::startsWith($decodedPath, ['images', 'pages', 'pages_marked', 'pages_txt', 'source.'])) {
            abort(404);
        }

        /** @var ?EmbedSlice $content */
        $content = collect($artifact->getContents())
            ->filter(fn (Slice $content) => $content instanceof EmbedSlice)
            ->first(fn (EmbedSlice $content) => $content->getPath() === $decodedPath);

        if ($content === null) {
            abort(404);
        }

        $contents = $artifact->getRawEmbedContents($content);

        return response($contents, 200, [
            'Content-Type' => $content->getMimeType(),
        ]);
	}
}
