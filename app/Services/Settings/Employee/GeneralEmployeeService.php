<?php

namespace App\Services\Settings\Employee;

use App\Models\Setting;

class GeneralEmployeeService
{
    public function getEditableNip()
    {
        $settings = Setting::whereIn('key', ['editable_employee_external_id'])
            ->get(['key', 'value'])->keyBy('key')
            ->transform(function ($setting) {
                return $setting->value;
            })
            ->toArray();

        return $settings;
    }

    public function updateEditableNip($request)
    {
        $inputs = $request->only(['editable_employee_external_id']);

        foreach ($inputs as $key => $value) {
            Setting::updateOrCreate([
                'key' => $key
            ], [
                'value' => $value
            ]);
        }

        return true;
    }
}
