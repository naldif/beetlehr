<?php

namespace App\Services\Settings\Systems;

use App\Models\Setting;

class AuthenticationService
{
    public function getData()
    {
        // Get All General Payroll Settings
        $settings = Setting::whereIn('key', ['lock_user_device'])
            ->get(['key', 'value'])->keyBy('key')
            ->transform(function ($setting) {
                return $setting->value;
            })
            ->toArray();

        return $settings;
    }

    public function updateData($request)
    {
        $inputs = $request->only(['lock_user_device']);

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
