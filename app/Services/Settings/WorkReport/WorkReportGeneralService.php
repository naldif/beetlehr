<?php

namespace App\Services\Settings\WorkReport;

use App\Models\Setting;

class WorkReportGeneralService
{
    public function getMaxTime()
    {
        $settings = Setting::whereIn('key', ['max_time_work_report'])
            ->get(['key', 'value'])->keyBy('key')
            ->transform(function ($setting) {
                return $setting->value;
            })
            ->toArray();

        return $settings;
    }

    public function updateMaxTime($request)
    {
        $inputs = $request->only(['max_time_work_report']);

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
