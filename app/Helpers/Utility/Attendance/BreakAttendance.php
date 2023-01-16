<?php

namespace App\Helpers\Utility\Attendance;

use Carbon\Carbon;
use App\Models\BreakTime;
use App\Models\Attendance;
use App\Models\BreakTimeOffline;
use App\Models\AttendanceOffline;
use App\Helpers\Utility\Authentication;

class BreakAttendance
{
    public static function calculateTotalWorkHours($start_time, $end_time)
    {
        $clock_in = Carbon::parse($start_time);
        $clock_out = Carbon::parse($end_time);

        if ($end_time < $start_time) {
            $today = Carbon::now()->addDays(1)->format('Y-m-d');
            $clock_out = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $end_time);
        }

        return gmdate("H:i:s", $clock_out->diffInSeconds($clock_in));
    }

    public static function buttonBreakHome($attendance)
    {
        $break = BreakTime::where('attendance_id', $attendance->id)->latest()->first();
        $result = [];

        if (!isset($break)) {
            $result['status'] = 'start';
            $result['start_break_time'] = null;
        } elseif ($break->end_time === null) {
            $result['status'] = 'end';
            $result['start_break_time'] = $break->start_time;
        } else {
            $result['status'] = 'start';
            $result['start_break_time'] = null;
        }

        return $result;
    }

    public static function buttonBreakHomeAbnormal($attendance)
    {
        $break = BreakTimeOffline::where('attendance_offline_log_id', $attendance->id)->latest()->first();
        $result = [];

        if (!isset($break)) {
            $result['status'] = 'start';
            $result['start_break_time'] = null;
        } elseif ($break->end_time === null) {
            $result['status'] = 'end';
            $result['start_break_time'] = $break->start_time;
        } else {
            $result['status'] = 'start';
            $result['start_break_time'] = null;
        }

        return $result;
    }

    public static function getAttendanceForBreakAbnormal($request)
    {
        // Init required data
        $employee = Authentication::getEmployeeLoggedIn();
        $user_id = $employee->user_id;

        if (config('app.env') !== 'production') {
            $today = Carbon::parse($request->date)->format('Y-m-d');
        } else {
            $today = Carbon::now()->format('Y-m-d');
        }

        $yesterday = Carbon::parse($today)->subDays(1)->format('Y-m-d');

        // Get Attendance Id base on user_id and active attendance
        $yesterday_attendance_offline = AttendanceOffline::where('date_clock', $yesterday)->where('user_id', $user_id)->where('status', '!=', 'cancelled')->where('clock_out', null)->first();
        $today_attendance_offline = AttendanceOffline::where('date_clock', $today)->where('user_id', $user_id)->where('status', '!=', 'cancelled')->where('clock_out', null)->first();
        isset($yesterday_attendance_offline) ? $attendance = $yesterday_attendance_offline : $attendance = $today_attendance_offline;

        // Check if attendance didnt found
        if (!isset($attendance)) {
            throw new \Exception('You are not active in today attendance, cant clock for break');
        }

        return $attendance;
    }

    public static function getAttendanceForBreak($request)
    {
        // Init required data
        $employee = Authentication::getEmployeeLoggedIn();
        $user_id = $employee->user_id;

        if (config('app.env') !== 'production') {
            $today = Carbon::parse($request->date)->format('Y-m-d');
        } else {
            $today = Carbon::now()->format('Y-m-d');
        }
        $yesterday = Carbon::parse($today)->subDays(1)->format('Y-m-d');

        // Get Attendance Id base on user_id and active attendance
        $yesterday_attendance = Attendance::where('date_clock', $yesterday)->where('user_id', $user_id)->where('clock_out', null)->first();
        $today_attendance = Attendance::where('date_clock', $today)->where('user_id', $user_id)->where('clock_out', null)->first();
        isset($yesterday_attendance) ? $attendance = $yesterday_attendance : $attendance = $today_attendance;

        // Check if attendance didnt found
        if (!isset($attendance)) {
            throw new \Exception('You are not active in today attendance, cant clock for break');
        }

        return $attendance;
    }
}
