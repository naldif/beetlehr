<?php

namespace App\Services\Settings\Attendance;

use App\Models\Setting;

class AttendanceGeneralService
{
    public function getCloseBreakup()
    {
        $settings = Setting::whereIn('key', ['close_break_page'])
            ->get(['key', 'value'])->keyBy('key')
            ->transform(function ($setting) {
                return $setting->value;
            })
            ->toArray();

        return $settings;
    }

    public function updateCloseBreakup($request)
    {
        $inputs = $request->only(['close_break_page']);

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
