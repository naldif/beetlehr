<?php

namespace App\Http\Resources\Settings\Leave;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LeaveQuotaListResource extends ResourceCollection
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
                "message" => "Success get leave quota lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        return [
            'id' => $data->id,
            'leave_type_name' => $data->leave_type_detail->name,
            'leave_type_id' => $data->leave_type_id,
            'leave_update_name' => $data->leave_type_detail->name.' -  max '.$data->leave_type_detail->quota.' Quota',
            'employee_name' => $data->employee_detail->user_detail->name,
            'employee_id' => $data->employee_id,
            'quota' => $data->quota
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
