<?php

namespace App\Http\Requests\Settings\Payroll;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePayrollComponentRequest extends FormRequest
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
        $action = $this->request->get('action');

        if ($action === 'deduction_late') {
            return [
                'late_tolerance' => 'required|numeric'
            ];
        } else if ($action === 'earning_overtime') {
            return [];
        } else {
            return [
                'name' => 'required|string',
                'type' => 'required|string'
            ];
        }
    }
}
