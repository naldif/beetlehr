<?php

namespace App\Http\Resources\Settings\Employee;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GroupListResource extends ResourceCollection
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
                "message" => "Success get group lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        $employee_id = [];
        foreach ($data->employee_groups as $employee) {
            $employee_id[] = $employee->id;
        }
        return [
            'id' => $data->id,
            'name' => $data->name,
            'branch' => $data->employee_groups[0]->employee->branch->name,
            'branch_id' => $data->employee_groups[0]->employee->branch->id,
            'total_employee' => $data->employee_groups->count(),
            'employee_id' => $employee_id
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
