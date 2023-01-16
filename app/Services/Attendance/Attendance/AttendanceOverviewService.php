<?php

namespace App\Services\Attendance\Attendance;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\Schedule;
use App\Models\BreakTime;
use App\Models\Attendance;
use App\Helpers\Utility\Time;
use App\Actions\Utility\GetFile;
use App\Actions\Utility\Leave\GenerateLeavePeriod;
use App\Actions\Utility\Attendance\CheckStatusAttendance;
use App\Actions\Utility\Attendance\CalculateAttendanceStatus;

class AttendanceOverviewService
{
    public function getAttendanceOverview($request)
    {
        $filter_date = $request->filter_date ?: Carbon::now();
        $filter_branch = $request->filter_branch ?: 1;
        
        $month = Carbon::parse($filter_date)->format('m');
        $year = Carbon::parse($filter_date)->format('Y');
        $employees = Employee::where('branch_id', $filter_branch)->get();
        $attendances = Attendance::whereMonth('date_clock', $month)->whereYear('date_clock', $year)->whereIn('user_id', $employees->pluck('user_id'))->get();
        $schedules = Schedule::whereIn('user_id', $employees->pluck('user_id'))->whereMonth('date', $month)->whereYear('date', $year)->get();
        $leaves = Leave::whereIn('employee_id', $employees->pluck('id'))->where(function ($query) use ($month) {
                    $query->whereMonth('start_date', $month)->orWhereMonth('end_date', $month);
                })->where(function ($query) use ($year) {
                    $query->whereYear('start_date', $year)->orWhereYear('end_date', $year);
                })->where('status', 'approved')->get();

        $attendance_status = new CalculateAttendanceStatus($attendances);
        $result = [
            'total_present' => $attendance_status->calculatePresentStatus(),
            'total_absent' => $attendance_status->calculateAbsentStatus(collect($schedules)->where('is_leave', 0)->all()),
            'total_late' => $attendance_status->calculateLateStatus(),
            'total_clockout_early' => $attendance_status->calculateClockoutEarlyStatus(),
            'total_leaves' => $attendance_status->calculateLeaveStatus($leaves),
            'total_holiday' => $attendance_status->calculateHolidayStatus($schedules)
        ];

        return $result;
    }

    public function getAttendanceListDate($request)
    {
        $filter_date = $request->filter_date ?: Carbon::now();

        // Get Header Table
        $month = Carbon::parse($filter_date)->format('m');
        $year = Carbon::parse($filter_date)->format('Y');
        $listDateByMonth = Carbon::createFromFormat('Y-m-d', $year . '-' . $month . '-' . 1);
        $daysInMonth = $listDateByMonth->daysInMonth;
        $attendanceHeader = ['Employee Name'];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $attendanceHeader[$i] = $i;
        }

        return $attendanceHeader;
    }

    public function getAttendanceList($request)
    {
        $filter_date = $request->filter_date ?: Carbon::now();
        $filter_branch = $request->filter_branch ?: 1;

        // Generate date 
        $month = Carbon::parse($filter_date)->format('m');
        $year = Carbon::parse($filter_date)->format('Y');
        $current_month = Carbon::now()->format('m');
        $current_year = Carbon::now()->format('Y');
        $current_day = Carbon::now()->format('d');
        $listDateByMonth = Carbon::createFromFormat('Y-m-d', $year . '-' . $month . '-' . 1);
        $daysInMonth = $listDateByMonth->daysInMonth;
        $listDate = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $listDate[$i] = Carbon::createFromFormat('Y-m-d', $year . '-' . $month . '-' . $i)->format('Y-m-d');
        }

        // Query requirement data
        $employees = Employee::where('branch_id', $filter_branch)->get();
        $attendances = Attendance::with(['user_detail'])->whereMonth('date_clock', $month)->whereYear('date_clock', $year)->whereIn('user_id', $employees->pluck('user_id'))->get();
        $schedules =  Schedule::with(['shift_detail'])->whereMonth('date', $month)->whereYear('date', $year)->whereIn('user_id', $employees->pluck('user_id'))->get();
        $leaves = Leave::where(function ($query) use ($month) {
            $query->whereMonth('start_date', $month)->orWhereMonth('end_date', $month);
        })->where(function ($query) use ($year) {
            $query->whereYear('start_date', $year)->orWhereYear('end_date', $year);
        })->where('status', 'approved')->whereIn('employee_id', $employees->pluck('id'))->get();
        $breaks = BreakTime::whereHas('attendance', function ($q) use ($month, $year) {
            $q->whereMonth('date_clock', $month)->whereYear('date_clock', $year);
        })->get();
       
        $attendance_list = [];
        $checkStatusAttendance = new CheckStatusAttendance();
        $getFile = new GetFile();

        foreach ($employees as $employee) {
            $data = [
                'employee_name' => $employee->user_detail->name
            ];

            foreach ($listDate as $key => $value) {
                // Get Requirement data to get status attendance
                $generateLeavePeriod = new GenerateLeavePeriod();
                $leavePeriod = $generateLeavePeriod->handle(collect($leaves)->where('employee_id', $employee->id)->all());
                $attendance = collect($attendances)->where('user_id', $employee->user_id)->where('date_clock', $value)->first();
                $schedule = collect($schedules)->where('user_id', $employee->user_id)->where('date', $value)->first();
                $status = $checkStatusAttendance->handle($attendance, $schedule, $leavePeriod, $value);

                if($current_month === $month && $current_year === $year && $key > $current_day) {
                    $data['attendances'][$key] = ['id' => null, 'status' => ($status === 'leave' || $status === 'holiday') ? $status : 'netral'];
                }else{
                    if(in_array($status, ['unassigned', 'holiday', 'absent', 'netral', 'leave'])) {
                        $data['attendances'][$key] = ['id' => null, 'status' => $status];
                    }else{
                        $data['attendances'][$key] = [
                            'id' => $attendance->id, 
                            'date' => Carbon::parse($attendance->date_clock)->format('d F Y'),
                            'user_name' => $attendance->user_detail->name,
                            'status' => $status,
                            'status_formatted' => ucfirst($status),
                            'is_force_clock_out' => $attendance->is_force_clock_out ? 'Yes' : 'No',
                            'clock_in' => Carbon::parse($attendance->clock_in)->format('H:i'),
                            'clock_out' => $attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('H:i') : '-',
                            'schedule_start' => isset($schedule) && $schedule->shift_detail ? $schedule->shift_detail->start_time : '-',
                            'schedule_end' =>  isset($schedule) && $schedule->shift_detail ? $schedule->shift_detail->end_time : '-',
                            'total_late' => $attendance->is_late_clock_in ? $attendance->total_late_clock_in : '-',
                            'total_clock_out_early' => $attendance->is_early_clock_out ? $attendance->total_early_clock_out : '-',
                            'total_break_hours' => Time::calculateTotalHours(collect($breaks)->where('attendance_id', $attendance->id)->pluck('total_work_hours')),
                            'total_work_hours' => $attendance->total_work_hours,
                            'outside_radius_clock_in' => $attendance->is_outside_radius_clock_in ? 'Yes' : 'No',
                            'note_clock_in' => $attendance->notes_clock_in ?: '-',
                            'address_clock_in' => $attendance->address_clock_in,
                            'map_address_clock_in' => 'https://maps.google.com/?q='.$attendance->latitude_clock_in.','.$attendance->longitude_clock_in,
                            'clock_in_image' => $getFile->handle($attendance->image_id_clock_in)->full_path,
                            'files_clock_in' => $getFile->handle($attendance->files_clock_in),
                            'outside_radius_clock_out' => $attendance->is_outside_radius_clock_out ? 'Yes' : 'No',
                            'note_clock_out' => $attendance->notes_clock_out ?: '-',
                            'address_clock_out' => $attendance->address_clock_out ?: '-',
                            'map_address_clock_out' => 'https://maps.google.com/?q=' . $attendance->latitude_clock_out . ',' . $attendance->longitude_clock_out,
                            'clock_out_image' => $attendance->image_id_clock_out ? $getFile->handle($attendance->image_id_clock_out)->full_path : '-',
                            'files_clock_out' => $attendance->files_clock_out ? $getFile->handle($attendance->files_clock_out) : '-'
                        ];
                    }
                }
            }

            array_push($attendance_list, $data);
        }

        return $attendance_list;
    }

    public function getAttendanceRecap($request)
    {
        $filter_date = $request->filter_date ?: Carbon::now();
        $filter_branch = $request->filter_branch ?: 1;
        $recap_lists = ['present', 'late', 'absent', 'clockout_early', 'leave', 'holiday'];

        // Generate date 
        $month = Carbon::parse($filter_date)->format('m');
        $year = Carbon::parse($filter_date)->format('Y');
        $listDateByMonth = Carbon::createFromFormat('Y-m-d', $year . '-' . $month . '-' . 1);
        $daysInMonth = $listDateByMonth->daysInMonth;
        $listDate = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $listDate[$i] = Carbon::createFromFormat('Y-m-d', $year . '-' . $month . '-' . $i)->format('Y-m-d');
        }

        // Query requirement data
        $employees = Employee::where('branch_id', $filter_branch)->get();
        $attendances = Attendance::whereMonth('date_clock', $month)->whereYear('date_clock', $year)->whereIn('user_id', $employees->pluck('user_id'))->get();
        $schedules =  Schedule::whereMonth('date', $month)->whereYear('date', $year)->whereIn('user_id', $employees->pluck('user_id'))->get();
        $leaves = Leave::where(function ($query) use ($month) {
            $query->whereMonth('start_date', $month)->orWhereMonth('end_date', $month);
        })->where(function ($query) use ($year) {
            $query->whereYear('start_date', $year)->orWhereYear('end_date', $year);
        })->where('status', 'approved')->whereIn('employee_id', $employees->pluck('id'))->get();

        $attendance_recaps = [];

        foreach ($recap_lists as $key => $value) {
            $data = [
                'status' => $value
            ];

            foreach ($listDate as $dateKey => $dateValue) {
                $attendance = collect($attendances)->where('date_clock', $dateValue)->all();
                $attendance_status = new CalculateAttendanceStatus($attendance);

                switch ($value) {
                    case 'present':
                        $data['recaps'][$dateKey] = ['total_recap' => $attendance_status->calculatePresentStatus()];
                        break;

                    case 'late':
                        $data['recaps'][$dateKey] = ['total_recap' => $attendance_status->calculateLateStatus()];
                        break;

                    case 'absent':
                        $schedule = collect($schedules)->where('is_leave', 0)->where('date', $dateValue)->all();
                        $data['recaps'][$dateKey] = ['total_recap' => $attendance_status->calculateAbsentStatus($schedule)];
                        break;

                    case 'clockout_early':
                        $data['recaps'][$dateKey] = ['total_recap' => $attendance_status->calculateClockoutEarlyStatus()];
                        break;

                    case 'leave':
                        $leave = collect($leaves)->where('start_date', '<=', $dateValue)->where('end_date', '>=', $dateValue)->count();
                        $data['recaps'][$dateKey] = ['total_recap' => $leave];
                        break;

                    case 'holiday':
                        $schedule = collect($schedules)->where('date', $dateValue)->all();
                        $data['recaps'][$dateKey] = ['total_recap' => $attendance_status->calculateHolidayStatus($schedule)];
                        break;
                    
                    default:
                        $data['recaps'][$dateKey] = ['total_recap' => 0];
                        break;
                }
            }

            array_push($attendance_recaps, $data);
        }

        return $attendance_recaps;
    }
}
