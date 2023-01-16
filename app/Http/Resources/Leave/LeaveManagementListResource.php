<?php

namespace App\Http\Resources\Leave;

use Carbon\Carbon;
use App\Models\Approval;
use App\Services\FileService;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LeaveManagementListResource extends ResourceCollection
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
                "message" => "Success get leave management lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        $fileService = new FileService();

        // Check if leave has approvals
        $leave_has_approvals = Approval::where('type', 'create_leave')->where('reference_id', $data->id)->exists();

        return [
            'id' => $data->id,
            'employee_name' => $data->employee_detail->user_detail->name,
            'leave_type' => $data->leave_type_detail->name,
            'start_date' => Carbon::parse($data->start_date)->format('d F Y'),
            'end_date' => Carbon::parse($data->end_date)->format('d F Y'),
            'status' => $data->status,
            'status_formatted' => ucfirst($data->status),
            'file' => $data->file,
            'file_ext' => $fileService->getFileById($data->file)->extension,
            'leave_has_approvals' => $leave_has_approvals
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
