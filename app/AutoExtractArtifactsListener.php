<?php

namespace App;

use App\Models\ExtractionBucket;
use App\Models\File;
use Capevace\MagicImport\Artifacts\GenerateArtifactJob;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class AutoExtractArtifactsListener
{
    public function __construct() {}

    public function handle(MediaHasBeenAddedEvent $event): void
    {
        /** @var ExtractionBucket $bucket */
        $bucket = $event->media->model()->first();

        /** @var File $file */
        $file = $event->media;

        GenerateArtifactJob::dispatch(bucket: $bucket, file: $file);
    }
}
