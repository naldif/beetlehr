<?php

namespace App\Services\Approval;

use App\Models\Approval;
use App\Models\Employee;
use App\Actions\Utility\Approval\LeaveApprovalAction;
use App\Actions\Utility\Approval\AttendanceApprovalAction;

class ApprovalService
{
    public function getData($request)
    {
        $filter_status = $request->filter_status;
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->first();
        $approver_id = $employee ? $employee->id : null;

        // Get Approval
        $query = Approval::with(['approval_steps'])
        ->whereHas('approval_steps', function ($q) use ($approver_id, $filter_status) {
            $q->where('approver_id', $approver_id)->whereNotIn('status', ['pending', 'void']);

            // Filter by status
            $q->when(request('status', false), function ($a) use ($filter_status) {
                $a->where('status', $filter_status);
            });
        });

        return $query->paginate(10);
    }

    public function getDetailApproval($id)
    {
        $approval = Approval::with(['approval_steps'])->find($id);
        return $approval;
    }

    public function approveApproval($id, $request)
    {
        $approval = $this->getDetailApproval($id);

        if ($approval->type === 'create_leave') {
            $leaveApprovalAction = new LeaveApprovalAction();
            return $leaveApprovalAction->approveLeaveRequest($request, $approval);
        } elseif ($approval->type === 'attendance_without_schedule') {
            $attendanceApprovalAction = new AttendanceApprovalAction();
            return $attendanceApprovalAction->approveAttendanceRequest($request, $approval);
        }
    }

    public function rejectApproval($id, $request)
    {
        $approval = $this->getDetailApproval($id);

        if ($approval->type === 'create_leave') {
            $leaveApprovalAction = new LeaveApprovalAction();
            return $leaveApprovalAction->rejectLeaveRequest($request, $approval);
        } elseif ($approval->type === 'attendance_without_schedule') {
            $attendanceApprovalAction = new AttendanceApprovalAction();
            return $attendanceApprovalAction->rejectAttendanceRequest($request, $approval);
        }
    }
}
