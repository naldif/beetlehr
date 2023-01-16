<?php

namespace App\Actions\Utility\Approval;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Employee;
use Carbon\CarbonPeriod;
use App\Models\LeaveQuota;
use App\Models\ApprovalRule;
use App\Helpers\Utility\Localization;
use App\Helpers\Utility\Authentication;


class LeaveApprovalAction
{
    public function getLeaveApprovementStatus($data)
    {
        // Order Leave Approvement By Level
        $leaveApprovements = $data->sortBy('level')->all();

        // Formatting Approvement Result
        $result = [];
        foreach ($leaveApprovements as $approvement) {
            $isStatusApproved = $approvement->status === 'approved';
            $isStatusRejected = $approvement->status === 'rejected';
            $data = [
                'user_id' => $approvement->approver['user_id'],
                'approver_name' => $approvement->approver['name'],
                'approver_image' => $approvement->approver['image'],
                'designation' => $approvement->approver['designation'],
                'status' => $approvement->status,
                'status_label' => ucfirst($approvement->status),
                'reason' => $isStatusApproved ? $approvement->approved_reason : ($isStatusRejected ? $approvement->rejected_reason : null),
                'timestamp' => $approvement->done_at ? Localization::convertTimeToUserBranch($approvement->done_at, $approvement->approver['timezone'])->format('Y-m-d H:i:s') : null,
                'timestamp_gmt' => $approvement->done_at ? Localization::convertTimeToUTC($approvement->done_at)->format('Y-m-d H:i:s') : null
            ];

            $result[] = $data;
        }

        return $result;
    }

    public function checkLeaveApproverStatus($data, $approver)
    {
        if ($approver->level !== 1) {
            // Get Approver Before Current Level
            $prevLevel = $approver->level - 1;
            $prevApprover = $data->where('level', $prevLevel)->first();

            return ($prevApprover->status === 'awaiting' || $prevApprover === 'pending') ? false : ($approver->status === 'awaiting' ? true : false);
        }

        // Return Action Base on Status
        return $approver->status === 'awaiting' ? true : false;
    }

    public function approveLeaveRequest($request, $data)
    {
        // Order Leave Approvement By Level
        $leaveApprovements = $data->approval_steps->sortBy('level');

        // Count total level and get current approver
        $totalLevel = count($leaveApprovements);
        $user = Authentication::getUserLoggedIn();
        $approver_id = Employee::where('user_id', $user->id)->first()->id;
        $approver = $leaveApprovements->where('approver_id', $approver_id)->first();
        $current = array_search($approver->id, array_column($leaveApprovements->toArray(), 'id'));
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        if ($approver->level === $totalLevel) {
            // Update status and data in leave
            $leave = Leave::find($data->reference_id);
            $leave->update([
                'status' => 'approved'
            ]);

            // Update approval status to approved  
            $data->update([
                'approved_at' => $timestamp,
                'done_at' => $timestamp,
                'status' => 'approved',
            ]);

            // Update Current Approver Data
            $approver->update([
                'approved_reason' => $request->reason,
                'approved_at' => $timestamp,
                'done_at' => $timestamp,
                'status' => 'approved',
            ]);

            // Reduce employee quota
            $period = CarbonPeriod::create($leave->start_date, $leave->end_date);
            $totalDay = $period->toArray();
            $quota = LeaveQuota::where('employee_id', $leave->employee_id)->where('leave_type_id', $leave->leave_type_id)->first();
            $quota->update([
                'quota' => $quota->quota - count($totalDay)
            ]);
        } else {
            // Get next approver
            $nextApprover = $leaveApprovements[$current + 1];

            // Update Current Approver Data
            $approver->update([
                'approved_reason' => $request->reason,
                'approved_at' => $timestamp,
                'done_at' => $timestamp,
                'status' => 'approved',
            ]);

            // Update next approver status to awaiting
            $nextApprover->update([
                'status' => 'awaiting',
                'notified_at' => $timestamp
            ]);
        }

        return $approver;
    }

    public function rejectLeaveRequest($request, $data)
    {
        // Order Leave Approvement By Level
        $leaveApprovements = $data->approval_steps->sortBy('level');

        // Count total level and get current approver
        $totalLevel = count($leaveApprovements);
        $user = Authentication::getUserLoggedIn();
        $approver_id = Employee::where('user_id', $user->id)->first()->id;
        $approver = $leaveApprovements->where('approver_id', $approver_id)->first();
        $current = array_search($approver->id, array_column($leaveApprovements->toArray(), 'id'));
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        if ($approver->level === $totalLevel) {
            // Update status and data in leave
            Leave::find($data->reference_id)->update([
                'status' => 'rejected',
                'reject_reason' => $request->reason
            ]);

            // Update approval status to rejected  
            $data->update([
                'rejected_at' => $timestamp,
                'done_at' => $timestamp,
                'status' => 'rejected',
            ]);

            // Update Current Approver Data
            $approver->update([
                'rejected_reason' => $request->reason,
                'rejected_at' => $timestamp,
                'done_at' => $timestamp,
                'status' => 'rejected',
            ]);
        } else {
            // Update status and data in leave
            Leave::find($data->reference_id)->update([
                'status' => 'rejected',
                'reject_reason' => $request->reason
            ]);

            // Update approval status to rejected  
            $data->update([
                'rejected_at' => $timestamp,
                'done_at' => $timestamp,
                'status' => 'rejected',
            ]);

            // Update Current Approver Data
            $approver->update([
                'rejected_reason' => $request->reason,
                'rejected_at' => $timestamp,
                'done_at' => $timestamp,
                'status' => 'rejected',
            ]);

            for ($i = $current + 1; $i < $totalLevel; $i++) {
                $voidApprover = $leaveApprovements[$i];

                // Update next approver status to void
                $voidApprover->update([
                    'void_at' => $timestamp,
                    'done_at' => $timestamp,
                    'status' => 'void',
                    'notified_at' => $timestamp
                ]);
            }
        }

        return $approver;
    }

    public function checkApprovableLeave($requester)
    {
        $approvalRule = ApprovalRule::where('branch_id', $requester->branch_id)->whereHas('approval_type', function($q) {
            $q->where('type', 'create_leave');
        })->first();

        return $approvalRule && $approvalRule->need_approval ? true : false;
    }
}
