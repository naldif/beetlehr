<?php

namespace App\Http\Resources\Api\V1\Attendance;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceOverviewResource extends JsonResource
{
    private $message;

    public function __construct($resource, $message)
    {
        // Ensure you call the parent constructor
        parent::__construct($resource);
        $this->resource = $resource;

        $this->message = $message;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'present' => $this['present'],
                'ontime' => $this['ontime'],
                'clockout_early' => $this['clockout_early'],
                'late' => $this['late'],
                'absent' => $this['absent'],
                'holiday' => $this['holiday'],
                'total_work_hours' => $this['total_work_hours'],
                'total_late_hours' => $this['total_late_hours'],
                'total_early_hours' => $this['total_early_hours'],
                'total_absent_hours' => $this['total_absent_hours'],
                'total_leaves' => $this['total_leaves'],
                'total_remaining_leaves' => $this['total_remaining_leaves'],
                'leaves' => $this['leaves']
            ],
            'meta' => [
                'success' => true,
                'message' => $this->message,
                'pagination' => (object)[],
            ],
        ];
    }
}
