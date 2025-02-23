<?php

namespace App;

use App\Jobs\GenerateArtifactJob;
use App\Models\ExtractionBucket;
use App\Models\File;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class AutoExtractArtifactsListener
{
    public function __construct() {}

    public function handle(MediaHasBeenAddedEvent $event): void
    {
        /** @var ExtractionBucket|CloudArtifact $model */
        $model = $event->media->model()->first();

        /** @var File $file */
        $file = $event->media;

        match ($model::class) {
            ExtractionBucket::class => GenerateArtifactJob::dispatch(bucket: $model, file: $file),
//            CloudArtifact::class => GenerateCloudArtifactJob::dispatch(cloudArtifact: $model),
        };
    }
}
