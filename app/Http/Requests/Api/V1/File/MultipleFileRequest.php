<?php

namespace App\Http\Requests\Api\V1\File;

use App\Http\Requests\ApiBaseRequest;

class MultipleFileRequest extends ApiBaseRequest
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
            'files' => 'required',
            'files.*' => 'mimes:pdf,jpeg,png,jpg,mp4|max:8192'
        ];
    }
}
