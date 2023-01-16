<?php

namespace App\Services\Attendance\Schedule;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Group;
use App\Models\Shift;
use App\Models\Employee;
use App\Models\Schedule;
use Carbon\CarbonPeriod;
use App\Models\Attendance;

class ScheduleService
{

    public function getData($request)
    {
        $search = $request->search;
        $filter_branch = $request->filter_branch;
        $filter_month = $request->valid_month;
        $shift = $request->shift;


        if($filter_month){
            $year = Carbon::parse($filter_month)->format('Y');
            $month = Carbon::parse($filter_month)->format('m');
            $date = Carbon::create($year, $month, 1); 
            $daysInMonth = $date->daysInMonth;
        }else{
            $date =  Carbon::now();
            $daysInMonth = $date->daysInMonth;
            $year = Carbon::parse($date)->format('Y');
            $month = Carbon::parse($date)->format('m');
        }

        $head = ['Employee Name'];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $head[$i] = $i;
        }

        $requestedDate = Carbon::parse(Carbon::parse('01-' . $month . '-' . $year))->endOfMonth();

        $dataUser = User::with(['employee','schedules']);
        if($request->search || $request->filter_branch){
            if($shift){
                $dataUser->whereHas('schedules', function ($qs) use ($shift) {
                    if($shift == 'assign'){
                        $qs->where('is_leave',0);
                    }elseif ($shift == 'libur') {
                        $qs->where('is_leave',1);
                    }else{
                        $qs->where('shift_id',$shift);
                    }
                });
                if($filter_month){
                    $dataUser->whereHas('schedules', function ($qs) use ($month,$year) {
                            $qs->whereRaw('MONTH(schedules.date) = ?', [$month])->whereRaw('YEAR(schedules.date) = ?', [$year]);
                    });
                }
            }else{
                $dataUser->when(request('search', false), function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
        
                $dataUser->when(request('filter_branch', false), function ($q) use ($filter_branch) {
                    $q->whereHas('employee', function ($qu) use ($filter_branch) {
                        if ($filter_branch !== 'all') {
                            $qu->where('branch_id', $filter_branch);
                        }
                    });
                });
            }
        }else{
            if($shift){
                $dataUser->whereHas('schedules', function ($qs) use ($shift) {
                    if($shift == 'assign'){
                        $qs->where('is_leave',0);
                    }elseif ($shift == 'libur') {
                        $qs->where('is_leave',1);
                    }else{
                        $qs->where('shift_id',$shift);
                    }
                });
            }else{
                $dataUser = User::all();
            }
        }
       


        $users = $request->search ||  $request->filter_branch ? $dataUser->get() : $dataUser;



        $final = [];
        foreach ($users as $user) {

            $dataTillToday = array_fill(1, $date->copy()->format('d'), 'assign');

            $dataFromTomorrow = [];
            if (($date->copy()->addDay()->format('d') != $daysInMonth) && !$requestedDate->isPast()) {
                $dataFromTomorrow = array_fill($date->copy()->addDay()->format('d'), ($daysInMonth - $date->copy()->format('d')), 'assign');
            } else {
                $dataFromTomorrow = array_fill($date->copy()->addDay()->format('d'), ($daysInMonth - $date->copy()->format('d')), 'assign');
            }
            $final[$user->id]['date'] = array_replace($dataTillToday, $dataFromTomorrow);

            // Schedule Value 
            $schedules = Schedule::where('user_id', $user->id)->whereRaw('MONTH(schedules.date) = ?', [$month])->whereRaw('YEAR(schedules.date) = ?', [$year])->get();
            foreach ($schedules as $schedule) {
                if ($schedule->is_leave == 1) {
                    $status = 'libur';
                } else {
                    $status = $schedule->shift_detail->id;
                }
                if ($final[$user->id]['date'][Carbon::parse($schedule->date)->day] == 'assign' && $schedule->user_id == $user->id) {
                    $final[$user->id]['date'][Carbon::parse($schedule->date)->timezone(config('app.timezone'))->day] = $status;
                }
            }


            $emplolyeeName = $user->name;
            $final[$user->id]['name'] = $emplolyeeName;
        }

        $users['final'] = $final;
        $users['head'] = $head;
        return $users;
    }

    public function getReport($request)
    {
        $search = $request->search;
        $filter_branch = $request->filter_branch;
        $filter_month = $request->valid_month;
        $shift = $request->shift;
        
        if($filter_month){
            $year = Carbon::parse($filter_month)->format('Y');
            $month = Carbon::parse($filter_month)->format('m');
            $date = Carbon::create($year, $month, 1); 
            $daysInMonth = $date->daysInMonth;
        }else{
            $date =  Carbon::now();
            $daysInMonth = $date->daysInMonth;
            $year = Carbon::parse($date)->format('Y');
            $month = Carbon::parse($date)->format('m');
        }
        $requestedDate = Carbon::parse(Carbon::parse('01-' . $month . '-' . $year))->endOfMonth();


        $reportShift = [
            0 => 'libur'
        ];
        $shiftDropDown = Shift::where('branch_id', $filter_branch)->pluck('name', 'id')->toArray();
        foreach ($shiftDropDown as $key => $value) {
            $reportShift[$key] = $value;
        }


        $dataUser = User::with(['employee','schedules']);
        if($request->search || $request->filter_branch){
            if($shift){
                $dataUser->whereHas('schedules', function ($qs) use ($shift) {
                    if($shift == 'assign'){
                        $qs->where('is_leave',0);
                    }elseif ($shift == 'libur') {
                        $qs->where('is_leave',1);
                    }else{
                        $qs->where('shift_id',$shift);
                    }
                });
                if($filter_month){
                    $dataUser->whereHas('schedules', function ($qs) use ($month,$year) {
                            $qs->whereRaw('MONTH(schedules.date) = ?', [$month])->whereRaw('YEAR(schedules.date) = ?', [$year]);
                    });
                }
            }else{
                $dataUser->when(request('search', false), function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
        
                $dataUser->when(request('filter_branch', false), function ($q) use ($filter_branch) {
                    $q->whereHas('employee', function ($qu) use ($filter_branch) {
                        if ($filter_branch !== 'all') {
                            $qu->where('branch_id', $filter_branch);
                        }
                    });
                });
            }
        }else{
            if($shift){
                $dataUser->whereHas('schedules', function ($qs) use ($shift) {
                    if($shift == 'assign'){
                        $qs->where('is_leave',0);
                    }elseif ($shift == 'libur') {
                        $qs->where('is_leave',1);
                    }else{
                        $qs->where('shift_id',$shift);
                    }
                });
            }else{
                $dataUser = User::all();
            }
        }

        $headReport = ['Date'];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $headReport[$i] = $i;
        }

        $users = $request->search ||  $request->filter_branch ? $dataUser->get() : $dataUser;


        $totalDay = [];
        $totalCount = [];
        $finalCount = [];
        $dataUntilToday = array_fill(1, $date->copy()->format('d'), '0');

        $dataTomorrow = [];
        if (($date->copy()->addDay()->format('d') != $daysInMonth) && !$requestedDate->isPast()) {
            $dataTomorrow = array_fill($date->copy()->addDay()->format('d'), ($daysInMonth - $date->copy()->format('d')), '0');
        } else {
            $dataTomorrow = array_fill($date->copy()->addDay()->format('d'), ($daysInMonth - $date->copy()->format('d')), '0');
        }
        $totalDay[] = array_replace($dataUntilToday, $dataTomorrow);

        foreach ($reportShift as $key => $value) {
            $report[$value] = array_replace($dataUntilToday, $dataTomorrow);

            foreach ($totalDay[0] as $key2 => $value2) {
                // Calculate Date  
                $date = Carbon::createFromFormat('Y-m-d', $year . '-' . $month . '-' . $key2)->format('Y-m-d');

                if ($value == 'libur') {
                    $count = Schedule::where('date', $date)->whereIn('user_id', $users->pluck('id'))->where('is_leave', 1)->count();
                    $report['libur'][$key2] = $count;
                } else {
                    $count = Schedule::where('date', $date)->whereIn('user_id', $users->pluck('id'))->where('shift_id', $key)->count();
                    $report[$value][$key2] = $count;
                }
                $totalCount[][$key2] = $count;
            }
        }
        $scheduleReport = $report;

        foreach ($totalDay[0] as $key2 => $value2) {
            // Calculate Date  
            $date = Carbon::createFromFormat('Y-m-d', $year . '-' . $month . '-' . $key2)->format('Y-m-d');

            $finalCount[$key2] = array_sum(array_column($totalCount, $key2));
        }

        $final = [
            'total_count' => $finalCount,
            'headReport' => $headReport,
            'report' => $scheduleReport
        ];

        return $final;
    }

    public function getGroupByBranch($request)
    {
        $Group = Group::with(['employee_groups', 'employee_groups.employee'])->whereHas('employee_groups.employee', function ($q) use ($request) {
            $q->where('branch_id', $request);
        })->pluck('name', 'id');

        return $Group;
    }

    public function getEmployeeByBranch($request)
    {
        $employee = Employee::with(['user'])->where('branch_id', $request)->get()->pluck('user.name', 'user.id');

        return $employee;
    }

    public function storeBulkSchedule($request)
    {
        if ($request->type == 'Employee') {
            foreach ($request->employee_id as $employee) {
                $period = CarbonPeriod::create($request->start_date, $request->end_date);

                foreach ($period as $key => $value) {
                    $schedule = Schedule::where('user_id', $employee)->where('date', $value->format('Y-m-d'))->first();
                    $input = [
                        'user_id' => $employee,
                        'shift_id' => $request->shift_id == 'libur' ? null : $request->shift_id,
                        'is_leave' => $request->shift_id == 'libur' ? 1 : 0,
                        'date' => $value->format('Y-m-d'),
                    ];

                    $attendance = Attendance::where('date_clock', $value->format('Y-m-d'))->where('user_id', $employee)->first();

                    if (isset($schedule) && !isset($attendance)) {
                        $schedule->update($input);
                    } elseif (!isset($attendance)){
                        Schedule::create($input);
                    }
                }
            }
        } else {
            foreach ($request->group_id as $group_id) {
                $group = Group::find($group_id);
                $employees = $group->employee_groups->pluck('employee_id');
                foreach ($employees as $employee_id) {
                    $period = CarbonPeriod::create($request->start_date, $request->end_date);
                    $employee = Employee::find($employee_id)->user_id;

                    foreach ($period as $key => $value) {
                        $schedule = Schedule::where('user_id', $employee)->where('date', $value->format('Y-m-d'))->first();
                        $input = [
                            'user_id' => $employee,
                            'shift_id' => $request->shift_id == 'libur' ? null : $request->shift_id,
                            'is_leave' => $request->shift_id == 'libur' ? 1 : 0,
                            'date' => $value->format('Y-m-d'),
                        ];

                        $attendance = Attendance::where('date_clock', $value->format('Y-m-d'))->where('user_id', $employee)->first();

                        if (isset($schedule) && !isset($attendance)) {
                            $schedule->update($input);
                        } elseif (!isset($attendance)) {
                            Schedule::create($input);
                        }
                    }
                }
            }
        }
    }

    public function updateSchedule($user_id, $request)
    {
        $date = $request->date;
        $dateNow = Carbon::now()->format('Y-m-d');
        $input = [];

        if (Carbon::parse($date)->lt($dateNow)) {
            $output = [
                'success'  => false,
                'msg' => "Cant Update Schedule Before Today"
            ];
            return $output;
        }

        if ($request->type != 'libur' && $request->type != 'assign') {
            $input['shift_id'] = $request->type;
            $input['is_leave'] = 0;
        } else {
            $input['shift_id'] = null;
            $input['is_leave'] = 1;
        }

        $schedule = Schedule::where('user_id', $user_id)->where('date', $date)->first();
        $attendance = Attendance::where('user_id', $user_id)->where('date_clock', $date)->exists();

        if ($attendance) {
            throw new \Exception("Cant Update Schedule Because User Already Clockin", 400);
        }

        if ($request->type == 'assign' && $schedule != null) {
            Schedule::findOrFail($schedule->id)->delete();
        } elseif ($schedule != null) {
            Schedule::findOrFail($schedule->id)->update($input);
        } elseif ($schedule == null) {
            $input['date'] = $date;
            $input['user_id'] = $user_id;

            Schedule::create($input);
        }

        return $output = [
            'success'  => true,
            'msg' => "Success change schedule"
        ];;
    }
}
