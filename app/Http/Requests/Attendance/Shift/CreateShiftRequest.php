<?php

namespace App\Http\Requests\Attendance\Shift;

use Illuminate\Foundation\Http\FormRequest;

class CreateShiftRequest extends FormRequest
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
            'branch_id'  => 'required',
            'name'       => 'required',
            'shift_type' => 'required',
            'start_time' => 'required',
            'end_time'   => 'required',
        ];
    }
}
