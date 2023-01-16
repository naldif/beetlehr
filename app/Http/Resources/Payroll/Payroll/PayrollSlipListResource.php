<?php

namespace App\Http\Resources\Payroll\Payroll;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PayrollSlipListResource extends ResourceCollection
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
                "message" => "Success get payroll slip lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        return [
            'id' => $data->id,
            'branch_name' => $data->branch_detail ? $data->branch_detail->name : 'All Branches',
            'month' => Carbon::parse($data->date)->format('m'),
            'year' => Carbon::parse($data->date)->format('Y'),
            'total_amount' => number_format($data->total_amount, 2, ',', '.'),
            'total_employee' => $data->employee_slips->count(),
            'created_by' => $data->created_by_detail->name,
            'created_at' => Carbon::parse($data->created_at)->format('d F Y')
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
