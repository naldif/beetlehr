<?php

namespace App\Listeners\Leave;

use App\Services\Approval\LeaveApprovalService;

class LeaveCreatedSubscriber
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

    public function handleLeaveApproval($event)
    {
        $leaveApprovalService = new LeaveApprovalService();
        $leaveApprovalService->assignApprover($event);
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
            'App\Events\Leave\LeaveWasCreated',
            'App\Listeners\Leave\LeaveCreatedSubscriber@handleLeaveApproval'
        );
    }
}
