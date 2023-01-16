<?php

namespace App\Actions\Utility\Attendance;

use Carbon\Carbon;
use App\Models\Setting;
use Carbon\CarbonPeriod;


class CalculateAttendanceStatus
{
    public function __construct($attendances)
    {
        // Get setting related to attendance
        $settings = Setting::whereIn('key', ['is_absent_force_clock_out'])->get(['key', 'value'])->keyBy('key')
        ->transform(function ($setting) {
            return $setting->value;
        })->toArray();

        $this->settings = $settings;
        $this->attendances = collect($attendances);
    }

    public function calculatePresentStatus()
    {
        if ($this->settings['is_absent_force_clock_out'] == 1) {
            $totalPresent = $this->attendances->where('is_force_clock_out', null)->where('clock_out', '!=', null)->count();
        } else {
            $totalPresent = $this->attendances->where('clock_out', '!=', null)->count();
        }

        return $totalPresent;
    }

    public function calculateOntimeStatus()
    {
        if ($this->settings['is_absent_force_clock_out'] == 1) {
            $totalPresent = $this->attendances->where('is_force_clock_out', null)->where('clock_out', '!=', null)->where('is_late_clock_in', 0)->where('is_early_clock_out', 0)->count();
        } else {
            $totalPresent = $this->attendances->where('clock_out', '!=', null)->where('is_late_clock_in', 0)->where('is_early_clock_out', 0)->count();
        }

        return $totalPresent;
    }

    public function calculateAbsentStatus($schedules)
    {
        $absent = 0;

        foreach ($schedules as $schedule) {
            $attendance = $this->attendances->where('date_clock', $schedule->date)->first();
            if (!isset($attendance) && $schedule->date < Carbon::now()->format('Y-m-d')) {
                $absent += 1;
            } elseif ((isset($attendance) ? $attendance->is_force_clock_out == 1 : false) && $this->settings['is_absent_force_clock_out'] == 1) {
                $absent += 1;
            }
        }

        return $absent;
    }

    public function calculateLateStatus()
    {
        if ($this->settings['is_absent_force_clock_out'] == 1) {
            $totalLate = $this->attendances->where('is_late_clock_in', 1)->where('is_force_clock_out', null)->where('clock_out', '!=', null)->count();
        } else {
            $totalLate = $this->attendances->where('is_late_clock_in', 1)->where('clock_out', '!=', null)->count();
        }

        return $totalLate;
    }

    public function calculateClockoutEarlyStatus()
    {
        if ($this->settings['is_absent_force_clock_out'] == 1) {
            $totalEarlyClockOut = $this->attendances->where('is_early_clock_out', 1)->where('is_force_clock_out', null)->where('clock_out', '!=', null)->count();
        } else {
            $totalEarlyClockOut = $this->attendances->where('is_early_clock_out', 1)->where('clock_out', '!=', null)->count();
        }
        return $totalEarlyClockOut;
    }

    public function calculateLeaveStatus($leaves)
    {
        $total_leaves = 0;

        foreach ($leaves as $leave) {
            $day = count(CarbonPeriod::create($leave->start_date, $leave->end_date)->toArray());
            $total_leaves += $day;
        }

        return $total_leaves;
    }

    public function calculateHolidayStatus($schedules)
    {
        $holiday = collect($schedules)->where('is_leave', 1)->count();

        return $holiday;
    }
}
