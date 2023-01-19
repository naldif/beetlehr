<?php

namespace App\Services\Api\V1\Leave;

use stdClass;
use App\Models\Leave;
use Carbon\CarbonPeriod;
use App\Models\LeaveType;
use App\Models\LeaveQuota;
use App\Services\FileService;
use App\Events\Leave\LeaveWasCreated;
use App\Helpers\Utility\Authentication;

class LeaveService
{
    public function getLeaveQuota()
    {
        $employee = Authentication::getEmployeeLoggedIn();
        $total_quota = LeaveType::where('branch_id', $employee->branch_id)->get()->sum('quota');
        $query = LeaveQuota::with(['leave_type'])->where('employee_id', $employee->id)->whereHas('leave_type', function ($query) use ($employee) {
            $query->where('branch_id', $employee->branch_id);
        })->get();

        $data = new stdClass;
        $data->total_quota = $total_quota;
        $data->total_quota_remaining = collect($query)->sum('quota');
        $data->leave_quotas = collect($query)->map(function ($q) {
            return [
                'type_id' => $q->leave_type_id,
                'name' => $q->leave_type->name,
                'remaining' => $q->quota,
                'total' => $q->leave_type->quota
            ];
        });

        return $data;
    }

    public function getLeaveType($request)
    {
        // Required Init Data
        $per_page = $request->per_page ? $request->per_page : 10;
        $employee = Authentication::getEmployeeLoggedIn();

        $query = LeaveType::where('branch_id', $employee->branch_id)->paginate($per_page);

        return $query;
    }

    public function getLeave($request)
    {
        // Required Init Data
        $employee = Authentication::getEmployeeLoggedIn();
        $per_page = $request->per_page ?: 10;
        $status = $request->status;
        $type = $request->type;

        $query = Leave::with(['leave_type_detail'])->where('employee_id', $employee->id);

        // Filter By Params
        $query->when(request('status',false), function ($q) use ($status) {
            return $q->where('status', $status);
        });
        $query->when(request('type', false), function ($q) use ($type) {
            if ($type === 'inprocess') {
                return $q->where('status', 'waiting');
            } elseif ($type === 'complete') {
                return $q->whereIn('status', ['approved', 'cancelled', 'rejected']);
            }
        });

        return $query->paginate($per_page);
    }

    public function getLeaveDetail($id)
    {
        $leave = Leave::with(['leave_type_detail'])->findOrFail($id);
        return $leave;
    }

    public function createLeave($request)
    {
        $employee = Authentication::getEmployeeLoggedIn();
        $inputs = $request->only(['start_date', 'end_date', 'leave_type_id', 'reason']);

        $period = CarbonPeriod::create($request->start_date, $request->end_date);
        $total_day = $period->toArray();

        $quota = LeaveQuota::where('employee_id', $employee->id)->where('leave_type_id', $request->leave_type_id)->first();
        if ($quota === null || (isset($quota) && $quota->quota === 0)) {
            throw new \Exception("You did not set quota yet or employee quota now are 0. Please check again", 400);
        } else if ($quota->quota - count($total_day) < 0) {
            throw new \Exception("The remaining quota is not enough. Update quota first", 400);
        }

        $leaveExists = Leave::where('start_date', '<=', $request->start_date)->where('end_date', '>=', $request->end_date)->where('employee_id',$employee->id)->where('status', 'approved')->exists();
        if ($leaveExists) {
            throw new \Exception("Employee already have submission in this date range", 400);
        }

        // Upload File
        $file_service = new FileService();
        $file = $file_service->uploadFile($request->file('file'));

        $inputs['employee_id'] = $employee->id;
        $inputs['status'] = 'waiting';
        $inputs['file'] = $file->id;
        $leave = Leave::create($inputs);

        // Call Leave Created Event
        event(new LeaveWasCreated($leave, $employee, count($total_day)));

        return $leave;
    }

    public function cancelLeave($id)
    {
        $leave = $this->getLeaveDetail($id);
        if ($leave->status != 'waiting') {
            throw new \Exception("Your leave has been approved or rejected. Cant be cancelled", 400);
        }

        $leave->update([
            'status' => 'cancelled'
        ]);

        return $leave;
    }
}
