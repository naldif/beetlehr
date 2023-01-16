<?php

namespace App\Http\Resources\Employees\Resign;

use Carbon\Carbon;
use App\Services\FileService;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ResignManagementListResource extends ResourceCollection
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
                "message" => "Success get resign management lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        $fileService = new FileService();

        return [
            'id' => $data->id,
            'name' => $data->employee_detail->user_detail->name,
            'branch' => $data->employee_detail->branch_detail->name,
            'date_submission' => Carbon::parse($data->date)->format('d F Y'),
            'end_contract' => Carbon::parse($data->end_contract)->format('d F Y'),
            'status' => $data->status,
            'status_formatted' => ucfirst($data->status),
            'file' => $data->file,
            'file_ext' => $fileService->getFileById($data->file)->extension
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
