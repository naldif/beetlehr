<?php

namespace App\Actions\Utility\Attendance;

use Carbon\Carbon;
use App\Models\Setting;
use App\Helpers\Utility\Time;


class CalculateAttendanceWorkHours
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

    public function calculatePresentWorkHours()
    {
        $present_hours = $this->attendances->map(function ($model) {
            if ($this->settings['is_absent_force_clock_out'] == 1) {
                $force_clock_out_check = $model->is_force_clock_out !== null && $model->clock_out !== null && $model->is_force_clock_out !== 1;
                if($force_clock_out_check) {
                    return $model->total_work_hours;
                }else{
                    return '00:00:00';
                }
            } else {
                return $model->total_work_hours;
            }
        })->toArray();

        $total_work_hours = Time::calculateTotalHours($present_hours);

        return $total_work_hours;
    }

    public function calculateLateWorkHours(){
        $late_hours = $this->attendances->map(function ($model) {
            if ($this->settings['is_absent_force_clock_out'] == 1) {
                $force_clock_out_check = $model->is_force_clock_out !== null && $model->clock_out !== null && $model->is_force_clock_out !== 1 && $model->total_late_clock_in !== null;
                if ($force_clock_out_check) {
                    return $model->total_late_clock_in;
                } else {
                    return '00:00:00';
                }
            } else {
                return $model->total_late_clock_in;
            }
        })->toArray();

        $total_work_hours = Time::calculateTotalHours($late_hours);

        return $total_work_hours;
    }

    public function calculateClockoutEarlyWorkHours()
    {
        $early_work_hours = $this->attendances->map(function ($model) {
            if ($this->settings['is_absent_force_clock_out'] == 1) {
                $force_clock_out_check = $model->is_force_clock_out !== null && $model->clock_out !== null && $model->is_force_clock_out !== 1 && $model->total_early_clock_out !== null;
                if ($force_clock_out_check) {
                    return $model->total_early_clock_out;
                } else {
                    return '00:00:00';
                }
            } else {
                return $model->total_early_clock_out;
            }
        })->toArray();

        $total_work_hours = Time::calculateTotalHours($early_work_hours);

        return $total_work_hours;
    }

    public function calculateTotalWorkHours()
    {
        $early_work_hours = $this->attendances->map(function ($model) {
            if ($this->settings['is_absent_force_clock_out'] == 1) {
                $force_clock_out_check = $model->is_force_clock_out !== null && $model->is_force_clock_out !== 1;
                if ($force_clock_out_check) {
                    return $model->total_work_hours;
                } else {
                    return '00:00:00';
                }
            } else {
                return $model->total_work_hours;
            }
        })->toArray();

        $total_work_hours = Time::calculateHours($early_work_hours);

        return $total_work_hours;
    }

    public function calculateAttendanceTotalWorkHours()
    {
        $total_hours = $this->attendances->map(function ($model) {
            if ($this->settings['is_absent_force_clock_out'] == 1) {
                $force_clock_out_check = $model->is_force_clock_out !== null && $model->is_force_clock_out !== 1;
                if ($force_clock_out_check) {
                    return $model->total_work_hours;
                } else {
                    return '00:00:00';
                }
            } else {
                return $model->total_work_hours;
            }
        })->toArray();

        $total_work_hours = Time::calculateTotalHours($total_hours);

        return $total_work_hours;
    }

    public function calculateAbsentWorkHours($schedules)
    {
        $work_hours = [];

        foreach ($schedules as $schedule) {
            $attendance = $this->attendances->where('date_clock', $schedule->date)->first();
            if (!isset($attendance) && $schedule->date < Carbon::now()->format('Y-m-d')) {
                $end_time =  Carbon::parse($schedule->shift_detail->end_time);
                $start_time =  Carbon::parse($schedule->shift_detail->start_time);
                $time = $end_time->diff($start_time)->format('%H:%I:%S');
                array_push($work_hours, $time);
            } elseif ((isset($attendance) ? $attendance->is_force_clock_out == 1 : false) && $this->settings['is_absent_force_clock_out'] == 1) {
                $end_time =  Carbon::parse($schedule->shift_detail->time_end);
                $start_time =  Carbon::parse($schedule->shift_detail->time_start);
                $time = $end_time->diff($start_time)->format('%H:%I:%S');
                array_push($work_hours, $time);
            }
        }

        $total_work_hours = Time::calculateTotalHours($work_hours);
        return $total_work_hours;
    }
}
