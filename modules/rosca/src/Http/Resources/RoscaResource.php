<?php

namespace Modules\Rosca\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoscaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'cycle_period' => $this->cycle_period,
            'contribution_amount' => $this->contribution_amount,
            'start_date' => $this->start_date,
            'members_count' => $this->whenLoaded('members') ? $this->members->count() : $this->members_count ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
