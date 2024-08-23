<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExtractionRunResource;
use App\Models\ExtractionRun;
use Illuminate\Http\Request;

class ExtractionRunController extends Controller
{
    public function index()
    {
        return ExtractionRunResource::collection(ExtractionRun::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'status' => ['required'],
            'started_by_id' => ['nullable', 'exists:users'],
            'result_json' => ['nullable'],
            'partial_result_json' => ['nullable'],
        ]);

        return new ExtractionRunResource(ExtractionRun::create($data));
    }

    public function show(ExtractionRun $extractionRun)
    {
        return new ExtractionRunResource($extractionRun);
    }

    public function update(Request $request, ExtractionRun $extractionRun)
    {
        $data = $request->validate([
            'status' => ['required'],
            'started_by_id' => ['nullable', 'exists:users'],
            'result_json' => ['nullable'],
            'partial_result_json' => ['nullable'],
        ]);

        $extractionRun->update($data);

        return new ExtractionRunResource($extractionRun);
    }

    public function destroy(ExtractionRun $extractionRun)
    {
        $extractionRun->delete();

        return response()->json();
    }
}
