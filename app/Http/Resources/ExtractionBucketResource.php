<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\ExtractionBucket */
class ExtractionBucketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'id' => $this->id,
            'description' => $this->description,
            'status' => $this->status,
            'started_at' => $this->started_at,
            'extractor_id' => $this->extractor_id,

            'created_by' => $this->created_by,
        ];
    }
}
