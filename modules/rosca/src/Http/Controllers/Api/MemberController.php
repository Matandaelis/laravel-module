<?php

namespace Modules\Rosca\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Rosca\Models\Member;

class MemberController extends Controller
{
    public function index()
    {
        return response()->json(Member::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rosca_id' => 'required|exists:roscas,id',
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|integer',
            'contact' => 'nullable|string|max:255',
        ]);

        $member = Member::create($data);

        return response()->json($member, 201);
    }

    public function show(Member $roscaMember)
    {
        return response()->json($roscaMember);
    }

    public function update(Request $request, Member $roscaMember)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'contact' => 'nullable|string|max:255',
        ]);

        $roscaMember->update($data);

        return response()->json($roscaMember);
    }

    public function destroy(Member $roscaMember)
    {
        $roscaMember->delete();

        return response()->json(null, 204);
    }
}
