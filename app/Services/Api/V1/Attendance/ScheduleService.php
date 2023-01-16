<?php

namespace App\Services\Api\V1\Attendance;

use Carbon\Carbon;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Helpers\Utility\Authentication;

class ScheduleService
{
    public function getData($request)
    {
        // Required Init Data
        $employee = Authentication::getEmployeeLoggedIn();
        $today = Carbon::now();
        $user_id = $employee->user_id;
        $per_page = $request->per_page ?: 10;
        $page = $request->page ?: 1;
        $date = $request->date ? Carbon::parse($request->date)->format('Y-m-d') : $today->format('Y-m-d');
        $end_date = $request->endDate ? Carbon::parse($request->endDate)->format('Y-m-d') : $date;
        $yesterday = Carbon::parse($date)->subDays(1)->format('Y-m-d');

        // Query requirement data to get schedule
        $yesterday_schedule = Schedule::with(['shift_detail'])->whereHas('shift')->where('user_id', $user_id)->where('date', $yesterday)->first(); 
        $yesterday_attendance = Attendance::where('date_clock', $yesterday)->where('user_id', $user_id)->first();

        // Get Schedule 
        $query = Schedule::with(['shift_detail'])->where('user_id', $user_id);

        // Checking if employee has ongoing yesterday attendance / Night Shift, and filter it
        $yesterday_clock_out_tolerance = isset($yesterday_schedule) ? Carbon::parse($yesterday_schedule->shift_detail->start_time)->format('H:i') : null;
        $validate_yesterday_schedule_exists = isset($yesterday_schedule) ? $yesterday_schedule->shift_detail->is_night_shift === 1  && $today->format('H:i') <= $yesterday_clock_out_tolerance : false;
        $validate_yesterday_attendance_exists = isset($yesterday_attendance) ? $yesterday_attendance->clock_out == null : false;

        if($validate_yesterday_schedule_exists && $validate_yesterday_attendance_exists) {
            $query->whereBetween('date', [$yesterday, $yesterday]);
        }else{
            $query->whereBetween('date', [$date, $end_date]);
        }

        // Get the result
        return $query->orderBy('date', 'asc')->paginate($per_page);
    }
}
