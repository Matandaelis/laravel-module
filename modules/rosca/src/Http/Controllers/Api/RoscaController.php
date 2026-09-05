<?php

namespace Modules\Rosca\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Rosca\Models\Rosca;

class RoscaController extends Controller
{
    public function index()
    {
        return response()->json(Rosca::withCount('members')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cycle_period' => 'required|string|in:weekly,monthly,quarterly,yearly',
            'contribution_amount' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
        ]);

        $rosca = Rosca::create($data);

        return response()->json($rosca, 201);
    }

    public function show(Rosca $rosca)
    {
        return response()->json($rosca->load(['members','rounds']));
    }

    public function update(Request $request, Rosca $rosca)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'cycle_period' => 'sometimes|string|in:weekly,monthly,quarterly,yearly',
            'contribution_amount' => 'sometimes|numeric|min:0',
        ]);

        $rosca->update($data);

        return response()->json($rosca);
    }

    public function destroy(Rosca $rosca)
    {
        $rosca->delete();

        return response()->json(null, 204);
    }
}
