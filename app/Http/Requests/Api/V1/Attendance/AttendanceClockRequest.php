<?php

namespace App\Http\Requests\Api\V1\Attendance;

use App\Http\Requests\ApiBaseRequest;

class AttendanceClockRequest extends ApiBaseRequest
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
            'date' => 'required|date:format:Y-m-d',
            'clock' => 'required|date_format:H:i:s',
            'type' => 'required|string|in:normal,clockout',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'notes' => 'nullable|string',
            'address' => 'required|string',
            'image_id' => 'required|exists:files,id'
        ];
    }
}
