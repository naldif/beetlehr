<?php

namespace App\Services\Api\V1\Approval;

use App\Models\Approval;
use App\Helpers\Utility\Authentication;
use App\Actions\Utility\Approval\LeaveApprovalAction;

class ApprovalService
{
    public function getData($request)
    {
        // Init Default Query Params for Query
        $employee = Authentication::getEmployeeLoggedIn();
        $approver_id = $employee ? $employee->id : null;
        $request_type = $request->request_type;
        $start_time = $request->start_time;
        $end_time = $request->end_time;
        $employees = explode(',', $request->employees);
        $status = $request->status;
        $per_page = $request->per_page ? $request->per_page : 10;
        $order_by = $request->order_by ? $request->order_by : 'asc';

        // Get Approval
        $query = Approval::with(['approval_steps', 'requester_detail'])
        ->whereHas('approval_steps', function ($q) use ($approver_id, $status) {
            $q->where('approver_id', $approver_id)->whereNotIn('status', ['pending', 'void']);

            // Filter by status
            $q->when(request('status', false), function ($a) use ($status) {
                $a->where('status', $status);
            });
        })->whereIn('type', ['create_leave']);

        // Filter by existing params
        $query->when(request('employees', false), function ($q) use ($employees) {
            $q->whereIn('requester_id', $employees);
        });
        $query->when(request('request_type', false), function ($q) use ($request_type) {
            if($request_type === 'time_off'){
                $q->where('type', 'create_leave');
            }else{
                $q->where('type', $request_type);
            }
        });
        $query->when(request('start_time',false), function ($q) use ($start_time) {
            $q->whereDate('requested_at', '>=', $start_time);
        });
        $query->when(request('end_time', false), function ($q) use ($end_time) {
            $q->whereDate('requested_at', '<=', $end_time);
        }); 

        return $query->orderBy('requested_at', $order_by)->paginate($per_page);
    }

    public function getDetailApproval($id)
    {
        $approval = Approval::with(['approval_steps', 'requester_detail'])->find($id);
        return $approval;
    }

    public function approveApproval($id, $request)
    {
        $approval = $this->getDetailApproval($id);

        if ($request->type === 'time_off') {
            $leaveApprovalAction = new LeaveApprovalAction();
            return $leaveApprovalAction->approveLeaveRequest($request, $approval);
        }
    }

    public function rejectApproval($id, $request)
    {
        $approval = $this->getDetailApproval($id);

        if ($request->type === 'time_off') {
            $leaveApprovalAction = new LeaveApprovalAction();
            return $leaveApprovalAction->rejectLeaveRequest($request, $approval);
        }
    }
}
