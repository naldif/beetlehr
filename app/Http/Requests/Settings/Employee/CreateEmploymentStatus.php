<?php

namespace App\Http\Requests\Settings\Employee;

use Illuminate\Foundation\Http\FormRequest;

class CreateEmploymentStatus extends FormRequest
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
            'name' => 'required',
            'pkwt_type' => 'required',
            // 'status' => 'required'
        ];
    }

    public function messages(){
        return [
            'pkwt_type.required' => 'The employment type field is required.' 
        ];
    }
}
