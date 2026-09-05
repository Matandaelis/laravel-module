<?php

namespace Modules\Rosca\Http\Controllers\Api;

use Modules\Rosca\Http\Requests\ContributionRequest;
use Modules\Rosca\Http\Resources\ContributionResource;
use Illuminate\Routing\Controller;
use Modules\Rosca\Models\Contribution;

class ContributionController extends Controller
{
    public function index()
    {
        return ContributionResource::collection(Contribution::with(['member','rosca'])->latest()->get());
    }

    public function store(ContributionRequest $request)
    {
        $contribution = Contribution::create($request->validated());

        return new ContributionResource($contribution);
    }

    public function show(Contribution $contribution)
    {
        return new ContributionResource($contribution->load(['member','rosca']));
    }
}
