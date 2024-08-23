<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExtractionBucketResource;
use App\Models\ExtractionBucket;
use Illuminate\Http\Request;

class ExtractionBucketController extends Controller
{
    public function index()
    {
        return ExtractionBucketResource::collection(ExtractionBucket::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => ['nullable'],
            'created_by_id' => ['nullable', 'exists:users'],
            'status' => ['required'],
            'started_at' => ['nullable', 'date'],
            'extractor_id' => ['required'],
        ]);

        return new ExtractionBucketResource(ExtractionBucket::create($data));
    }

    public function show(ExtractionBucket $extractionBucket)
    {
        return new ExtractionBucketResource($extractionBucket);
    }

    public function update(Request $request, ExtractionBucket $extractionBucket)
    {
        $data = $request->validate([
            'description' => ['nullable'],
            'created_by_id' => ['nullable', 'exists:users'],
            'status' => ['required'],
            'started_at' => ['nullable', 'date'],
            'extractor_id' => ['required'],
        ]);

        $extractionBucket->update($data);

        return new ExtractionBucketResource($extractionBucket);
    }

    public function destroy(ExtractionBucket $extractionBucket)
    {
        $extractionBucket->delete();

        return response()->json();
    }
}
