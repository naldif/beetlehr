<?php

namespace App\Actions\Utility\Approval;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\BreakTime;
use App\Models\Attendance;
use App\Models\ApprovalRule;
use App\Models\BreakTimeOffline;
use App\Models\AttendanceOffline;
use App\Helpers\Utility\Authentication;


class AttendanceApprovalAction
{
    public function approveAttendanceRequest($request, $data)
    {
        // Order Attendance Approvement By Level
        $attendanceApprovements = $data->approval_steps->sortBy('level');

        // Count total level and get current approver
        $totalLevel = count($attendanceApprovements);
        $user = Authentication::getUserLoggedIn();
        $approver_id = Employee::where('user_id', $user->id)->first()->id;
        $approver = $attendanceApprovements->where('approver_id', $approver_id)->first();
        $current = array_search($approver->id, array_column($attendanceApprovements->toArray(), 'id'));
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        if ($approver->level === $totalLevel) {
            // Update status and data in leave
            $attendance = AttendanceOffline::find($data->reference_id);
            $attendance->update([
                'status' => 'approved'
            ]);
            $this->createAttendanceApproved($attendance);

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
        } else {
            // Get next approver
            $nextApprover = $attendanceApprovements[$current + 1];

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

    public function rejectAttendanceRequest($request, $data)
    {
        // Order Attendance Approvement By Level
        $attendanceApprovements = $data->approval_steps->sortBy('level');

        // Count total level and get current approver
        $totalLevel = count($attendanceApprovements);
        $user = Authentication::getUserLoggedIn();
        $approver_id = Employee::where('user_id', $user->id)->first()->id;
        $approver = $attendanceApprovements->where('approver_id', $approver_id)->first();
        $current = array_search($approver->id, array_column($attendanceApprovements->toArray(), 'id'));
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        if ($approver->level === $totalLevel) {
            // Update status and data in leave
            $attendance = AttendanceOffline::find($data->reference_id);
            $attendance->update([
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
            $attendance = AttendanceOffline::find($data->reference_id);
            $attendance->update([
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
                $voidApprover = $attendanceApprovements[$i];

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

    public function checkApprovableAttendance($requester)
    {
        $approvalRule = ApprovalRule::where('branch_id', $requester->branch_id)->whereHas('approval_type', function($q) {
            $q->where('type', 'attendance_without_schedule');
        })->first();

        return $approvalRule && $approvalRule->need_approval ? true : false;
    }

    public function createAttendanceApproved($attendance)
    {
        $attendance_input = [
            'is_offline_clock_in' => $attendance->is_offline_clock_in,
            'is_offline_clock_out' => $attendance->is_offline_clock_out,
            'user_id' => $attendance->user_id,
            'status' => $attendance->type,
            'clock_in' => $attendance->clock_in,
            'clock_out' => $attendance->clock_out,
            'date_clock' => $attendance->date_clock,
            'latitude_clock_in' => $attendance->latitude_clock_in,
            'longitude_clock_in' => $attendance->longitude_clock_in,
            'latitude_clock_out' => $attendance->latitude_clock_out,
            'longitude_clock_out' => $attendance->longitude_clock_out,
            'notes_clock_in' => $attendance->notes_clock_in,
            'notes_clock_out' => $attendance->notes_clock_out,
            'files_clock_in' => $attendance->files_clock_in,
            'files_clock_out' => $attendance->files_clock_out,
            'image_id_clock_in' => $attendance->image_id_clock_in,
            'image_id_clock_out' => $attendance->image_id_clock_out,
            'address_clock_in' => $attendance->address_clock_in,
            'address_clock_out' => $attendance->address_clock_out,
            'is_outside_radius_clock_in' => 0,
            'is_outside_radius_clock_out' => 0,
            'is_late_clock_in' => 0,
            'is_early_clock_out' => 0,
            'total_late_clock_in' => '00:00:00',
            'total_early_clock_out' => '00:00:00',
            'total_work_hours' => $attendance->total_work_hours
        ];

        $attendance_result = Attendance::create($attendance_input);
        $breaks = BreakTimeOffline::where('attendance_offline_log_id', $attendance->id)->get();

        foreach ($breaks as $break) {
            $break_data = [
                'attendance_id' => $attendance_result->id,
                'end_time' => $break->end_time,
                'total_work_hours' => $break->total_work_hours,
                'latitude_end_break' => $break->latitude_end_break,
                'longitude_end_break' => $break->longitude_end_break,
                'address_end_break' => $break->address_end_break,
                'note_end_break' => $break->note_end_break,
                'image_id_end_break' => $break->image_id_end_break,
                'start_time' => $break->start_time,
                'latitude_start_break' => $break->latitude_start_break,
                'longitude_start_break' => $break->longitude_start_break,
                'address_start_break' => $break->address_start_break,
                'note_start_break' => $break->note_start_break,
                'image_id_start_break' => $break->image_id_start_break
            ];

            BreakTime::create($break_data);
        }

        return $attendance_result;
    }
}
