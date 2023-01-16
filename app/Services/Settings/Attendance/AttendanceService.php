<?php

namespace App\Services\Settings\Attendance;

use App\Models\Setting;

class AttendanceService
{
    public function getData()
    {
        $settings = Setting::whereIn('key', ['tolerance_notification', 'tolerance_clock_in', 'tolerance_clock_out', 'is_absent_force_clock_out', 'time_for_force_clockout_type', 'time_for_force_clockout_fixed', 'time_for_force_clockout_minutes'])
            ->get(['key', 'value'])->keyBy('key')
            ->transform(function ($setting) {
                return $setting->value;
            })
            ->toArray();

        return $settings;
    }

    public function updateData($request)
    {
        $inputs = $request->only(['tolerance_notification', 'tolerance_clock_in', 'tolerance_clock_out', 'is_absent_force_clock_out', 'time_for_force_clockout_type', 'time_for_force_clockout_fixed', 'time_for_force_clockout_minutes']);

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
