<?php

namespace App\Listeners\Leave;

use App\Services\Approval\LeaveApprovalService;

class LeaveDeletedSubscriber
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

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handleDeleteApproval($event)
    {
        $leaveApprovalService = new LeaveApprovalService();
        $leaveApprovalService->deleteRelatedApproval($event);
    }

    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Leave\LeaveWasDeleted',
            'App\Listeners\Leave\LeaveDeletedSubscriber@handleDeleteApproval'
        );
    }
}
