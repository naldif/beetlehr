<?php

namespace App\Http\Resources\Api\V1\Approval;

use Carbon\Carbon;
use App\Services\FileService;
use App\Helpers\Utility\Authentication;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Actions\Utility\Approval\ApprovalMetaFormat;

class ApprovalDetailResource extends JsonResource
{
    public function __construct($resource)
    {
        // Ensure you call the parent constructor
        parent::__construct($resource);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Init Approval Service
        $formatMeta = new ApprovalMetaFormat();
        $file_service = new FileService();

        // Get Approval Status
        $employee = Authentication::getEmployeeLoggedIn();
        $approver_id = $employee ? $employee->id : null;
        $approval_step = $this->approval_steps->where('approver_id', $approver_id)->first();
        $status = $approval_step ? $approval_step->status : '-';

        // Format Approval Type 
        $meta = $formatMeta->handle($this);

        return [
            'data' => [
                'id' => $this->id,
                'requester_name' => $this->requester_detail->user_detail->name,
                'requester_image' => $this->requester_detail->image ? $file_service->getFileById($this->requester_detail->image)->full_path : null,
                'requester_designation' => $this->requester_detail->designation_detail->name,
                'requester_placement' => $this->requester_detail->branch_detail->name,
                'status' => $status,
                'status_label' => ucfirst($status),
                'type' => $meta['type_mobile'],
                'is_approvable' => $status === 'awaiting' ? true : false,
                'meta_data' => $meta,
                'approvers' => $this->approval_steps->map(function($q) use($employee) {
                    return [
                        'user_id' => $q->approver['user_id'],
                        'approver_name' => $q->approver['name'],
                        'designation' => $q->approver['designation'],
                        'status' => $q->status,
                        'status_label' => ucfirst($q->status),
                        'reason' => $q->status === 'approved' ? $q->approved_reason : $q->rejected_reason,
                        'timestamp' => Carbon::parse($q->updated_at)->timezone($employee->branch_detail->timezone)->format('Y-m-d H:i:s'),
                        'timestamp_gmt' => Carbon::parse($q->updated_at)->format('Y-m-d H:i:s'),
                    ];
                })
            ],
            'meta' => [
                'success' => true,
                'message' => 'Success Get Detail Approval Request',
                'pagination' => (object)[],
            ],
        ];
    }
}
