<?php

namespace App\Http\Requests\NoticeBoard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNoticeBoardRequest extends FormRequest
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
            'branch_id' => 'required',
            'type' => 'required|string',
            'start' => 'required',
            'end' => 'required|after:start',
            'title' => 'required',
            'description' => 'required_if:type,description|nullable|string',
            'file' => 'nullable|file',
        ];
    }

    public function messages()
    {
        return [
            'start.required' => 'The start date field is required',
            'end.required' => 'The end date field is required'
        ];
    }
}
