<?php

namespace App\Http\Requests\Settings\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBranchRequest extends FormRequest
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
            'name' => 'required|string|unique:branches,name,' . $this->id,
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip_code' => 'required|string',
            'latitude'  => 'required|string',
            'longitude' => 'required|string',
            'radius'    => 'required|numeric',
            'timezone'  => 'required|string',
            'telegram_chat_id' => 'nullable|string|unique:branches,telegram_chat_id,' . $this->id,
            'npwp_list_id' => 'required|exists:npwp_lists,id'
        ];
    }
}
