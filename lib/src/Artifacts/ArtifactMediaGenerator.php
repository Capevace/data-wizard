<?php

namespace Capevace\MagicImport\Artifacts;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\Conversions\ImageGenerators\ImageGenerator;

//class ArtifactMediaGenerator extends ImageGenerator
//{
//    /**
//    * This function should return a path to an image representation of the given file.
//    */
//    public function convert(string $file, Conversion $conversion = null) : string
//    {
//        Log::info("Generating media for file $file");
//        // Run python script
//        $bin = __DIR__ . '/../../python/venv/bin/python';
//        $script = __DIR__ . '/../../python/prepare-pdf.py';
//
//        $dir = str($file)
//            ->beforeLast('/')
//            ->append('/artifact');
//
//        $output = shell_exec("$bin $script $dir $file");
//
//        dd($output);
//
//        return $output;
//    }
//
//    public function requirementsAreInstalled() : bool
//    {
//        return true;
//    }
//
//    public function supportedExtensions() : Collection
//    {
//        return collect(['pdf']);
//    }
//
//    public function supportedMimeTypes() : Collection
//    {
//        return collect([
//            'application/pdf',
//        ]);
//    }
//}
