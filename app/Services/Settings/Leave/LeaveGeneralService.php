<?php

namespace App\Services\Settings\Leave;

use App\Models\Setting;

class LeaveGeneralService
{
    public function getResetLeave()
    {
        $settings = Setting::whereIn('key', ['date_reset_leave_quota'])
            ->get(['key', 'value'])->keyBy('key')
            ->transform(function ($setting) {
                return $setting->value;
            })
            ->toArray();

        return $settings;
    }

    public function updateResetLeave($request)
    {
        $inputs = $request->only(['date_reset_leave_quota']);

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
