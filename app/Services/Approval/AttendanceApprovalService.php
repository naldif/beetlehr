<?php

namespace App\Services\Approval;

use App\Models\Attendance;
use App\Models\ApprovalRuleLevel;
use App\Actions\Utility\Approval\AssignApprovalData;
use App\Actions\Utility\Approval\AttendanceApprovalAction;


class AttendanceApprovalService
{

    public function __construct()
    {
        $this->attendanceApprovalAction = new AttendanceApprovalAction();
        $this->assignApprovalData = new AssignApprovalData();
    }

    public function assignApprover($data)
    {
        // Check If Attendance Need Approval 
        $approvableAttendance = $this->attendanceApprovalAction->checkApprovableAttendance($data->requester);

        // Update status to approved if result false
        if (!$approvableAttendance) {
            $data->attendance->update([
                'status' => 'approved',
            ]);

            $this->attendanceApprovalAction->createAttendanceApproved($data->attendance);
        }

        // Get All Approvers and Insert Approval data to DB | Upprove if doesnt have approver or insert it to db
        $listApprovers = ApprovalRuleLevel::whereHas('approval_rule', function($q) use ($data){
            $q->where('branch_id', $data->requester->branch_id)->whereHas('approval_type', function($qu) {
                $qu->where('type', 'attendance_without_schedule');
            });
        })->orderBy('level_approval', 'asc')->get();
        
        if (count($listApprovers) > 0 && $listApprovers->first()->approver_type === 'reports_to' && $data->requester->manager == null) {
            $data->attendance->update([
                'status' => 'approved',
            ]);

            $this->attendanceApprovalAction->createAttendanceApproved($data->attendance);
        }

        $meta_data = [
            'date' => $data->attendance->date_clock,
            'clock_in' => $data->attendance->clock_in,
            'clock_out' => $data->attendance->clock_out,
            'address_clock_in' => $data->attendance->address_clock_in,
            'address_clock_out' => $data->attendance->address_clock_out,
            'notes_clock_in' => $data->attendance->notes_clock_in,
            'notes_clock_out' => $data->attendance->notes_clock_out,
            'total_work_hours' => $data->attendance->total_work_hours
        ];

        $this->assignApprovalData->handle($listApprovers, $data, $meta_data, $data->attendance->id);
    }
}
