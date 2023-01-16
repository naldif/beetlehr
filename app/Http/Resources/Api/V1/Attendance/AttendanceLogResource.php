<?php

namespace App\Http\Resources\Api\V1\Attendance;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AttendanceLogResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->transformCollection($this->collection),
            'meta' => [
                "success" => true,
                "message" => "Success Get Attendance Log",
                'pagination' => (object)[]
            ]
        ];
    }

    private function transformData($data)
    {
        return [
            'date' => $data['date'],
            'date_gmt' => $data['date_gmt'],
            'clock_in' => $data['clock_in'],
            'clock_in_gmt' => $data['clock_in_gmt'],
            'clock_out' => $data['clock_out'],
            'clock_out_gmt' => $data['clock_out_gmt'],
            'work_hours' => $data['work_hours'],
            'type' => $data['type'],
            'status' => $data['status'],
            'is_force_clock_out' => $data['is_force_clock_out']
        ];
    }

    private function transformCollection($collection)
    {
        return $collection->transform(function ($data) {
            return $this->transformData($data);
        });
    }  
}
