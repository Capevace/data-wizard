<?php

use App\Models\SavedExtractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::any('/test/webhook/{extractorId}', function (string $extractorId) {
    $secret = SavedExtractor::find($extractorId)?->webhook_secret;

    $signature = request()->header('Signature');
    $payload = json_encode(request()->all());
    $calculated_signature = hash_hmac('sha256', $payload, $secret);

    Log::info('Webhook received 2', [
        'extractorId' => $extractorId,
        'payload' => $payload,
        'signature' => $signature,
        'calculated_signature' => $calculated_signature,
    ]);

    if ($signature !== $calculated_signature) {
        abort(403);
    }

    Log::info('Webhook received!', ['payload' => $payload]);

    return response()->json([
        'status' => 'ok',
        'payload' => $payload,
    ]);
})
    ->name('test-webhook');
