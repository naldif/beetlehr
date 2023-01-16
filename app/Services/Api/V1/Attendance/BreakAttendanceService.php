<?php

namespace App\Services\Api\V1\Attendance;

use Carbon\Carbon;
use App\Models\BreakTime;
use App\Models\BreakTimeOffline;
use App\Helpers\Utility\Localization;
use App\Helpers\Utility\Authentication;
use App\Helpers\Utility\Attendance\BreakAttendance;

class BreakAttendanceService
{
    public function clockBreakAbnormal($request)
    {
        // Get Current Attendance
        $attendance = BreakAttendance::getAttendanceForBreakAbnormal($request);

        // Select Clock Time
        if (config('app.env') !== 'production') {
            $clock_time = Carbon::parse($request->clock)->format('H:i:s');
        } else {
            $clock_time =  Carbon::now()->format('H:i:s');
        }

        // Store data base on type 
        if ($request->type === 'start') {
            $input = [
                'attendance_offline_log_id' => $attendance->id,
                'start_time' => $clock_time,
                'total_work_hours' => '00:00:00',
                'latitude_start_break' => $request->latitude,
                'longitude_start_break' => $request->longitude,
                'address_start_break' => $request->address,
                'note_start_break' => $request->notes,
                'image_id_start_break' => $request->image_id,
                'files_start_break' => $request->input('files.*')
            ];

            $break = BreakTimeOffline::where('attendance_offline_log_id', $attendance->id)->latest()->first();

            if (!$break || (isset($break) && $break->end_time !== null)) {
                $break = BreakTimeOffline::create($input);
            }
        } elseif ($request->type === 'end') {
            $break = BreakTimeOffline::where('attendance_offline_log_id', $attendance->id)->where('end_time', null)->where('start_time', '!=', null)->latest()->first();

            if (!isset($break)) {
                throw new \Exception('Cant find active break. Cant end break now');
            }

            $total_break_hours = BreakAttendance::calculateTotalWorkHours($break->start_time, $clock_time);

            $input = [
                'attendance_offline_log_id' => $attendance->id,
                'end_time' => $clock_time,
                'total_work_hours' => $total_break_hours,
                'latitude_end_break' => $request->latitude,
                'longitude_end_break' => $request->longitude,
                'address_end_break' => $request->address,
                'note_end_break' => $request->notes,
                'image_id_end_break' => $request->image_id,
                'files_end_break' => $request->input('files.*')
            ];

            $break->update($input);
        } elseif ($request->type === 'clockout') {
            $break = BreakTimeOffline::where('attendance_offline_log_id', $attendance->id)->where('end_time', null)->where('start_time', '!=', null)->latest()->first();

            if (isset($break)) {
                $total_break_hours = BreakAttendance::calculateTotalWorkHours($break->start_time, $clock_time);

                $input = [
                    'attendance_offline_log_id' => $attendance->id,
                    'end_time' => $clock_time,
                    'total_work_hours' => $total_break_hours,
                    'latitude_end_break' => $request->latitude,
                    'longitude_end_break' => $request->longitude,
                    'address_end_break' => $request->address,
                    'note_end_break' => $request->notes,
                    'image_id_end_break' => $request->image_id,
                    'files_end_break' => $request->input('files.*')
                ];

                $break->update($input);
            }
        } else {
            throw new \Exception('Please input a valid type to access this request');
        }

        return $break;
    }

    public function clockBreak($request)
    {
        // Get Current Attendance
        $attendance = BreakAttendance::getAttendanceForBreak($request);

        // Select Clock Time
        if (config('app.env') !== 'production') {
            $clock_time = Carbon::parse($request->clock)->format('H:i:s');
        } else {
            $clock_time =  Carbon::now()->format('H:i:s');
        }

        // Store data base on type 
        if ($request->type === 'start') {
            $input = [
                'attendance_id' => $attendance->id,
                'start_time' => $clock_time,
                'total_work_hours' => '00:00:00',
                'latitude_start_break' => $request->latitude,
                'longitude_start_break' => $request->longitude,
                'address_start_break' => $request->address,
                'note_start_break' => $request->notes,
                'image_id_start_break' => $request->image_id,
                'files_start_break' => $request->input('files.*')
            ];

            $break = BreakTime::where('attendance_id', $attendance->id)->latest()->first();
            if (!$break || (isset($break) && $break->end_time !== null)) {
                $break = BreakTime::create($input);
            }
        } elseif ($request->type === 'end') {
            $break = BreakTime::where('attendance_id', $attendance->id)->where('end_time', null)->where('start_time', '!=', null)->latest()->first();

            if (!isset($break)) {
                throw new \Exception('Cant find active break. Cant end break now');
            }

            $total_break_hours = BreakAttendance::calculateTotalWorkHours($break->start_time, $clock_time);

            $input = [
                'attendance_id' => $attendance->id,
                'end_time' => $clock_time,
                'total_work_hours' => $total_break_hours,
                'latitude_end_break' => $request->latitude,
                'longitude_end_break' => $request->longitude,
                'address_end_break' => $request->address,
                'note_end_break' => $request->notes,
                'image_id_end_break' => $request->image_id,
                'files_end_break' => $request->input('files.*')
            ];

            $break->update($input);
        } elseif ($request->type === 'clockout') {
            $break = BreakTime::where('attendance_id', $attendance->id)->where('end_time', null)->where('start_time', '!=', null)->latest()->first();

            if (isset($break)) {
                $total_break_hours = BreakAttendance::calculateTotalWorkHours($break->start_time, $clock_time);

                $input = [
                    'attendance_id' => $attendance->id,
                    'end_time' => $clock_time,
                    'total_work_hours' => $total_break_hours,
                    'latitude_end_break' => $request->latitude,
                    'longitude_end_break' => $request->longitude,
                    'address_end_break' => $request->address,
                    'note_end_break' => $request->notes,
                    'image_id_end_break' => $request->image_id,
                    'files_end_break' => $request->input('files.*')
                ];

                $break->update($input);
            }
        } else {
            throw new \Exception('Please input a valid type to access this request');
        }

        return $break;
    }

    public function checkLocation($request)
    {
        // Get Required Data
        $employee = Authentication::getEmployeeLoggedIn();

        // Call Each Helper
        $final_resp = Localization::getDataFromLatLong($request->latitude, $request->longitude);
        $calculate = Localization::calculateDistanceLocation($request->latitude, $request->longitude, $employee);

        // Response
        return [
            'address' => $final_resp->display_name,
            'branch_latitude' => $calculate['placement_latitude'],
            'branch_longitude' => $calculate['placement_longitude'],
            'max_radius' => $calculate['placement_radius'],
            'distance' => floor($calculate['meters'])
        ];
    }
}
