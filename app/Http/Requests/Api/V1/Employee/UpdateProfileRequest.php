<?php

namespace App\Http\Requests\Api\V1\Employee;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiBaseRequest;
use App\Helpers\Utility\Authentication;

class UpdateProfileRequest extends ApiBaseRequest
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
        $employee = Authentication::getEmployeeLoggedIn();
        
        return [
            'name' => 'required|string',
            'email' => ['required', 'string',  Rule::unique('users', 'email')->ignore($employee->user_id)],
            'phone_number' => 'required|phone:ID',
            'address' => 'required|string',
            'account_number' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:3072'
        ];
    }

    public function messages()
    {
        return [
            'phone_number.phone' => 'Please input a valid phone number'
        ];
    }
}
