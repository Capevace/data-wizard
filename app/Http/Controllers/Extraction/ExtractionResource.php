<?php

namespace App\Http\Controllers\Extraction;

use App\Models\ExtractionRun;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/** @mixin ExtractionRun */
class ExtractionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'target_schema' => $this->target_schema,
            'result_json' => $this->result_json,
            'partial_result_json' => $this->partial_result_json,
            'error' => $this->error,
            'token_stats' => $this->token_stats,
            'strategy' => $this->strategy,
            'status' => $this->status,
            'data' => $this->data,
            'partial_data' => $this->partial_data,

            'saved_extractor_id' => $this->saved_extractor_id,
        ];
    }
}
