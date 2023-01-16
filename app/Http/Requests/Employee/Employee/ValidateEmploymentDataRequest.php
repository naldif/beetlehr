<?php

namespace App\Http\Requests\Employee\Employee;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class ValidateEmploymentDataRequest extends FormRequest
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
        $settings = Setting::whereIn('key', ['payroll_istaxable'])->get(['key', 'value'])->keyBy('key')
                    ->transform(function ($setting) {
                        return $setting->value;
                    })->toArray();
        
        if($settings['payroll_istaxable'] == 1) {
            $ptkp_tax_rule = 'required';
        }else{
            $ptkp_tax_rule = 'nullable';
        }

        if($this->pkwt_type === 'pkwt') {
            $end_contract_rule = 'required|date_format:Y-m-d';
        }else{
            $end_contract_rule = 'nullable';
        }

        return [
            'branch_id' => 'required|exists:branches,id',
            'employment_status_id' => 'required|exists:employment_statuses,id',
            'ptkp_tax_list_id' => $ptkp_tax_rule,
            'start_date' => 'required',
            'end_date' => $end_contract_rule,
            'payroll_group_id' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'end_date.date_format' => 'The end date field is required'
        ];
    }
}
