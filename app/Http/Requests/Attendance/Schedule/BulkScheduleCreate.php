<?php

namespace App\Http\Requests\Attendance\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class BulkScheduleCreate extends FormRequest
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
            'type' => 'required',
            'shift_id' => 'required', 
            'date' => 'required',
            'group_id' => 'required_if:type,Group', 
            'employee_id' => 'required_if:type,Employee'
        ];
    }

    public function messages()
    {
        return [
            'shift_id' => 'The shift field is required.',
            'group_id' => 'The group field is required.',
            'employee_id' => 'The employee field is required.',
        ];
    }
}
