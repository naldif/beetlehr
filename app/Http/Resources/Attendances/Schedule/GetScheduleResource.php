<?php

namespace App\Http\Resources\Attendances\Schedule;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GetScheduleResource extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->transformCollection($this->collection),
            'meta' => [
                "success" => true,
                "message" => "Success get schedule lists",
                // 'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        return [
            'data' => $data
        ];
    }

    private function transformCollection($collection)
    {
        return $collection->transform(function ($data) {
            return $this->transformData($data);
        });
    }

    // private function metaData()
    // {
    //     return [
    //         "total" => $this->total(),
    //         "count" => $this->count(),
    //         "per_page" => (int)$this->perPage(),
    //         "current_page" => $this->currentPage(),
    //         "total_pages" => $this->lastPage(),
    //         "links" => [
    //             "next" => $this->nextPageUrl()
    //         ],
    //     ];
    // }
}
