<?php

namespace App\Http\Resources\Employees\Employee;

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
                'total_present' => $this['total_present'],
                'total_absent' => $this['total_absent'],
                'total_late' => $this['total_late'],
                'total_clockout_early' => $this['total_clockout_early'],
                'present_work_hours' => $this['present_work_hours'],
                'late_work_hours' => $this['late_work_hours'],
                'clockout_early_work_hours' => $this['clockout_early_work_hours']
            ],
            'meta' => [
                'success' => true,
                'message' => $this->message,
                'pagination' => (object)[],
            ],
        ];
    }
}
