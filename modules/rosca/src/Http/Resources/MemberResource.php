<?php

namespace Modules\Rosca\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'rosca_id' => $this->rosca_id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'contact' => $this->contact,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
