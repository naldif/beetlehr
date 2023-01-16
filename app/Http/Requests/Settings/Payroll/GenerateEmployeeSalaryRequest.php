<?php

namespace App\Http\Requests\Settings\Payroll;

use Illuminate\Foundation\Http\FormRequest;

class GenerateEmployeeSalaryRequest extends FormRequest
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
            'branch_id' => 'required',
            'designation_id' => 'required',
            'employee_id' => 'nullable',
            'type' => 'required|string',
            'amount' => 'required|numeric'
        ];
    }
}
