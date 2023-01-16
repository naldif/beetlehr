<?php

namespace App\Http\Requests\Api\V1\File;

use App\Http\Requests\ApiBaseRequest;

class MultipleOptionalFileRequest extends ApiBaseRequest
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
            'files.*' => 'file|max:8192',
            'files' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'files.max' => 'File cant be more than 8192 kb',
        ];
    }
}
