<?php

namespace App\Http\Requests\Settings\Employee;

use Illuminate\Foundation\Http\FormRequest;

class CreateGroup extends FormRequest
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
            'name'          => 'required',
            'branch_id'  => 'required',
            'employee_id'   => 'required'
        ];
    }

    public function messages()
    {
        return [
            'branch_id.required' => 'The branch field is required.',
            'employee_id.required'  => 'The employee field is required.',
        ];
    }
}
