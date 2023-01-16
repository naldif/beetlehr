<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Http\Requests\ApiBaseRequest;

class UpdateFcmRequest extends ApiBaseRequest
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
            'fcm_token' => 'required|string',
        ];
    }
}
