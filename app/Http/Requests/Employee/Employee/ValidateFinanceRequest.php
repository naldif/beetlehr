<?php

namespace App\Http\Requests\Employee\Employee;

use Illuminate\Foundation\Http\FormRequest;

class ValidateFinanceRequest extends FormRequest
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
        if($this->is_use_bpjsk == 'true') {
            $bpjsk_setting_rule = 'required_if:is_use_bpjsk,true|numeric';
        }else{
            $bpjsk_setting_rule = 'required_if:is_use_bpjsk,true';
        }

        if ($this->is_use_bpjstk == 'true') {
            $bpjstk_setting_rule = 'required_if:is_use_bpjstk,true|numeric';
        } else {
            $bpjstk_setting_rule = 'required_if:is_use_bpjstk,true';
        }

        return [
            'account_number' => 'required|string',
            'bank_name' => 'required|string',
            'account_name' => 'required|string',

            'is_use_bpjstk' => 'sometimes',
            'bpjstk_use_specific_amount' => 'sometimes',
            'bpjstk_number_card' => 'required_if:is_use_bpjstk,true',
            'bpjstk_setting_id' => $bpjstk_setting_rule,
            'bpjstk_specific_amount' => 'required_if:bpjstk_use_specific_amount,true',

            'is_use_bpjsk' => 'sometimes',
            'bpjsk_use_specific_amount' => 'sometimes',
            'bpjsk_number_card' => 'required_if:is_use_bpjsk,true',
            'bpjsk_setting_id' => $bpjsk_setting_rule,
            'bpjsk_specific_amount' => 'required_if:bpjsk_use_specific_amount,true',
        ];
    }

    public function messages()
    {
        return [
            'bpjsk_setting_id.numeric' => 'The bpjsk setting id field is required when is use bpjsk is true'
        ];
    }
}
