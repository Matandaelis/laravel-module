<?php

namespace Modules\Rosca\Http\Controllers\Api;

use Modules\Rosca\Http\Requests\MemberRequest;
use Modules\Rosca\Http\Resources\MemberResource;
use Illuminate\Routing\Controller;
use Modules\Rosca\Models\Member;

class MemberController extends Controller
{
    public function index()
    {
        return MemberResource::collection(Member::all());
    }

    public function store(MemberRequest $request)
    {
        $member = Member::create($request->validated());

        return new MemberResource($member);
    }

    public function show(Member $member)
    {
        return new MemberResource($member);
    }

    public function update(MemberRequest $request, Member $member)
    {
        $member->update($request->validated());

        return new MemberResource($member);
    }

    public function destroy(Member $member)
    {
        $member->delete();

        return response()->json(null, 204);
    }
}
