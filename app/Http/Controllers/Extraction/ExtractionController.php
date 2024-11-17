<?php

namespace App\Http\Controllers\Extraction;

use App\Http\Controllers\Controller;
use App\Models\ExtractionRun;
use Illuminate\Http\Request;

#[OpenApi\PathItem]
class ExtractionController extends Controller
{
    #[OpenApi\Operation(tags: ['Extraction'], summary: 'List all extractions')]
    public function index()
    {
        $extractions = ExtractionRun::all();

        return ExtractionResource::collection($extractions);
    }

    #[OpenApi\Operation(tags: ['Extraction'], summary: 'Create a new extraction')]
    public function store(Request $request)
    {
    }

    #[OpenApi\Operation(tags: ['Extraction'], summary: 'Show a specific extraction')]
    public function show($id)
    {
    }

    #[OpenApi\Operation(tags: ['Extraction'], summary: 'Update a specific extraction')]
    public function update(Request $request, $id)
    {
    }

    #[OpenApi\Operation(tags: ['Extraction'], summary: 'Delete a specific extraction')]
    public function destroy($id)
    {
    }
}
