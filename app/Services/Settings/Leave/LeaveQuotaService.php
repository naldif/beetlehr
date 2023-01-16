<?php

namespace App\Services\Settings\Leave;

use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\LeaveQuota;

class LeaveQuotaService
{
    public function getData($request)
    {
        $search = $request->search;
        $filter_branch = $request->filter_branch;

        $query = LeaveQuota::whereHas('employee')->with(['employee_detail', 'leave_type_detail']);

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->whereHas('leave_type_detail', function ($qu) use ($search) {
                $qu->where('name', 'like', '%' . $search . '%');
            });
        });
        $query->when(request('filter_branch', false), function ($q) use ($filter_branch) {
            $q->whereHas('employee', function ($qu) use ($filter_branch) {
                $qu->where('branch_id', $filter_branch);
            });
        });

        return $query->paginate(10);
    }

    public function getLeaveTypeOptions($request)
    {
        $type = [];
        $chooses = LeaveType::where('branch_id', $request->branch_id)->get();

        foreach ($chooses as $choose) {
            $type[$choose->id] = $choose->name . ' - max ' . $choose->quota . ' Quota';
        }
        return $type;
    }

    public function storeData($request)
    {
        $input = $request->only(['leave_type_id', 'quota']);

        // Checking available quota 
        $leavetype = LeaveType::findOrFail($request->leave_type_id);
        if ($request->quota > $leavetype->quota) {
            throw new \Exception("Cant input quota more than base type quota", 400);
        }

        // Get Employee Base Branch
        $employees = Employee::where('branch_id', $request->branch_id)->get();
        collect($employees)->each(function ($employee) use ($input) {
            $input['employee_id'] = $employee->id;
            LeaveQuota::updateOrCreate([
                'employee_id' => $employee->id,
                'leave_type_id' => $input['leave_type_id'],
            ], $input);
        });

        return true;
    }

    public function updateData($id, $request)
    {
        $input = $request->only(['quota']);
        $leaveQuota = LeaveQuota::findOrFail($id);

        // Checking available quota 
        $leavetype = LeaveType::findOrFail($leaveQuota->leave_type_id);
        if ($request->quota > $leavetype->quota) {
            throw new \Exception("Cant input quota more than base type quota", 400);
        }

        $leaveQuota->update($input);

        return $leaveQuota;
    }

    public function deleteData($id)
    {
        $leaveQuota = LeaveQuota::findOrFail($id);
        $leaveQuota->delete();

        return $leaveQuota;
    }
}
