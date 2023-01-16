<?php

namespace App\Http\Requests\Settings\Overtime;

use Illuminate\Foundation\Http\FormRequest;

class CreateRuleRequest extends FormRequest
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
            'clock_in' => 'required',
            'clock_out' => 'required',
        ];
    }
}
