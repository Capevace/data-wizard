<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\ExtractionRun */
class ExtractionRunResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'id' => $this->id,
            'status' => $this->status,
            'result_json' => $this->result_json,
            'partial_result_json' => $this->partial_result_json,

            'started_by_id' => $this->started_by_id,
        ];
    }
}
