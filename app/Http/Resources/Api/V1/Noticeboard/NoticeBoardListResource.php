<?php

namespace App\Http\Resources\Api\V1\Noticeboard;

use Carbon\Carbon;
use App\Services\FileService;
use App\Helpers\Utility\Authentication;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NoticeBoardListResource extends ResourceCollection
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
                "message" => "Success Get Notice Boards",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        $employee = Authentication::getEmployeeLoggedIn();
        $fileService = new FileService();
        
        return [
            'id' => $data->id,
            'title' => $data->title,
            'description' => $data->type === 'description' ? $data->description : null,
            'date' => Carbon::parse($data->created_at)->timezone($employee->branch_detail->timezone)->format('d F Y'),
            'type' => $data->type,
            'file' => $data->type === 'document' ? $fileService->getFileById($data->file)->full_path : null
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
