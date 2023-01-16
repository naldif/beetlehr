<?php

namespace App\Http\Requests\Employee\Resign;

use Illuminate\Foundation\Http\FormRequest;

class CreateResignRequest extends FormRequest
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
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date_format:Y-m-d',
            'end_contract' => 'required|date_format:Y-m-d|after:date',
            'reason' => 'required|string',
            'file' => 'required|file|max:3072'
        ];
    }
}
