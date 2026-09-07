<?php

namespace Modules\Rosca\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContributionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'rosca_id' => $this->rosca_id,
            'member_id' => $this->member_id,
            'amount' => $this->amount,
            'contributed_at' => $this->contributed_at,
            'created_at' => $this->created_at,
        ];
    }
}
