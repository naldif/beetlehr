<?php

namespace App\Http\Resources\NoticeBoard;

use Carbon\Carbon;
use App\Services\FileService;
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
                "message" => "Success get notice board lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        $fileService = new FileService();

        return [
            'id' => $data->id,
            'title' => $data->title,
            'branch' => $data->branch_id ? $data->branch_detail->name : 'All Branches',
            'branch_id' => $data->branch_id ?: 'all',
            'type' => $data->type,
            'description' => $data->description,
            'start_date_db' => Carbon::parse($data->start),
            'end_date_db' => Carbon::parse($data->end),
            'end_date' => Carbon::parse($data->end)->format('d F Y - H:i'),
            'file_id' => $data->file,
            'file_ext' => $data->file ? $fileService->getFileById($data->file)->extension : null
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
