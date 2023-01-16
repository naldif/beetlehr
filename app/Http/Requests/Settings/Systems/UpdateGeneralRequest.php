<?php

namespace App\Http\Requests\Settings\Systems;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeneralRequest extends FormRequest
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
            'date_reset_leave_quota' => 'required',
            'max_time_activity' => 'required',
            'max_time_work_report' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'date_reset_leave_quota' => 'This Field is Required',
            'max_time_activity' => 'This Field is Required',
            'max_time_work_report' => 'This Field is Required'
        ];
    }
}
