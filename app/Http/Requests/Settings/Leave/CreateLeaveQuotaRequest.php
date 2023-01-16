<?php

namespace App\Http\Requests\Settings\Leave;

use Illuminate\Foundation\Http\FormRequest;

class CreateLeaveQuotaRequest extends FormRequest
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
            'leave_type_id' => 'required',
            'branch_id' => 'required',
            'quota' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'branch_id.required' => 'The branch field is required.',
            'leave_type_id.required' => 'The leave type field is required.'
        ];
    }
}
