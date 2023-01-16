<?php

namespace App\Http\Resources\Payroll\Payroll;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PayrollEmployeeListResource extends ResourceCollection
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
                "message" => "Success get payroll employee lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        return [
            'id' => $data->id,
            'branch_name' => $data->employee_detail->branch_detail->name,
            'employee_name' => $data->employee_detail->user_detail->name. ' - ' .$data->employee_detail->designation_detail->name,
            'total_amount' => number_format($data->total_amount, 2, ',', '.'),
            'status' => $data->status
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
