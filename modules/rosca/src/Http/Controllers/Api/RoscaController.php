<?php

namespace Modules\Rosca\Http\Controllers\Api;

use Modules\Rosca\Http\Requests\RoscaRequest;
use Modules\Rosca\Http\Resources\RoscaResource;
use Illuminate\Routing\Controller;
use Modules\Rosca\Models\Rosca;

class RoscaController extends Controller
{
    public function index()
    {
        return RoscaResource::collection(Rosca::withCount('members')->get());
    }

    public function store(RoscaRequest $request)
    {
        $rosca = Rosca::create($request->validated());

        return new RoscaResource($rosca);
    }

    public function show(Rosca $rosca)
    {
        $rosca->load(['members','rounds']);

        return new RoscaResource($rosca);
    }

    public function update(RoscaRequest $request, Rosca $rosca)
    {
        $rosca->update($request->validated());

        return new RoscaResource($rosca);
    }

    public function destroy(Rosca $rosca)
    {
        $rosca->delete();

        return response()->json(null, 204);
    }
}
