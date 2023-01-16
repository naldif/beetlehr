<?php

namespace App\Actions\Utility\Attendance;

use Carbon\Carbon;
use App\Models\BreakTime;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Helpers\Utility\Time;
use App\Models\BreakTimeOffline;
use App\Models\AttendanceOffline;
use App\Helpers\Utility\Authentication;
use App\Services\Api\V1\Attendance\BreakAttendanceService;
use App\Events\Attendance\AttendanceWithoutScheduleCreated;

class SubmitAttendance
{
    public function __construct()
    {
        $this->break_service = new BreakAttendanceService();
    }

    public function clockoutWithoutSchedule($attendance, $input, $date, $user_id, $is_offline = false)
    {
        // Clockout Existing Break
        $clock_in = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->date_clock . ' ' . $attendance->clock_in);
        $break_request = new Request();
        $break_request->replace([
            "date" => $input['date'],
            "clock" => $input['clock'],
            "type" => "clockout",
            "notes" =>  $input['notes'],
            "latitude" => $input['latitude'],
            "longitude" => $input['longitude'],
            "image_id" => $input['image_id'],
            "address" => $input['address'],
            "files" => !empty($input['files']) ? $input['files'] : []
        ]);
        $this->break_service->clockBreakAbnormal($break_request);

        // Calculate Work Hours
        $breaks = BreakTimeOffline::where('attendance_offline_log_id', $attendance->id)->get();
        $break_hours = collect($breaks)->map(function ($q) {
            return $q->total_work_hours;
        })->toArray();
        $total_attendance_work_hours = gmdate("H:i:s", $date->diffInSeconds($clock_in));
        $total_break_work_hours = Time::calculateTotalHours($break_hours);
        $final_attendance_work_hours = gmdate("H:i:s", Carbon::parse($total_attendance_work_hours)->diffInSeconds(Carbon::parse($total_break_work_hours)));

        $attendance->update([
            'user_id' => $user_id,
            'address_clock_out' => $input['address'],
            'clock_out' => $input['clock'],
            'latitude_clock_out' => $input['latitude'],
            'longitude_clock_out' => $input['longitude'],
            'notes_clock_out' => $input['notes'],
            'image_id_clock_out' => $input['image_id'],
            'total_work_hours' => $final_attendance_work_hours,
            'files_clock_out' => !empty($input['files']) ? $input['files'] : [],
            'is_offline_clock_out' => $is_offline
        ]);

        $employee = Authentication::getEmployeeLoggedIn();
        event(new AttendanceWithoutScheduleCreated($attendance, $employee));

        return $attendance;
    }

    public function clockinWithoutSchedule($input, $user_id, $is_offline = false)
    {
        $attendance = AttendanceOffline::create([
            'user_id' => $user_id,
            'status' => 'waiting',
            'type' => $input['status'],
            'clock_in' => $input['clock'],
            'date_clock' => $input['date'],
            'latitude_clock_in' => $input['latitude'],
            'longitude_clock_in' => $input['longitude'],
            'notes_clock_in' => $input['notes'],
            'image_id_clock_in' => $input['image_id'],
            'address_clock_in' => $input['address'],
            'total_work_hours' => '00:00:00',
            'files_clock_in' => !empty($input['files']) ? $input['files'] : [],
            'is_offline_clock_in' => $is_offline
        ]);

        return $attendance;
    }

    public function clockinWithSchedule($schedule, $input, $is_inside_radius, $user_id, $is_offline = false)
    {
        // Check Is Attendance Late
        $schedule_time =  Carbon::createFromFormat('Y-m-d H:i', $schedule->date . ' ' . $schedule->shift_detail->start_time)->format('Y-m-d H:i:s');
        $submit_time =  Carbon::createFromFormat('Y-m-d H:i:s', $input['date'] . ' ' . $input['clock'])->format('Y-m-d H:i:s');
        $submit_time > $schedule_time ? $is_late_clock = 1 : $is_late_clock = 0;

        if (!$is_inside_radius) {
            $is_outside_radius_clock_in = 1;
        } else {
            $is_outside_radius_clock_in = 0;
        }

        if ($is_late_clock === 1) {
            $time =  Carbon::parse($submit_time);
            $to = Carbon::parse($schedule_time);
            $total_late = gmdate("H:i:s", $time->diffInSeconds($to));
        } else {
            $total_late = "00:00:00";
        }

        $attendance = Attendance::create([
            'user_id' => $user_id,
            'status' => $input['status'],
            'clock_in' => $input['clock'],
            'date_clock' => Carbon::createFromFormat('Y-m-d H:i:s', $schedule->date . ' ' . $input['clock'])->format('Y-m-d'),
            'latitude_clock_in' => $input['latitude'],
            'longitude_clock_in' => $input['longitude'],
            'notes_clock_in' => $input['notes'],
            'image_id_clock_in' => $input['image_id'],
            'address_clock_in' => $input['address'],
            'is_outside_radius_clock_in' => $is_outside_radius_clock_in,
            'is_late_clock_in' => $is_late_clock,
            'total_late_clock_in' => $total_late,
            'files_clock_in' => !empty($input['files']) ? $input['files'] : [],
            'total_work_hours' => '00:00:00',
            'is_offline_clock_in' => $is_offline
        ]);

        return $attendance;
    }

    public function clockoutWithSchedule($schedule, $input, $is_inside_radius, $attendance, $is_offline = false)
    {
        // Check Is Attendance Early
        $schedule_start_time =  Carbon::createFromFormat('Y-m-d H:i', $schedule->date . ' ' . $schedule->shift_detail->start_time);
        $schedule_end_time =  Carbon::createFromFormat('Y-m-d H:i', $schedule->date . ' ' . $schedule->shift_detail->end_time);
        if ($schedule_start_time->format('A') == 'PM' && $schedule_end_time->format('A') == 'AM') {
            $schedule_time = $schedule_end_time->addDays(1)->format('Y-m-d H:i:s');
        } else {
            $schedule_time = $schedule_end_time->format('Y-m-d H:i:s');
        }

        $submit_time =  Carbon::createFromFormat('Y-m-d H:i:s', $input['date'] . ' ' . $input['clock'])->format('Y-m-d H:i:s');
        $submit_time < $schedule_time ? $is_early_clock_out = 1 : $is_early_clock_out = 0;
        
        if (!$is_inside_radius) {
            $is_outside_radius_clock_out = 1;
        } else {
            $is_outside_radius_clock_out = 0;
        }
        
        if ($is_early_clock_out === 1) {
            $time = Carbon::parse($submit_time);
            $to = Carbon::parse($schedule_time);
            $total_early_clock_out = gmdate("H:i:s", $time->diffInSeconds($to));
        } else {
            $total_early_clock_out = "00:00:00";
        }
        
        // Clockout Existing Break
        $break_request = new Request();
        $break_request->replace([
            "date" => $input['date'],
            "clock" => $input['clock'],
            "type" => "clockout",
            "notes" =>  $input['notes'],
            "latitude" => $input['latitude'],
            "longitude" => $input['longitude'],
            "image_id" => $input['image_id'],
            "address" => $input['address'],
            "files" => !empty($input['files']) ? $input['files'] : []
        ]);
        $this->break_service->clockBreak($break_request);
        
        // Calculate Work Hours
        $clock_in = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->date_clock . ' ' . $attendance->clock_in);
        $breaks = BreakTime::where('attendance_id', $attendance->id)->get();
        $break_hours = collect($breaks)->map(function ($q) {
            return $q->total_work_hours;
        })->toArray();
        $total_attendance_work_hours = gmdate("H:i:s", Carbon::parse($submit_time)->diffInSeconds($clock_in));
        $total_break_work_hours = Time::calculateTotalHours($break_hours);
        $final_attendance_work_hours = gmdate("H:i:s", Carbon::parse($total_attendance_work_hours)->diffInSeconds(Carbon::parse($total_break_work_hours)));
        
        $attendance->update([
            'clock_out' => $input['clock'],
            'latitude_clock_out' => $input['latitude'],
            'longitude_clock_out' => $input['longitude'],
            'notes_clock_out' => $input['notes'],
            'image_id_clock_out' => $input['image_id'],
            'address_clock_out' => $input['address'],
            'is_outside_radius_clock_out' => $is_outside_radius_clock_out,
            'is_early_clock_out' => $is_early_clock_out,
            'total_early_clock_out' => $total_early_clock_out,
            'files_clock_out' => !empty($input['files']) ? $input['files'] : [],
            'total_work_hours' => $final_attendance_work_hours,
            'is_offline_clock_out' => $is_offline
        ]);

        return $attendance;
    }
}
