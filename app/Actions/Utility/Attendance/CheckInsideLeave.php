<?php

namespace App\Actions\Utility\Attendance;

use Carbon\Carbon;
use App\Models\Leave;
use Carbon\CarbonPeriod;


class CheckInsideLeave
{
    public function handle($date, $employee)
    {
        $month = Carbon::parse($date)->format('m');
        $year = Carbon::parse($date)->format('Y');
        
        $leaves = Leave::where(function ($query) use ($month) {
            $query->whereMonth('start_date', $month)->orWhereMonth('end_date', $month);
        })->where(function ($query) use ($year) {
            $query->whereYear('start_date', $year)->orWhereYear('end_date', $year);
        })->where('status', 'approved')->where('employee_id', $employee->id)->get();

        $is_leave = false;
        foreach ($leaves as $leave) {
            $period = CarbonPeriod::create($leave->start_date, $leave->end_date);
            $period = collect($period)->map(function ($q) {
                return $q->format('Y-m-d');
            })->toArray();

            in_array($date, $period) ? $is_leave = true : $is_leave = false;
        }

        return $is_leave;
    }
}
