<?php

namespace App\Http\Requests\Employee\Resign;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResignRequest extends FormRequest
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
        if ($this->action === 'reject') {
            $reject_reason_rules = 'required|string';
        } else {
            $reject_reason_rules = 'nullable|string';
        }

        return [
            'reject_reason' => $reject_reason_rules
        ];
    }
}
