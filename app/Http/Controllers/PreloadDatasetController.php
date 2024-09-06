<?php

namespace App\Http\Controllers;

class PreloadDatasetController extends Controller
{
    public function __invoke(string $dataset)
    {
        $path = base_path('../magic-import/fixtures/'.$dataset.'/expose.json');

        if (! file_exists($path)) {
            abort(404, 'Dataset not found');
        }

        $json = file_get_contents($path);

        return response()->json(json_decode($json, flags: JSON_THROW_ON_ERROR));
    }
}
