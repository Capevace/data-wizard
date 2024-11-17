<?php

namespace App\Http\Controllers;

use App\Http\Resources\ListResource;
use App\Models\Collection;
use Illuminate\Http\Request;

class ListController extends Controller
{
    public function index()
    {
        return ListResource::collection(Collection::all());
        }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => ['required'],
            'icon' => ['nullable'],
            'color' => ['nullable'],
        ]);

        return new ListResource(Collection::create($data));
        }

    public function show(list $list)
    {
        return new ListResource($list);
    }

    public function update(Request $request, list $list)
    {
        $data = $request->validate([
            'label' => ['required'],
            'icon' => ['nullable'],
            'color' => ['nullable'],
        ]);

        $list->update($data);

        return new ListResource($list);
    }

    public function destroy(list $list)
    {
        $list->delete();

        return response()->json();
    }
}
