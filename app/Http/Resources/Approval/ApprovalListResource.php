<?php

namespace App\Http\Resources\Approval;

use Carbon\Carbon;
use App\Models\Employee;
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
        $user = Authentication::getUserLoggedIn();
        $employee = Employee::where('user_id', $user->id)->first();
        $approver_id = $employee ? $employee->id : null;
        $approval_step = $data->approval_steps->where('approver_id', $approver_id)->first();

        return [
            'id' => $data->id,
            'type' => $data->type,
            'status' => $approval_step ? $approval_step->status : '-',
            'created_by' => $data->requester['name'],
            'branch' => $data->requester['branch'],
            'created_at' => Carbon::parse($data->created_at)->format('d F Y'),
            'meta_data' => $formatMeta->handle($data)
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
