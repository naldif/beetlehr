<?php

namespace App\Http\Resources\Settings\Company;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NpwpListResource extends ResourceCollection
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
                "message" => "Success get npwp lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        return [
            'id' => $data->id,
            'npwp_name' => $data->npwp_name,
            'number_npwp' => $data->number_npwp,
            'npwp_company_name' => $data->npwp_company_name,
            'address' => $data->address,
            'postal_code' => $data->postal_code,
            'city' => $data->city,
            'kpp' => $data->kpp,
            'date' => [
                'month' => Carbon::parse($data->active_month)->subMonth(1)->format('m'),
                'year' => Carbon::parse($data->active_month)->format('Y')
            ],
            'active_month_formatted' => Carbon::parse($data->active_month)->format('F Y'),
            'status_formatted' => $data->status ? 'active' : 'inactive' ,
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
