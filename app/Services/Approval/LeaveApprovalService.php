<?php

namespace App\Services\Approval;

use App\Models\Approval;
use App\Models\LeaveQuota;
use App\Models\ApprovalRuleLevel;
use App\Actions\Utility\Approval\AssignApprovalData;
use App\Actions\Utility\Approval\LeaveApprovalAction;


class LeaveApprovalService
{

    public function __construct()
    {
        $this->leaveApprovalAction = new LeaveApprovalAction();
        $this->assignApprovalData = new AssignApprovalData();
    }

    public function assignApprover($data)
    {
        // Check If Leave Need Approval 
        $approvableLeave = $this->leaveApprovalAction->checkApprovableLeave($data->requester);

        // Update status leave to approved if approvableLeave false
        if (!$approvableLeave) {
            $quota = LeaveQuota::where('employee_id', $data->leave->employee_id)->where('leave_type_id', $data->leave->leave_type_id)->first();
            $quota->update([
                'quota' => $quota->quota - $data->leave_duration
            ]);

            return $data->leave->update([
                'status' => 'approved',
            ]);
        }

        // Get All Approvers and Insert Approval data to DB | Upprove if doesnt have approver or insert it to db
        $listApprovers = ApprovalRuleLevel::whereHas('approval_rule', function($q) use ($data){
            $q->where('branch_id', $data->requester->branch_id)->whereHas('approval_type', function ($qu) {
                $qu->where('type', 'create_leave');
            });;
        })->orderBy('level_approval', 'asc')->get();
        
        if (count($listApprovers) > 0 && $listApprovers->first()->approver_type === 'reports_to' && $data->requester->manager == null) {
            return $data->leave->update([
                'status' => 'approved',
            ]);
        }

        $meta_data = [
            'reason' => $data->leave->reason,
            'duration' => $data->leave_duration,
            'end_date' => $data->leave->end_date,
            'start_date' => $data->leave->start_date
        ];

        $this->assignApprovalData->handle($listApprovers, $data, $meta_data, $data->leave->id);
    }

    public function deleteRelatedApproval($data)
    {
        // Get Approval Leave and Delete it
        Approval::where('type', 'create_leave')->where('reference_id', $data->leave->id)->delete();
    }
}
