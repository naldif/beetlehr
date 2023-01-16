<?php

namespace App\Http\Resources\Api\V1\Attendance;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceClockResource extends JsonResource
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
                'id' => $this['id'],
                'date' => $this['date'],
                'date_gmt' => $this['date_gmt'],
                'clock' => $this['clock'],
                'clock_gmt' => $this['clock_gmt'],
                'type' => $this['type'],
                'status' => $this['status'],
                'latitude' => $this['latitude'],
                'longitude' => $this['longitude'],
                'address' => $this['address'],
                'image' => $this['image'],
                'files' => $this['files'],
                'notes'  => $this['notes'],
                'is_late' => $this['is_late'],
                'clockout_duration' => $this['clockout_duration'],
                'clockout_tolerance_duration' => $this['clockout_tolerance_duration'],
                'tracker_interval' => $this['tracker_interval'],
                'tracker_configuration' => $this['tracker_configuration']
            ],
            'meta' => [
                'success' => true,
                'message' => $this->message,
                'pagination' => (object)[],
            ],
        ];
    }
}
