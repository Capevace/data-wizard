<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Mateffy\Magic\Extraction\ContentType;
use Mateffy\Magic\Extraction\Slices\EmbedSlice;
use Mateffy\Magic\Extraction\Slices\Slice;

/**
 * Show a thumbnail of a file.
 */
class FileThumbnailController extends Controller
{
    public function __invoke(string $fileId)
    {
        $file = File::findOrFail($fileId);
        $artifact = $file->artifact;

        $page_image = $artifact->getContents()
            ->first(fn (Slice $content) => $content instanceof EmbedSlice && $content->getType() === ContentType::PageImage);

        if ($page_image === null) {
            $image = $artifact->getContents()
                ->first(fn (Slice $content) => $content instanceof EmbedSlice && $content->getType() === ContentType::Image);
        }

        if ($page_image === null && $image === null) {
            $stream = Storage::disk($file->conversions_disk)
                ->readStream($file->getPath('thumbnail'));
        } else {
            $stream = $artifact->getRawEmbedStream($page_image ?? $image);
        }

        if (!$stream) {
            abort(404);
        }

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => 'image/jpeg',
        ]);
    }
}
