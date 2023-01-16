<?php

namespace App\Http\Requests\Api\V1\Leave;

use Carbon\Carbon;
use App\Http\Requests\ApiBaseRequest;

class CreateLeaveRequest extends ApiBaseRequest
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
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|before:end_date',
            'end_date' => 'required',
            'reason' => 'required|string',
            'file' => 'required|mimes:pdf,jpeg,png,jpg|max:3072'
        ];
    }
}
