<?php

namespace App\Http\Resources\Settings\Payroll;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PayrollEmployeeComponentListResource extends ResourceCollection
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
                "message" => "Success get payroll employee component lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        if (count($data->payroll_employee_components) > 0) {
            $default_amount_formatted = number_format($data->payroll_employee_components->first()->default_amount, 2, ',', '.');
            $default_amount = (int)$data->payroll_employee_components->first()->default_amount;
            $status_formatted = $data->payroll_employee_components->first()->status ? 'active' : 'inactive';
            $status = $data->payroll_employee_components->first()->status;
            $can_delete = true;
            $employee_component_id = $data->payroll_employee_components->first()->id;
        } else {
            $default_amount_formatted = null;
            $default_amount = null;
            $status_formatted = '-';
            $status = null;
            $can_delete = false;
            $employee_component_id = null;
        }

        return [
            'id' => $data->id,
            'name' => $data->user_detail->name,
            'branch_name' => $data->branch_detail->name,
            'default_amount_formatted' => $default_amount_formatted,
            'status_formatted' => $status_formatted,
            'default_amount' => $default_amount,
            'status' => $status,
            'can_delete' => $can_delete,
            'employee_component_id' => $employee_component_id
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
