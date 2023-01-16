<?php

namespace App\Services\Employee\Employee;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Actions\Utility\PaginateCollection;
use App\Actions\Utility\Leave\GenerateLeavePeriod;
use App\Actions\Utility\Attendance\CheckStatusAttendance;
use App\Actions\Utility\Attendance\CalculateAttendanceStatus;
use App\Actions\Utility\Attendance\CalculateAttendanceWorkHours;

class AttendanceLogService
{
    public function getData($employee, $request)
    {
        $search = $request->search;
        $filter_date = $request->filter_date;
        $filter_status = $request->filter_status;
        $date = collect($filter_date)->map(function ($q) {
            return Carbon::parse($q)->format('Y-m-d');
        })->toArray();

        // Get company
        $query = Attendance::where('user_id', $employee->user_id);

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->where('date_clock', $search);
        });
        $query->when(request('filter_date', false), function ($q) use ($date) {
            $q->whereBetween('date_clock', $date);
        });

        // Get Status Checker Required Data
        $schedules =  Schedule::where('user_id', $employee->user_id)->whereBetween('date', $date)->get();
        $leaves = Leave::where('start_date', '<=', $date[0])->where('end_date', '>=', $date[1])->where('status', 'approved')->where('employee_id', $employee->id)->get();
        $generateLeavePeriod = new GenerateLeavePeriod();
        $leavePeriod = $generateLeavePeriod->handle($leaves);

        // Check Status Attendance
        $checkStatusAttendance = new CheckStatusAttendance();
        if($filter_status){
            $query = $query->get()->filter(function ($q) use ($checkStatusAttendance, $leavePeriod, $schedules, $filter_status){
                $schedule = collect($schedules)->where('date', $q->date_clock)->first();
                $status = $checkStatusAttendance->handle($q, $schedule, $leavePeriod, $q->date_clock);

                if($status !== '' && $status === $filter_status) {
                    return $q;
                }
            })->map(function ($q) use ($checkStatusAttendance, $leavePeriod, $schedules) {
                $schedule = collect($schedules)->where('date', $q->date_clock)->first();
                $status = $checkStatusAttendance->handle($q, $schedule, $leavePeriod, $q->date_clock);

                return [
                    'id' => $q->id,
                    'date_clock' => $q->date_clock,
                    'clock_in' => $q->clock_in,
                    'clock_out' => $q->clock_out,
                    'total_work_hours' => $q->total_work_hours,
                    'status' => $status,
                ];
            });
        }else{
            $query = $query->get()->map(function ($q) use ($checkStatusAttendance, $leavePeriod, $schedules) {
                $schedule = collect($schedules)->where('date', $q->date_clock)->first();
                $status = $checkStatusAttendance->handle($q, $schedule, $leavePeriod, $q->date_clock);

                return [
                    'id' => $q->id,
                    'date_clock' => $q->date_clock,
                    'clock_in' => $q->clock_in,
                    'clock_out' => $q->clock_out,
                    'total_work_hours' => $q->total_work_hours,
                    'status' => $status,
                ];
            });;
        }

        $paginate = new PaginateCollection();
        return $paginate->handle($query, 10);
    }

    public function getAttendanceLogOverview($employee, $request)
    {
        $dates = collect($request->filter_date)->map(function ($q) {
            return Carbon::parse($q)->format('Y-m-d');
        })->toArray();

        $attendances = Attendance::whereBetween('date_clock', $dates)->where('user_id', $employee->user_id)->get();
        $schedules = Schedule::where('user_id', $employee->user_id)->whereBetween('date', $dates)->where('is_leave', 0)->get();

        $attendance_status = new CalculateAttendanceStatus($attendances);
        $work_hours = new CalculateAttendanceWorkHours($attendances);
        $result = [
            'total_present' => $attendance_status->calculatePresentStatus(),
            'total_absent' => $attendance_status->calculateAbsentStatus($schedules),
            'total_late' => $attendance_status->calculateLateStatus(),
            'total_clockout_early' => $attendance_status->calculateClockoutEarlyStatus(),
            'present_work_hours' => $work_hours->calculatePresentWorkHours(),
            'late_work_hours' => $work_hours->calculateLateWorkHours(),
            'clockout_early_work_hours' => $work_hours->calculateClockoutEarlyWorkHours()
        ];

        return $result;
    }
}
