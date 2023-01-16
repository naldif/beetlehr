<?php

namespace App\Http\Resources\Settings\Company;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BpjskListResource extends ResourceCollection
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
                "message" => "Success get bpjsk lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        return [
            'id' => $data->id,
            'name' => $data->name,
            'registration_number' => $data->registration_number,
            'bpjs_office' => $data->bpjs_office,
            'minimum_value' => $data->minimum_value,
            'minimum_value_formatted' => $data->minimum_value_formatted,
            'date' => [
                'month' => Carbon::parse($data->valid_month)->subMonth(1)->format('m'),
                'year' => Carbon::parse($data->valid_month)->format('Y')
            ],
            'valid_month_formatted' => Carbon::parse($data->valid_month)->format('F Y'),
            'status_formatted' => $data->status ? 'active' : 'inactive',
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
