<?php

namespace App\Http\Requests\Api\V1\Attendance;

use App\Http\Requests\ApiBaseRequest;

class CheckAttendanceLocationRequest extends ApiBaseRequest
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
            'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/']
        ];
    }

    public function messages()
    {
        return [
            'latitude.required' => "Latitude is required",
            'latitude.regex' => "Latitude value in incorrect format",
            'longitude.required' => "Longitude is required",
            'longitude.regex' => "Longitude value in incorrect format"
        ];
    }
}
