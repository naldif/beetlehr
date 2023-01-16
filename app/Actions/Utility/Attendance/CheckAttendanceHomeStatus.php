<?php

namespace App\Actions\Utility\Attendance;

use Carbon\Carbon;
use App\Models\Setting;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\AttendanceOffline;
use App\Helpers\Utility\Authentication;
use App\Helpers\Utility\Attendance\BreakAttendance;

class CheckAttendanceHomeStatus
{
    public function __construct()
    {
        // Get setting related to attendance
        $settings = Setting::whereIn('key', ['tolerance_clock_out'])->get(['key', 'value'])->keyBy('key')
            ->transform(function ($setting) {
                return $setting->value;
            })->toArray();

        $this->settings = $settings;
    }

    public function handle($date)
    {
        // Convert date into server time
        $employee = Authentication::getEmployeeLoggedIn();
        $today = Carbon::now();
        $input_date = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($date)->format('Y-m-d') . ' ' . $today->format('H:i:s'));
        $yesterday = Carbon::parse($input_date)->subDays(1)->format('Y-m-d');

        // Query Required Data
        $schedule = Schedule::where('user_id', $employee->user_id)->where('date', $input_date->format('Y-m-d'))->first();
        $yesterday_schedule = Schedule::where('user_id', $employee->user_id)->where('date', $yesterday)->where('is_leave', 0)->first();
        $yesterday_attendance = Attendance::where('date_clock', $yesterday)->where('user_id', $employee->user_id)->first();
        $today_attendance = Attendance::where('date_clock', $input_date->format('Y-m-d'))->where('user_id', $employee->user_id)->first();
        $yesterday_attendance_offline = AttendanceOffline::where('date_clock', $yesterday)->where('user_id', $employee->user_id)->where('status', '!=', 'cancelled')->first();
        $today_attendance_offline = AttendanceOffline::where('date_clock', $input_date->format('Y-m-d'))->where('user_id', $employee->user_id)->where('status', '!=', 'cancelled')->first();

        if (isset($yesterday_schedule)) {
            $yesterday_schedule_time = Carbon::createFromFormat('Y-m-d H:i', $yesterday_schedule->date . ' ' . $yesterday_schedule->shift_detail->end_time);
            if ($yesterday_schedule->shift_detail->is_night_shift == 1) {
                $yesterday_clock_out_tolerance = Carbon::parse($yesterday_schedule_time)->addDays(1)->addMinutes($this->settings['tolerance_clock_out']);
            } else {
                $yesterday_clock_out_tolerance = Carbon::parse($yesterday_schedule_time)->addMinutes($this->settings['tolerance_clock_out']);
            }
        }

        // Validate all requirement
        $clockin_on_yesterday_schedule = (isset($yesterday_schedule) ? $yesterday_schedule->shift_detail->is_night_shift == 1 && $input_date->format('H:i:s') <= $yesterday_schedule->shift_detail->time_end : false) && !isset($yesterday_attendance);
        $clockout_on_yesterday_schedule = (isset($yesterday_schedule) ? $yesterday_schedule->shift_detail->is_night_shift == 1  && $input_date->format('Y-m-d H:i') <= $yesterday_clock_out_tolerance->format('Y-m-d H:i') : false) && (isset($yesterday_attendance) ? $yesterday_attendance->clock_out == null : false);
        $clockin_on_today_schedule = isset($schedule) && !isset($today_attendance);
        $clockout_on_today_schedule = isset($schedule) && isset($today_attendance) ? $today_attendance->clock_out == null : false;
        $clockout_yesterday_without_schedule = !isset($yesterday_schedule) && (isset($yesterday_attendance_offline) ? $yesterday_attendance_offline->clock_out == null : false);
        $clockin_today_without_schedule = !isset($schedule) && !isset($today_attendance_offline);
        $clockout_today_without_schedule = !isset($schedule) && isset($today_attendance_offline) ? $today_attendance_offline->clock_out == null : false;

        if ($clockin_on_yesterday_schedule) {
            return [
                'type' => 'clockin',
                'attendance' => null,
                'message_type' => null,
                'break_type' => null,
                'start_break_time' => null
            ];
        } elseif ($clockout_on_yesterday_schedule) {
            // Get Break Status 
            $status = BreakAttendance::buttonBreakHome($yesterday_attendance);

            return [
                'type' => 'clockout',
                'attendance' => $yesterday_attendance,
                'message_type' => null,
                'break_type' => $status['status'],
                'start_break_time' => $status['start_break_time']
            ];
        } elseif ($clockin_on_today_schedule) {
            return [
                'type' => 'clockin',
                'attendance' => null,
                'message_type' => null,
                'break_type' => null,
                'start_break_time' => null
            ];
        } elseif ($clockout_on_today_schedule) {
            // Get Break Status 
            $status = BreakAttendance::buttonBreakHome($today_attendance);

            return [
                'type' => 'clockout',
                'attendance' => $today_attendance,
                'message_type' => null,
                'break_type' => $status['status'],
                'start_break_time' => $status['start_break_time']
            ];
        } elseif ($clockout_yesterday_without_schedule) {
            // Get Break Status 
            $status = BreakAttendance::buttonBreakHomeAbnormal($yesterday_attendance_offline);

            return [
                'type' => 'clockout',
                'attendance' => $yesterday_attendance_offline,
                'message_type' => 'clockout',
                'break_type' => $status['status'],
                'start_break_time' => $status['start_break_time']
            ];
        } elseif ($clockin_today_without_schedule) {
            return [
                'type' => 'clockin',
                'attendance' => null,
                'message_type' => 'clockin',
                'break_type' => null,
                'start_break_time' => null
            ];
        } elseif ($clockout_today_without_schedule) {
            // Get Break Status 
            $status = BreakAttendance::buttonBreakHomeAbnormal($today_attendance_offline);

            return [
                'type' => 'clockout',
                'attendance' => $today_attendance_offline,
                'message_type' => 'clockout',
                'break_type' => $status['status'],
                'start_break_time' => $status['start_break_time']
            ];
        } else {
            if (isset($schedule)) {
                return [
                    'type' => 'clockin',
                    'attendance' => $today_attendance,
                    'message_type' => null,
                    'break_type' => null,
                    'start_break_time' => null
                ];
            } else {
                return [
                    'type' => 'clockin',
                    'attendance' => $today_attendance_offline,
                    'message_type' => 'clockin',
                    'break_type' => null,
                    'start_break_time' => null
                ];
            }
        }
    }
}