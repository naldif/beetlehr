<?php

namespace App\Http\Resources\Api\V1\Attendance;

use Carbon\Carbon;
use App\Helpers\Utility\Authentication;
use App\Actions\Utility\Attendance\CheckInsideLeave;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ScheduleListResource extends ResourceCollection
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
                "message" => "Success Get Schedule",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        $employee = Authentication::getEmployeeLoggedIn();
        $timezone = $employee->branch_detail->timezone;

        //  Check if current date has leave
        $is_leave = $data->is_leave;
        $check_if_has_leave = new CheckInsideLeave();
        $check_if_has_leave->handle($data->date, $employee) ? $is_leave = 1 : null;

        return [
            'id' => $data->id,
            'date' => $data->date,
            'is_leave' => $is_leave,
            'shift' => $data->shift_detail ? 
                [
                    'name' => $data->shift_detail->name,
                    'time_start' => Carbon::parse($data->shift_detail->start_time)->timezone($timezone)->format('H:i:s'),
                    'time_end' => Carbon::parse($data->shift_detail->end_time)->timezone($timezone)->format('H:i:s')
                ] : null,
        ];
    }

    private function transformCollection($collection)
    {
        return $collection->transform(function ($data) {
            return $this->transformData($data);
        });
    }

    private function metaData()
    {
        return [
            "total" => $this->total(),
            "count" => $this->count(),
            "per_page" => (int)$this->perPage(),
            "current_page" => $this->currentPage(),
            "total_pages" => $this->lastPage(),
            "links" => [
                "next" => $this->nextPageUrl()
            ],
        ];
    }
}
