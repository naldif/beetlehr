<?php

namespace App\Http\Requests\Settings\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendance extends FormRequest
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
            'tolerance_notification' => 'required',
            'tolerance_clock_in' => 'required',
            'tolerance_clock_out' => 'required',
            'is_absent_force_clock_out' => 'required'
        ];
    }
}
