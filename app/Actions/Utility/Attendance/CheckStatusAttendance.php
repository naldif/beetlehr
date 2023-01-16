<?php

namespace App\Actions\Utility\Attendance;

use Carbon\Carbon;
use App\Models\Setting;

class CheckStatusAttendance
{
    public function __construct()
    {
        // Get setting related to attendance
        $settings = Setting::whereIn('key', ['is_absent_force_clock_out'])->get(['key', 'value'])->keyBy('key')
        ->transform(function ($setting) {
            return $setting->value;
        })->toArray();

        $this->settings = $settings;
    }

    public function handle($attendance, $schedule, $leavePeriod, $date)
    {
        $status = '';

        // Leave Check
        if (in_array($date, $leavePeriod)) {
            return 'leave';
        }

        // Attendance Based Check
        if (isset($attendance)) {
            // Define All Status Condition
            if ($attendance->is_late_clock_in == 1) {
                $status = 'late';
            } else if ($attendance->is_early_clock_out == 1) {
                $status = 'clockout_early';
            } else {
                $status = 'present';
            }

            if ($this->settings['is_absent_force_clock_out'] == 1 && $attendance->is_force_clock_out == 1) {
                $status = 'absent';
            }
        } else {
            // Define All Status Condition
            $holiday = isset($schedule) && $schedule->is_leave == 1;
            $absent = isset($schedule) && $schedule->date < Carbon::now()->format('Y-m-d');
            $unassign = !isset($schedule);

            if ($unassign) {
                $status = 'unassigned';
            } elseif ($holiday) {
                $status = 'holiday';
            } elseif ($absent) {
                $status = 'absent';
            } else {
                $status = 'netral';
            }
        }

        return $status;
    }
}