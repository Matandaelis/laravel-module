<?php

namespace Modules\Rosca\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoscaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cycle_period' => 'required|string|in:weekly,monthly,quarterly,yearly',
            'contribution_amount' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
        ];
    }
}
