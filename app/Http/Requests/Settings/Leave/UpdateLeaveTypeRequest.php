<?php

namespace App\Http\Requests\Settings\Leave;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeaveTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'quota' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'branch_id.required' => 'The branch field is required.'
        ];
    }
}
