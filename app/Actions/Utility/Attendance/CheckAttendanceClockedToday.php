<?php

namespace App\Actions\Utility\Attendance;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Setting;
use App\Models\Schedule;
use Carbon\CarbonPeriod;
use App\Models\Attendance;
use App\Helpers\Utility\Authentication;

class CheckAttendanceClockedToday
{
    public function __construct()
    {
        // Get setting related to attendance
        $settings = Setting::whereIn('key', ['tolerance_clock_out', 'tolerance_clock_in'])->get(['key', 'value'])->keyBy('key')
            ->transform(function ($setting) {
                return $setting->value;
            })->toArray();

        $this->settings = $settings;
    }

    public function handle($request)
    {
        // Convert date into server time
        $employee = Authentication::getEmployeeLoggedIn();
        $user_id = $employee->user_id;
        
        if(config('app.env') !== 'production') {
            $date = Carbon::parse($request->date)->format('Y-m-d');
            $clock = Carbon::parse($request->clock)->format('H:i:s');
            $input_date = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $clock);
        }else{
            $input_date = Carbon::now();
        }

        $yesterday = Carbon::parse($input_date)->subDays(1)->format('Y-m-d');

        // Query Required Data
        $schedule = Schedule::where('user_id', $user_id)->where('date', $input_date->format('Y-m-d'))->where('is_leave', 0)->first();
        $yesterday_schedule = Schedule::where('user_id', $user_id)->where('date', $yesterday)->where('is_leave', 0)->first();
        $holiday_schedule = Schedule::where('user_id', $user_id)->where('date', $input_date->format('Y-m-d'))->where('is_leave', 1)->first();
        $today_attendance = Attendance::where('date_clock', $input_date->format('Y-m-d'))->where('user_id', $user_id)->first();

        if (isset($schedule)) {
            $shift_time_start = Carbon::parse($schedule->shift_detail->start_time);
            $clock_in_tolerance = Carbon::parse($shift_time_start)->subMinutes($this->settings['tolerance_clock_in'])->format('H:i');
            $tomorrow = Carbon::parse($schedule->date)->addDays(1)->format('Y-m-d');
            $night_shift_clock_out_tolerance = Carbon::createFromFormat('Y-m-d H:i', $tomorrow . ' ' . $schedule->shift_detail->end_time)->format('Y-m-d H:i');
            $is_leave = false;

            // Leave Check
            $leaves = Leave::where('status', 'approved')->where('employee_id', $employee->id)->get();
            foreach ($leaves as $leave) {
                $period = CarbonPeriod::create($leave->start_date, $leave->end_date);
                foreach ($period as $key => $value) {
                    if ($value->format('Y-m-d') == $schedule->date) {
                        $is_leave = true;
                    }
                }
            }
        }

        if (isset($yesterday_schedule)) {
            $tomorrow = Carbon::parse($yesterday_schedule->date)->addDays(1)->format('Y-m-d');
            $yesterday_night_shift_clock_out_tolerance = Carbon::createFromFormat('Y-m-d H:i', $tomorrow . ' ' . $yesterday_schedule->shift_detail->end_time)->format('Y-m-d H:i');
        }

        // Validate all requirement
        $clockin_leave_period = isset($schedule) ? $is_leave == true : false;
        $yesterday_clockin_outside_schedule = (isset($yesterday_schedule) ? $yesterday_schedule->shift_detail->is_night_shift == 1 && $input_date->format('Y-m-d H:i') > $yesterday_night_shift_clock_out_tolerance && $input_date->format('Y-m-d H:i') < $yesterday_night_shift_clock_out_tolerance : false);
        $clockin_outside_schedule = (isset($schedule) ? $input_date->format('Y-m-d H:i') > $night_shift_clock_out_tolerance : false);
        $holiday_clockin = isset($holiday_schedule);
        $today_already_clockout = isset($schedule) && (isset($today_attendance) ? $today_attendance->clock_out != null : false);
        $clockin_before_schedule = isset($schedule) && $input_date->format('H:i') < $clock_in_tolerance;
        $clockout_before_clockin = isset($schedule) && !isset($today_attendance);
        $already_clockout_today = isset($schedule) && (isset($today_attendance) ? $today_attendance->clock_out != null : false);

        if ($request->type === 'normal') {
            if ($clockin_leave_period) {
                return [
                    'accepted' => false,
                    'messageValidation' => "Cant Clockin In Leave Periode",
                    'status' => 400
                ];  
            } elseif ($yesterday_clockin_outside_schedule) {
                return [
                    'accepted' => false,
                    'messageValidation' => "Cant Clockin, outside schedule",
                    'status' => 400
                ];  
            } elseif ($clockin_outside_schedule) {
                return [
                    'accepted' => false,
                    'messageValidation' => "Cant Clockin, outside schedule",
                    'status' => 400
                ];  
            } elseif ($holiday_clockin) {
                return [
                    'accepted' => false,
                    'messageValidation' => "In Holiday Now, Cant Clock In",
                    'status' => 400
                ];
            } elseif ($today_already_clockout) {
                return [
                    'accepted' => false,
                    'messageValidation' => "Already Clock Out Today",
                    'status' => 409
                ];
            } elseif ($clockin_before_schedule) {
                return [
                    'accepted' => false,
                    'messageValidation' => "Belum bisa clock in. Silahkan clockin di jam " . $clock_in_tolerance,
                    'status' => 409
                ];
            } else {
                return [
                    'accepted' => true,
                    'messageValidation' => "Accepted Validation",
                    'status' => 200
                ];
            }
        } elseif ($request->type === 'clockout') {
            if ($clockout_before_clockin) {
                return [
                    'accepted' => false,
                    'messageValidation' => "Please Clock In First Before Clock Out",
                    'status' => 400
                ];
            } elseif ($already_clockout_today) {
                return [
                    'accepted' => false,
                    'messageValidation' => "Already Clock Out For Today",
                    'status' => 409
                ];
            } else {
                return [
                    'accepted' => true,
                    'messageValidation' => "Accepted Validation",
                    'status' => 200
                ];
            }
        }
    }
}