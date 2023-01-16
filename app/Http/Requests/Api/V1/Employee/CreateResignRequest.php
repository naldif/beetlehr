<?php

namespace App\Http\Requests\Api\V1\Employee;

use Carbon\Carbon;
use App\Http\Requests\ApiBaseRequest;

class CreateResignRequest extends ApiBaseRequest
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
        $rules = [
            'date' => 'required',
            'reason' => 'required|string',
            'is_according_procedure' => 'required|digits_between:0,1',
            'file' => 'required|mimes:pdf,jpeg,png,jpg|max:3072'
        ];

        $date = $this->input('date');
        $maxManualDate = Carbon::parse($date)->addDays(30);

        if ($this->input('is_according_procedure') == 1) {
            $rules['end_contract'] = 'required|after:date';
        } else {
            $rules['end_contract'] = 'required|before:' . $maxManualDate . '|after:date';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'end_contract.before' => "Only can input date before 30 days after start date if you choose not according procedure"
        ];
    }
}
