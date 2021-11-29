<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreSynonym;
use App\Http\Resources\SynonymResource;
use App\Synonym;
use Illuminate\Http\Request;

class SynonymsController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = Synonym::with(
            [
                'taxon',
            ]
        )->filter($request)->orderBy('id')->paginate($request->input('per_page', 15));

        return SynonymResource::collection($query);
    }

    public function store(StoreSynonym $request)
    {
        return new SynonymResource($request->store());
    }

    public function destroy(int $id)
    {
        $synonym = Synonym::where('id', $id);
        $synonym->delete();

        return response()->json(null, 204);
    }

    public function update(Synonym $synonym, StoreSynonym $form)
    {
        return new SynonymResource($form->save($synonym));
    }
}
