<?php

namespace App\Services\Api\V1\Attendance;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Setting;
use App\Models\Schedule;
use Carbon\CarbonPeriod;
use App\Models\Attendance;
use App\Models\LeaveQuota;
use Illuminate\Http\Request;
use App\Helpers\Utility\Time;
use App\Services\FileService;
use App\Actions\Utility\GetFile;
use App\Models\BreakTimeOffline;
use App\Models\AttendanceOffline;
use App\Helpers\Utility\Localization;
use App\Helpers\Utility\Authentication;
use App\Actions\Utility\Leave\GenerateLeavePeriod;
use App\Helpers\Utility\Attendance\BreakAttendance;
use App\Actions\Utility\Attendance\SubmitAttendance;
use App\Actions\Utility\Attendance\CheckStatusAttendance;
use App\Services\Api\V1\Attendance\BreakAttendanceService;
use App\Actions\Utility\Attendance\CalculateAttendanceStatus;
use App\Actions\Utility\Attendance\CheckAttendanceClockedToday;
use App\Actions\Utility\Attendance\CalculateAttendanceWorkHours;

class AttendanceService
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
    
    public function getAttendanceLogData($request)
    {
        $today = Carbon::now();
        $month = $request->month ?: $today->format('m');
        $year = $request->year ?: $today->format('Y');
        $filter_status = $request->status;

        // Generate date 
        $start_of_date = Carbon::createFromDate($year, $month, 1);
        $days_in_month = $start_of_date->daysInMonth;
        $end_of_date = $month === $today->format('m') ? $today : Carbon::createFromDate($year, $month, $days_in_month);
        $date_periods = CarbonPeriod::create($start_of_date->format('Y-m-d'), $end_of_date->format('Y-m-d'));
        $date_periods = collect($date_periods)->map(function($q) {
            return $q->format('Y-m-d');
        });

        // Query requirement data
        $employee = Authentication::getEmployeeLoggedIn();
        $attendances = Attendance::with(['user_detail'])->whereMonth('date_clock', $month)->whereYear('date_clock', $year)->where('user_id', $employee->user_id)->get();
        $schedules =  Schedule::with(['shift_detail'])->whereMonth('date', $month)->whereYear('date', $year)->where('user_id', $employee->user_id)->get();
        $leaves = Leave::where(function ($query) use ($month) {
            $query->whereMonth('start_date', $month)->orWhereMonth('end_date', $month);
        })->where(function ($query) use ($year) {
            $query->whereYear('start_date', $year)->orWhereYear('end_date', $year);
        })->where('status', 'approved')->where('employee_id', $employee->id)->get();
        $generateLeavePeriod = new GenerateLeavePeriod();
        $leavePeriod = $generateLeavePeriod->handle(collect($leaves)->where('employee_id', $employee->id)->all());

        $attendance_list = [];
        $checkStatusAttendance = new CheckStatusAttendance();

        foreach ($date_periods as $key => $value) {
            // Get Requirement data to get status attendance
            $attendance = collect($attendances)->where('user_id', $employee->user_id)->where('date_clock', $value)->first();
            $schedule = collect($schedules)->where('user_id', $employee->user_id)->where('date', $value)->first();
            $status = $checkStatusAttendance->handle($attendance, $schedule, $leavePeriod, $value);
            $timezone = $employee->branch_detail->timezone;

            if(isset($attendance)) {
                $data = [
                    'date' => Localization::convertTimeToUserBranch($attendance->date_clock, $timezone)->format('Y-m-d'),
                    'date_gmt' => Localization::convertTimeToUTC($attendance->date_clock)->format('Y-m-d'),
                    'clock_in' => Localization::convertTimeToUserBranch($attendance->clock_in, $timezone)->format('H:i:s'),
                    'clock_in_gmt' => Localization::convertTimeToUTC($attendance->clock_in)->format('H:i:s'),
                    'clock_out' => $attendance->clock_out ? Localization::convertTimeToUserBranch($attendance->clock_out, $timezone)->format('H:i:s') : null,
                    'clock_out_gmt' => $attendance->clock_out ? Localization::convertTimeToUTC($attendance->clock_out)->format('H:i:s') : null,
                    'work_hours' => $attendance->total_work_hours,
                    'type' => 'normal',
                    'status' => $status,
                    'is_force_clock_out' => $attendance->is_force_clock_out
                ];
            }else{
                $data = [
                    'date' => Localization::convertTimeToUserBranch($value, $timezone)->format('Y-m-d'),
                    'date_gmt' => Localization::convertTimeToUTC($value)->format('Y-m-d'),
                    'clock_in' => null,
                    'clock_in_gmt' => null,
                    'clock_out' => null,
                    'clock_out_gmt' => null,
                    'work_hours' => null,
                    'type' => 'normal',
                    'status' => $status,
                    'is_force_clock_out' => false
                ];
            }
            array_push($attendance_list, $data);
        }

        // Filter By Status
        if ($filter_status) {
            return collect($attendance_list)->filter(function ($q) use ($filter_status) {
                return $q['status'] == $filter_status;
            })->values()->toArray();
        }

        return $attendance_list;
    }

    public function getAttendanceOverviewData($request)
    {
        $filter_date = $request->date ?: Carbon::now();

        $month = Carbon::parse($filter_date)->format('m');
        $year = Carbon::parse($filter_date)->format('Y');
        $employee = Authentication::getEmployeeLoggedIn();
        $attendances = Attendance::whereMonth('date_clock', $month)->whereYear('date_clock', $year)->where('user_id', $employee->user_id)->get();
        $schedules = Schedule::with(['shift_detail'])->where('user_id', $employee->user_id)->whereMonth('date', $month)->whereYear('date', $year)->get();
        $leaves = Leave::where('employee_id', $employee->id)->where(function ($query) use ($month) {
            $query->whereMonth('start_date', $month)->orWhereMonth('end_date', $month);
        })->where(function ($query) use ($year) {
            $query->whereYear('start_date', $year)->orWhereYear('end_date', $year);
        })->where('status', 'approved')->with(['leave_type_detail'])->get();
        $remaining_leave_quota = LeaveQuota::where('employee_id', $employee->id)->get()->sum('quota');

        $attendance_status = new CalculateAttendanceStatus($attendances);
        $attendance_work = new CalculateAttendanceWorkHours($attendances);
        $result = [
            'present' => $attendance_status->calculatePresentStatus(),
            'ontime' => $attendance_status->calculateOntimeStatus(),
            'clockout_early' => $attendance_status->calculateClockoutEarlyStatus(),
            'late' => $attendance_status->calculateLateStatus(),
            'absent' =>  $attendance_status->calculateAbsentStatus(collect($schedules)->where('is_leave', 0)->all()),
            'holiday' => $attendance_status->calculateHolidayStatus($schedules),
            'total_work_hours' => $attendance_work->calculateAttendanceTotalWorkHours(),
            'total_late_hours' => $attendance_work->calculateLateWorkHours(),
            'total_early_hours' => $attendance_work->calculateClockoutEarlyWorkHours(),
            'total_absent_hours' => $attendance_work->calculateAbsentWorkHours(collect($schedules)->where('is_leave', 0)->all()),
            'total_leaves' => $attendance_status->calculateLeaveStatus($leaves),
            'leaves' => collect($leaves)->map(function ($q) {
                $leave_periods =  CarbonPeriod::create($q->start_date, $q->end_date);
                return [
                    'id' => $q->id,
                    'name' => $q->leave_type_detail->name,
                    'total' => count($leave_periods)
                ];
            }),
            'total_remaining_leaves' => $remaining_leave_quota
        ];

        return $result;
    }

    public function getAttendanceDetailData($date, $request, $status)
    {
        $employee = Authentication::getEmployeeLoggedIn();
        $per_page = $request->per_page ? $request->per_page : 10;
        $sort_by = $request->sort_by ? $request->sort_by : 'created_at';
        $order_by = $request->order_by ? $request->order_by : 'asc';

        if($status['message_type']) {
            $query = AttendanceOffline::query();
            
            return $query->where('date_clock', $date)->where('user_id', $employee->user_id)->where('status', '!=', 'cancelled')->orderBy($sort_by, $order_by)->paginate($per_page);
        }else {
            $query = Attendance::query();

            return $query->where('date_clock', $date)->where('user_id', $employee->user_id)->orderBy($sort_by, $order_by)->paginate($per_page);
        }
    }

    public function uploadImage($request)
    {
        $file_service = new FileService();
        $file = $file_service->uploadFile($request->file('image'));

        return $file;
    }

    public function checkLocation($request)
    {
        $employee = Authentication::getEmployeeLoggedIn();

        $finalResp = Localization::getDataFromLatLong($request->latitude, $request->longitude);
        $calculate = Localization::calculateDistanceLocation($request->latitude, $request->longitude, $employee);

        return [
            'address' => $finalResp->display_name,
            'placement_latitude' => $calculate['placement_latitude'],
            'placement_longitude' => $calculate['placement_longitude'],
            'max_radius' => $calculate['placement_radius'],
            'status' => $request->status,
            'distance' => floor($calculate['meters']),
            'accepted' => $calculate['meters'] <= $calculate['placement_radius'] ? true : false
        ];
    }

    public function checkAttendanceBeforeClock($request)
    {
        $employee = Authentication::getEmployeeLoggedIn();
        $user_id = $employee->user_id;

        if (config('app.env') !== 'production') {
            $date = Carbon::parse($request->date)->format('Y-m-d');
            $clock = Carbon::parse($request->clock)->format('H:i:s');
            $input_date = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $clock);
        } else {
            $input_date = Carbon::now();
        }

        $yesterday = Carbon::parse($input_date)->subDays(1)->format('Y-m-d');

        // Query data
        $schedule = Schedule::where('user_id', $user_id)->where('date', $input_date->format('Y-m-d'))->first();
        $yesterdaySchedule = Schedule::where('user_id', $user_id)->where('date', $yesterday)->where('is_leave', 0)->first();
        $yesterdayAttendance = Attendance::where('date_clock', $yesterday)->where('user_id', $user_id)->first();
        $todayAttendance = Attendance::where('date_clock', $input_date->format('Y-m-d'))->where('user_id', $user_id)->first();

        if (isset($yesterdaySchedule)) {
            $yesterday_shift_time_end = Carbon::createFromFormat('H:i', $yesterdaySchedule->shift_detail->end_time);
            $yesterday_clock_out_tolerance = Carbon::parse($yesterday_shift_time_end)->addMinutes(config('payroll_settings.tolerance_clock_out'))->format('H:i');
        }

        $is_late = false;
        if ((isset($yesterdaySchedule) ? $yesterdaySchedule->shift_detail->is_night_shift == 1 && $input_date->format('H:i:s') <= $yesterdaySchedule->shift_detail->end_time : false) && !isset($yesterdayAttendance)) {
            $scheduleCheck =  Carbon::createFromFormat('Y-m-d H:i', $yesterdaySchedule->date . ' ' . $yesterdaySchedule->shift_detail->start_time)->format('Y-m-d H:i:s');
            $timeConverted =  Carbon::parse($input_date)->format('Y-m-d H:i:s');
            $timeConverted > $scheduleCheck ? $is_late = true : $is_late = false;
        } elseif ((isset($yesterdaySchedule) ? $yesterdaySchedule->shift_detail->is_night_shift == 1 && $input_date->format('H:i') <= $yesterday_clock_out_tolerance : false) && isset($yesterdayAttendance) ? $yesterdayAttendance->clock_out == null : false) {
            $scheduleCheck =  Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($yesterdaySchedule->date)->addDays(1)->format('Y-m-d') . ' ' . $yesterdaySchedule->shift_detail->end_time)->format('Y-m-d H:i:s');
            $timeConverted =  Carbon::parse($input_date)->format('Y-m-d H:i:s');
            $timeConverted < $scheduleCheck ? $is_late = true : $is_late = false;
        } else {
            if (isset($schedule) && !isset($todayAttendance)) {
                $scheduleCheck =  Carbon::createFromFormat('Y-m-d H:i', $schedule->date . ' ' . $schedule->shift_detail->start_time)->format('Y-m-d H:i:s');
                $timeConverted =  Carbon::parse($input_date)->format('Y-m-d H:i:s');
                $timeConverted > $scheduleCheck ? $is_late = true : $is_late = false;
            } elseif (isset($schedule) && isset($todayAttendance) ? $todayAttendance->clock_out == null : false) {
                $scheduleStartCheck =  Carbon::createFromFormat('Y-m-d H:i', $schedule->date . ' ' . $schedule->shift_detail->start_time)->format('A');
                $scheduleEndCheck =  Carbon::createFromFormat('Y-m-d H:i', $schedule->date . ' ' . $schedule->shift_detail->end_time)->format('A');
                if ($scheduleStartCheck == 'PM' && $scheduleEndCheck == 'AM') {
                    $scheduleCheck =  Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($schedule->date)->addDays(1)->format('Y-m-d') . ' ' . $schedule->shift_detail->end_time)->format('Y-m-d H:i:s');
                    $timeConverted =  Carbon::parse($input_date)->format('Y-m-d H:i:s');
                    $timeConverted < $scheduleCheck ? $is_late = true : $is_late = false;
                } else {
                    $scheduleCheck =  Carbon::createFromFormat('Y-m-d H:i', $schedule->date . ' ' . $schedule->shift_detail->end_time)->format('Y-m-d H:i:s');
                    $timeConverted =  Carbon::parse($input_date)->format('Y-m-d H:i:s');
                    $timeConverted < $scheduleCheck ? $is_late = true : $is_late = false;
                }
            }
        }

        $branch_latitude = $employee->branch_detail->latitude;
        $branch_longitude = $employee->branch_detail->longitude;
        $branch_radius = $employee->branch_detail->radius;
        $is_accepted_location = Localization::checkIsInsideRadius($request->latitude, $request->longitude, $branch_latitude, $branch_longitude, $branch_radius);

        return [
            'is_late' => $is_late,
            'accepted_location' => $is_accepted_location,
            'accepted_image' => true,
            'status' => $request->status,
        ];
    }

    public function attendanceClock($request)
    {
        // Required Init Data
        $employee = Authentication::getEmployeeLoggedIn();
        $submit_attendance = new SubmitAttendance();
        $user_id = $employee->user_id;
        $branch = $employee->branch_detail;
        $input = $request->only(['notes', 'latitude', 'longitude', 'image_id', 'address', 'status', 'files']);

        if (config('app.env') !== 'production') {
            $date = Carbon::parse($request->date)->format('Y-m-d');
            $clock = Carbon::parse($request->clock)->format('H:i:s');
            $input_date = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $clock);
        } else {
            $input_date = Carbon::now();
        }

        $input['date'] = $input_date->format('Y-m-d');
        $input['clock'] = $input_date->format('H:i:s');
        $yesterday = Carbon::parse($input_date)->subDays(1)->format('Y-m-d');

        // Query Check
        $schedule = Schedule::where('user_id', $user_id)->where('date', $input_date->format('Y-m-d'))->first();
        $yesterday_schedule = Schedule::where('user_id', $user_id)->where('date', $yesterday)->where('is_leave', 0)->first();
        $yesterday_attendance = Attendance::where('date_clock', $yesterday)->where('user_id', $user_id)->first();
        $today_attendance = Attendance::where('date_clock', $input_date->format('Y-m-d'))->where('user_id', $user_id)->first();
        $yesterday_attendance_offline = AttendanceOffline::where('date_clock', $yesterday)->where('user_id', $user_id)->where('status', '!=', 'cancelled')->first();
        $today_attendance_offline = AttendanceOffline::where('date_clock', $input_date->format('Y-m-d'))->where('user_id', $user_id)->where('status', '!=', 'cancelled')->first();
        $is_inside_radius = Localization::checkIsInsideRadius($request->latitude, $request->longitude, $branch->latitude, $branch->longitude, $branch->radius);

        if (isset($yesterday_schedule)) {
            $yesterday_shift_time_end = Carbon::parse($yesterday_schedule->shift_detail->end_time);
            $yesterday_clock_out_tolerance = Carbon::parse($yesterday_shift_time_end)->addMinutes($this->settings['tolerance_clock_out'])->format('H:i');
        }

        // Validate Attendance
        $check_attendance_status = new CheckAttendanceClockedToday();
        $status = $check_attendance_status->handle($request);
        if(!$status['accepted']) {
            throw new \Exception($status['messageValidation'], $status['status']); 
        }

        // List All Type Attendance 
        $yesterday_clockout_without_schedule = !isset($yesterday_schedule) && (isset($yesterday_attendance_offline) ? $yesterday_attendance_offline->clock_out === null : false);
        $today_clockin_without_schedule = !isset($schedule) && !isset($today_attendance_offline);
        $today_clockout_without_schedule = !isset($schedule) && (isset($today_attendance_offline) ? $today_attendance_offline->clock_out === null : false);
        $yesterday_clockin = (isset($yesterday_schedule) ? $yesterday_schedule->shift_detail->is_night_shift === 1 && $input_date->format('H:i:s') <= $yesterday_schedule->shift_detail->end_time : false) && !isset($yesterday_attendance);
        $yesterday_clockout = (isset($yesterday_schedule) ? $yesterday_schedule->shift_detail->is_night_shift === 1  && $input_date->format('H:i') <= $yesterday_clock_out_tolerance : false) && (isset($yesterday_attendance) ? $yesterday_attendance->clock_out === null : false);
        $today_clockin = isset($schedule) && !isset($today_attendance);
        $today_clockout = isset($schedule) && isset($today_attendance) ? $today_attendance->clock_out === null : false;

        if($yesterday_clockout_without_schedule) {
            $type = 'clockout';
            $result_attendance = $submit_attendance->clockoutWithoutSchedule($yesterday_attendance_offline, $input, $input_date, $user_id, true);
        } elseif ($today_clockin_without_schedule) {
            $type = 'clockin';
            $result_attendance = $submit_attendance->clockinWithoutSchedule($input, $user_id, true);
        } elseif ($today_clockout_without_schedule) {
            $type = 'clockout';
            $result_attendance = $submit_attendance->clockoutWithoutSchedule($today_attendance_offline, $input, $input_date, $user_id, true);
        } elseif ($yesterday_clockin) {
            $type = 'clockin';
            $result_attendance = $submit_attendance->clockinWithSchedule($yesterday_schedule, $input, $is_inside_radius, $user_id);
        } elseif ($yesterday_clockout) {
            $type = 'clockout';
            $result_attendance = $submit_attendance->clockoutWithSchedule($yesterday_schedule, $input, $is_inside_radius, $yesterday_attendance);
        } elseif ($today_clockin) {
            $type = 'clockin';
            $result_attendance = $submit_attendance->clockinWithSchedule($schedule, $input, $is_inside_radius, $user_id);
        } elseif ($today_clockout) {
            $type = 'clockout';
            $result_attendance = $submit_attendance->clockoutWithSchedule($schedule, $input, $is_inside_radius, $today_attendance);
        }

        $file_service = new FileService();
        $file = $file_service->getFileById($input['image_id']);
        $get_multiple_file = new GetFile();

        return [
            'id' => $result_attendance->id,
            'date' => Carbon::parse($input_date)->timezone($employee->branch_detail->timezone)->format('Y-m-d'),
            'date_gmt' => $input_date->format('Y-m-d'),
            'clock' => Carbon::parse($input_date)->timezone($employee->branch_detail->timezone)->format('H:i:s'),
            'clock_gmt' => $input_date->format('H:i:s'),
            'type' => $type,
            'status' => $input['status'],
            'latitude' => $input['latitude'],
            'longitude' => $input['longitude'],
            'address' => $input['address'],
            'image' => [
                'id' => $file->id,
                'url' =>  $file->full_path,
                'file_name' => $file->file_name,
                'extension' => $file->extension,
                'size' => $file->size
            ],
            'files' => $get_multiple_file->handle($input['files']),
            'notes' => $input['notes'],
            'is_late' => isset($schedule) && $result_attendance->is_late_clock_in === 1 ? true : false,
            'clockout_duration' => '00:00:00',
            'clockout_tolerance_duration' => '00:00:00',
            'tracker_interval' => '00:00:00',
            'tracker_configuration' => [
                'is_enabled' => false,
                'tracker_endpoint' => '-'
            ]
        ];
    }

    public function syncAttendanceOffline($request)
    {
        $employee = Authentication::getEmployeeLoggedIn();
        $submit_attendance = new SubmitAttendance();
        $user_id = $employee->user_id;
        $branch = $employee->branch_detail;
        
        foreach ($request->all() as $value) {
            // Required Init Data
            $input = collect($value)->only(['clock', 'type', 'notes', 'latitude', 'longitude', 'image_id', 'date', 'status', 'files']);
            $date = Carbon::parse($input['date'])->format('Y-m-d');
            $clock = Carbon::parse($input['clock'])->format('H:i:s');
            $input_date = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $clock);
            $input['date'] = $input_date->format('Y-m-d');
            $input['clock'] = $input_date->format('H:i:s');
            $input['address'] = Localization::getDataFromLatLong($input['latitude'], $input['longitude'])->display_name;
            $yesterday = Carbon::parse($input_date)->subDays(1)->format('Y-m-d');

            // Query Check
            $schedule = Schedule::where('user_id', $user_id)->where('date', $input_date->format('Y-m-d'))->first();
            $yesterday_schedule = Schedule::where('user_id', $user_id)->where('date', $yesterday)->where('is_leave', 0)->first();
            $yesterday_attendance = Attendance::where('date_clock', $yesterday)->where('user_id', $user_id)->first();
            $today_attendance = Attendance::where('date_clock', $input_date->format('Y-m-d'))->where('user_id', $user_id)->first();
            $yesterday_attendance_offline = AttendanceOffline::where('date_clock', $yesterday)->where('user_id', $user_id)->where('status', '!=', 'cancelled')->first();
            $today_attendance_offline = AttendanceOffline::where('date_clock', $input_date->format('Y-m-d'))->where('user_id', $user_id)->where('status', '!=', 'cancelled')->first();
            $is_inside_radius = Localization::checkIsInsideRadius($input['latitude'], $input['longitude'], $branch->latitude, $branch->longitude, $branch->radius);

            if (isset($yesterday_schedule)) {
                $yesterday_shift_time_end = Carbon::parse($yesterday_schedule->shift_detail->end_time);
                $yesterday_clock_out_tolerance = Carbon::parse($yesterday_shift_time_end)->addMinutes($this->settings['tolerance_clock_out'])->format('H:i');
            }

            // List All Type Attendance 
            $yesterday_clockout_without_schedule = !isset($yesterday_schedule) && (isset($yesterday_attendance_offline) ? $yesterday_attendance_offline->clock_out === null : false) && ($input['type'] === 'clockout' || $input['type'] === 'out');
            $today_clockin_without_schedule = !isset($schedule) && !isset($today_attendance_offline) && ($input['type'] === 'clockin' || $input['type'] === 'in');
            $today_clockout_without_schedule = !isset($schedule) && (isset($today_attendance_offline) ? $today_attendance_offline->clock_out === null : false) && ($input['type'] === 'clockout' || $input['type'] === 'out');
            $yesterday_clockin = (isset($yesterday_schedule) ? $yesterday_schedule->shift_detail->is_night_shift === 1 && $input_date->format('H:i:s') <= $yesterday_schedule->shift_detail->end_time : false) && !isset($yesterday_attendance) && ($input['type'] === 'clockin' || $input['type'] === 'in');
            $yesterday_clockout = (isset($yesterday_schedule) ? $yesterday_schedule->shift_detail->is_night_shift === 1  && $input_date->format('H:i') <= $yesterday_clock_out_tolerance : false) && (isset($yesterday_attendance) ? $yesterday_attendance->clock_out === null : false) && ($input['type'] === 'clockout' || $input['type'] === 'out');
            $today_clockin = isset($schedule) && !isset($today_attendance) && ($input['type'] === 'clockin' || $input['type'] === 'in');
            $today_clockout = isset($schedule) && isset($today_attendance) ? $today_attendance->clock_out === null : false && ($input['type'] === 'clockout' || $input['type'] === 'out');

            if ($yesterday_clockout_without_schedule) {
                $submit_attendance->clockoutWithoutSchedule($yesterday_attendance_offline, $input, $input_date, $user_id, true);
            } elseif ($today_clockin_without_schedule) {
                $submit_attendance->clockinWithoutSchedule($input, $user_id, true);
            } elseif ($today_clockout_without_schedule) {
                $submit_attendance->clockoutWithoutSchedule($today_attendance_offline, $input, $input_date, $user_id, true);
            } elseif ($yesterday_clockin) {
                $submit_attendance->clockinWithSchedule($yesterday_schedule, $input, $is_inside_radius, $user_id, true);
            } elseif ($yesterday_clockout) {
                $submit_attendance->clockoutWithSchedule($yesterday_schedule, $input, $is_inside_radius, $yesterday_attendance, true);
            } elseif ($today_clockin) {
                $submit_attendance->clockinWithSchedule($schedule, $input, $is_inside_radius, $user_id, true);
            } elseif ($today_clockout) {
                $submit_attendance->clockoutWithSchedule($schedule, $input, $is_inside_radius, $today_attendance, true);
            }
        }
    }

    public function cancelAttendanceOffline()
    {
        $date = Carbon::now()->format('Y-m-d');
        $yesterday = Carbon::now()->subDays(1)->format('Y-m-d');
        $employee = Authentication::getEmployeeLoggedIn();
        $user_id = $employee->user_id;

        $offlineAttendance = AttendanceOffline::whereBetween('date_clock', [$yesterday, $date])->where('user_id', $user_id)->where('status', 'waiting')->latest()->first();

        if(!$offlineAttendance) {
            throw new \Exception('Doesnt have any offline attendance today', 400);
        }

        $offlineAttendance->update([
            'status' => 'cancelled'
        ]);
    }
}
