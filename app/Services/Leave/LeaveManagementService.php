<?php

namespace App\Services\Leave;

use App\Events\Leave\LeaveWasDeleted;
use App\Models\Leave;
use App\Models\Employee;
use Carbon\CarbonPeriod;
use App\Models\LeaveType;
use App\Models\LeaveQuota;
use App\Services\FileService;

class LeaveManagementService 
{
    public function getData($request)
    {
        $search = $request->search;
        $filter_branch = $request->filter_branch;

        // Get company
        $query = Leave::whereHas('employee', function($q) use ($filter_branch) {
            $q->where('branch_id', $filter_branch);
        })->with(['employee_detail', 'leave_type_detail']);

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->whereHas('employee', function ($qu) use ($search) {
                $qu->whereHas('user_detail', function ($qe) use ($search) {
                    $qe->where('name', 'like', '%' . $search . '%');
                });
            });
        });
        $query->when(request('filter_branch', false), function ($q) use ($filter_branch) {
            $q->whereHas('employee_detail', function ($qu) use ($filter_branch) {
                $qu->where('branch_id', $filter_branch);
            });
        });

        return $query->paginate(10);
    }  

    public function getLeaveTypeOptions($request)
    {   
        $employee = Employee::findOrFail($request->employee_id);
        $chooses = LeaveType::where('branch_id', $employee->branch_id)->get()->pluck('name', 'id');
        return $chooses;
    }

    public function createData($request)
    {
        $inputs = $request->only(['employee_id', 'leave_type_id', 'reason', 'start_date', 'end_date']);
        $inputs['status'] = 'waiting';

        $period = CarbonPeriod::create($inputs['start_date'], $inputs['end_date']);
        $totalDay = $period->toArray();
        $quota = LeaveQuota::where('employee_id', $request->employee_id)->where('leave_type_id', $request->leave_type_id)->first();

        if ($quota === null || (isset($quota) && $quota->quota === 0)) {
            throw new \Exception("You did not set quota yet or employee quota now are 0. Please check again", 400);
        } else if ($quota->quota - count($totalDay) < 0) {
            throw new \Exception("The remaining quota is not enough. Update quota first", 400);
        }

        // Check if leave in requested date range already exists
        $leaveExists = Leave::where('start_date', '<=', $inputs['start_date'])->where('end_date', '>=', $inputs['end_date'])->where('employee_id', $inputs['employee_id'])->where('status', 'approved')->exists();

        if($leaveExists) {
            throw new \Exception("Employee already have submission in this date range", 400);
        }

        // Upload File
        $fileService = new FileService();
        $file = $fileService->uploadFile($request->file('file'));
        $inputs['file'] = $file->id;

        $leave = Leave::create($inputs);
        return $leave;
    }

    public function deleteData($id)
    {
        $leave = Leave::findOrFail($id);
        
        // Call Leave Deleted Event
        event(new LeaveWasDeleted($leave));
        
        $leave->delete();
        return $leave;
    }

    public function updateStatus($id, $request)
    {
        if($request->action === 'approve') {
            $this->approve($id);
        }else{
            $this->reject($id, $request);
        }
    }

    public function approve($id)
    {
        $leave = Leave::find($id);
        $period = CarbonPeriod::create($leave->start_date, $leave->end_date);
        $totalDay = $period->toArray();

        $quota = LeaveQuota::where('employee_id', $leave->employee_id)->where('leave_type_id', $leave->leave_type_id)->first();

        if ($quota === null || (isset($quota) && $quota->quota === 0)) {
            throw new \Exception("You did not set quota yet or employee quota now are 0. Please check again", 400);
        } else if ($quota->quota - count($totalDay) < 0) {
            throw new \Exception("The remaining quota is not enough. Update quota first", 400);
        }

        $quota->update([
            'quota' => $quota->quota - count($totalDay)
        ]);

        $leave->update([
            'status' => 'approved'
        ]);

        return $leave;
    }

    public function reject($id, $request)
    {
        $leave = Leave::find($id);
        $leave->update([
            'status' => 'rejected',
            'reject_reason' => $request->reject_reason
        ]);

        return $leave;
    }
}
