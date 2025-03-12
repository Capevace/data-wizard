<?php

namespace App\Listeners;

use App\Jobs\GenerateArtifactJob;
use App\Models\ExtractionBucket;
use App\Models\File;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class AutoExtractArtifactsListener
{
    public function __construct() {}

    public function handle(MediaHasBeenAddedEvent $event): void
    {
        /** @var ExtractionBucket $model */
        $model = $event->media->model()->first();

        /** @var File $file */
        $file = $event->media;

        GenerateArtifactJob::dispatch(bucket: $model, file: $file);
    }
}
