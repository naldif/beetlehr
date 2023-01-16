<?php

namespace App\Http\Resources\Settings\Company;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BranchListResource extends ResourceCollection
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
                "message" => "Success get branch lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        return [
            'id' => $data->id,
            'name' => $data->name,
            'npwp_number' => $data->npwp_list_detail ? $data->npwp_list_detail->number_npwp : '-',
            'npwp_list_id' => $data->npwp_list_id,
            'address' => $data->address,
            'city' => $data->city,
            'state' => $data->state,
            'zip_code' => $data->zip_code,
            'latitude' => $data->latitude,
            'longitude' => $data->longitude,
            'radius' => $data->radius,
            'telegram_chat_id' => $data->telegram_chat_id,
            'timezone' => $data->timezone,
            'radius_tracker' => $data->radius_tracker,
            'tracker_interval' => [
                'hours' => Carbon::parse($data->tracker_interval)->format('H'),
                'minutes' => Carbon::parse($data->tracker_interval)->format('i')
            ]
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
