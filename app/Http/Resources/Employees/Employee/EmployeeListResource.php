<?php

namespace App\Http\Resources\Employees\Employee;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EmployeeListResource extends ResourceCollection
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
                "message" => "Success get employee lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        return [
            'id' => $data->id,
            'name' => $data->user_detail->name,
            'branch' => $data->branch_detail->name,
            'designation' => $data->designation_detail->name,
            'email' => $data->user_detail->email,
            'begin_contract' => Carbon::parse($data->start_date)->format('d F Y'),
            'end_contract' => Carbon::parse($data->end_date)->format('d F Y'),
            'role' => $data->user_detail->getRoleNames()->count() > 0 ? $data->user_detail->getRoleNames()[0] : '-',
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
