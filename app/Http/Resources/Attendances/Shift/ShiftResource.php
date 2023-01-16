<?php

namespace App\Http\Resources\Attendances\Shift;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ShiftResource extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->transformCollection($this->collection),
            'meta' => [
                "success" => true,
                "message" => "Success get employee lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        return [
            'id' => $data->id,
            'name' => $data->name,
            'branch' => $data->branch->name,
            'branch_id' => $data->branch->id,
            'is_night_shift' => $data->is_night_shift,
            'shift_type' => $data->is_night_shift ? 'Night Shift' : 'Normal Shift',
            'start_time' => [
                'hours' => Carbon::parse($data->start_time)->format('H'),
                'minutes' => Carbon::parse($data->start_time)->format('i')
            ],
            'end_time' =>[
                'hours' => Carbon::parse($data->end_time)->format('H'),
                'minutes' => Carbon::parse($data->end_time)->format('i')
            ],
            'formated_start_time' => $data->start_time,
            'formated_end_time' => $data->end_time,
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
