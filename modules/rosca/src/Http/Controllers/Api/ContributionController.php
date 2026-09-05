<?php

namespace Modules\Rosca\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Rosca\Models\Contribution;

class ContributionController extends Controller
{
    public function index()
    {
        return response()->json(Contribution::with(['member','rosca'])->latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rosca_id' => 'required|exists:roscas,id',
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'contributed_at' => 'nullable|date'
        ]);

        $contribution = Contribution::create($data);

        return response()->json($contribution, 201);
    }

    public function show(Contribution $contribution)
    {
        return response()->json($contribution->load(['member','rosca']));
    }
}
