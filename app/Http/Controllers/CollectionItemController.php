<?php

namespace App\Http\Controllers;

use App\Http\Resources\CollectionItemResource;
use App\Models\SmartCollectionItem;
use Illuminate\Http\Request;

class CollectionItemController extends Controller
{
    public function index()
    {
        return CollectionItemResource::collection(SmartCollectionItem::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required'],
            'data' => ['required'],
        ]);

        return new CollectionItemResource(SmartCollectionItem::create($data));
    }

    public function show(SmartCollectionItem $collectionItem)
    {
        return new CollectionItemResource($collectionItem);
    }

    public function update(Request $request, SmartCollectionItem $collectionItem)
    {
        $data = $request->validate([
            'title' => ['required'],
            'data' => ['required'],
        ]);

        $collectionItem->update($data);

        return new CollectionItemResource($collectionItem);
    }

    public function destroy(SmartCollectionItem $collectionItem)
    {
        $collectionItem->delete();

        return response()->json();
    }
}
