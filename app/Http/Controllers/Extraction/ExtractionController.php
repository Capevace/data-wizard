<?php

namespace App\Http\Controllers\Extraction;

use App\Http\Controllers\Controller;
use App\Models\ExtractionRun;
use App\Models\SavedExtractor;
use App\OpenApi\Parameters\ExtractionParameters;
use App\OpenApi\RequestBodies\ExtractionRequestBody;
use App\OpenApi\Responses\EmptySuccessResponse;
use App\OpenApi\Responses\ErrorValidationResponse;
use App\OpenApi\Responses\ExtractionResponse;
use App\OpenApi\Responses\NotFoundResponse;
use Illuminate\Http\Request;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class ExtractionController extends Controller
{
    /**
     * Get a list of all extractions.
     */
    #[OpenApi\Operation()]
    #[OpenApi\Response(factory: EmptySuccessResponse::class)]
    #[OpenApi\Parameters(factory: ExtractionParameters::class)]
    public function index()
    {
        $extractions = ExtractionRun::all();

        return ExtractionResource::collection($extractions);
    }

    /**
     * Create a new extraction.
     */
    #[OpenApi\Operation(method: 'post')]
    #[OpenApi\Parameters(factory: ExtractionParameters::class)]
    #[OpenApi\RequestBody(factory: ExtractionRequestBody::class)]
    #[OpenApi\Response(factory: ExtractionResponse::class, statusCode: 200)]
////    #[OpenApi\Response(factory: ErrorValidationResponse::class, statusCode: 422)]
    public function store(Request $request)
    {
        $data = $request->validate([
            'extractor_id' => 'required|exists:saved_extractors,id',
            'description' => 'nullable|string',
        ]);

        $extractor = SavedExtractor::findOrFail($data['extractor_id']);

        $extraction = ExtractionRun::create([
            'model' => $extractor->model ?? config('magic-extract.default-model'),
            ...$data
        ]);

        return new ExtractionResource($extraction);
    }

    /**
     * Get a specific extraction.
     */
    #[OpenApi\Operation]
    #[OpenApi\Parameters(factory: ExtractionParameters::class)]
    #[OpenApi\Response(factory: ExtractionResponse::class, statusCode: 200)]
    #[OpenApi\Response(factory: NotFoundResponse::class, statusCode: 404)]
    public function show($id)
    {
        $extraction = ExtractionRun::findOrFail($id);

        return new ExtractionResource($extraction);
    }
//
//    /**
//     * Update an extraction.
//     */
//    #[OpenApi\Operation(tags: ['Extraction'], method: 'put')]
//    #[OpenApi\Response(factory: ExtractionResponse::class, statusCode: 200)]
//    #[OpenApi\Response(factory: ErrorValidationResponse::class, statusCode: 422)]
//    #[OpenApi\Response(factory: NotFoundResponse::class, statusCode: 404)]
//    public function update(Request $request, $id)
//    {
//        $extraction = ExtractionRun::findOrFail($id);
//
//        $data = $request->validate([
//            'description' => 'nullable|string',
//        ]);
//
//        $extraction->update($data);
//
//        return new ExtractionResource($extraction);
//    }
//
//    /**
//     * Delete an extraction.
//     */
//    #[OpenApi\Operation(tags: ['Extraction'], method: 'delete')]
//    #[OpenApi\Response(factory: EmptySuccessResponse::class)]
//    #[OpenApi\Response(factory: NotFoundResponse::class, statusCode: 404)]
//    public function destroy($id)
//    {
//        $extraction = ExtractionRun::findOrFail($id);
//
//        $extraction->delete();
//
//        return response()->noContent();
//    }
}
