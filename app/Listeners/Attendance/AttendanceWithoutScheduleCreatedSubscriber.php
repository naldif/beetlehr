<?php

namespace App\Listeners\Attendance;

use App\Services\Approval\AttendanceApprovalService;

class AttendanceWithoutScheduleCreatedSubscriber
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handleAttendanceApproval($event)
    {
        $approvalService = new AttendanceApprovalService();
        $approvalService->assignApprover($event);
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Attendance\AttendanceWithoutScheduleCreated',
            'App\Listeners\Attendance\AttendanceWithoutScheduleCreatedSubscriber@handleAttendanceApproval'
        );
    }
}
