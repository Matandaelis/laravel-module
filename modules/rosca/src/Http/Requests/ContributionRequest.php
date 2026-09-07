<?php

namespace Modules\Rosca\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContributionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'rosca_id' => 'required|exists:roscas,id',
            'member_id' => 'required|exists:rosca_members,id',
            'amount' => 'required|numeric|min:0',
            'contributed_at' => 'nullable|date'
        ];
    }
}
