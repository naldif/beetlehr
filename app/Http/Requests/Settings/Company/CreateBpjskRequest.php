<?php

namespace App\Http\Requests\Settings\Company;

use Illuminate\Foundation\Http\FormRequest;

class CreateBpjskRequest extends FormRequest
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
            'registration_number' => 'nullable|string',
            'bpjs_office' => 'nullable|string',
            'minimum_value' => 'nullable|numeric',
            'valid_month' => 'required|string',
            'status' => 'nullable',
        ];
    }
}
