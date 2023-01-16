<?php

namespace App\Http\Resources\Settings\Overtime;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OvertimeRuleListResource extends ResourceCollection
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
                "message" => "Success get overtime rule lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        return [
            'id' => $data->id,
            'clock_in' => [
                'hours' => Carbon::parse($data->clock_in)->format('H'),
                'minutes' => Carbon::parse($data->clock_in)->format('i')
            ],
            'clock_out' => [
                'hours' => Carbon::parse($data->clock_out)->format('H'),
                'minutes' => Carbon::parse($data->clock_out)->format('i')
            ],
            'clock_in_format' => $data->clock_in,
            'clock_out_format' => $data->clock_out,
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
