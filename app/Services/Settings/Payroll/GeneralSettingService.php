<?php

namespace App\Services\Settings\Payroll;

use App\Models\Setting;

class GeneralSettingService
{
    public function getPayrollSettings()
    {
        // Get All General Payroll Settings
        $settings = Setting::whereIn('key', ['payroll_istaxable'])
                    ->get(['key', 'value'])->keyBy('key')
                    ->transform(function ($setting) {
                        return $setting->value;
                    })
                    ->toArray();

        return $settings;
    }

    public function updateData($request)
    {
        $inputs = $request->only(['payroll_istaxable']);

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
