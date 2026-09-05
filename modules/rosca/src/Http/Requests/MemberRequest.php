<?php

namespace Modules\Rosca\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'rosca_id' => 'required|exists:roscas,id',
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|integer',
            'contact' => 'nullable|string|max:255',
        ];
    }
}
