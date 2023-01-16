<?php

namespace App\Http\Resources\Api\V1\Approval;

use App\Services\FileService;
use App\Helpers\Utility\Authentication;
use App\Actions\Utility\Approval\ApprovalMetaFormat;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ApprovalListResource extends ResourceCollection
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
                "message" => "Success get approval lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        $formatMeta = new ApprovalMetaFormat();
        $meta = $formatMeta->handle($data);
        $file_service = new FileService();
        $employee = Authentication::getEmployeeLoggedIn();
        $approver_id = $employee ? $employee->id : null;
        $approval_step = $data->approval_steps->where('approver_id', $approver_id)->first();

        return [
            'id' => $data->id,
            'requester_name' => $data->requester['name'],
            'requester_image' => $data->requester_detail->image ? $file_service->getFileById($data->requester_detail->image)->full_path : null,
            'type' => $meta['type_mobile'],
            'type_label' => $meta['type_label'],
            'requested_at' => $data->requested_at,
            'status' => $approval_step ? $approval_step->status : null,
            'status_label' => $approval_step ? ucfirst($approval_step->status) : '-',
            'meta_data' => [
                'start_date' => $meta['start_date'],
                'end_date' => $meta['end_date'],
                'duration' => $meta['duration'],
            ]
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
