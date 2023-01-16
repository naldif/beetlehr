<?php

namespace App\Http\Requests\Settings\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNpwpRequest extends FormRequest
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
            'npwp_name' => 'required|string',
            'number_npwp' => 'required|string',
            'npwp_company_name' => 'required|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'kpp' => 'nullable|string',
            'active_month' => 'required|string',
            'status' => 'nullable',
        ];
    }
}
