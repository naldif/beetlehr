<?php

namespace App\Http\Resources\Api\V1\Approval;

use Illuminate\Http\Resources\Json\JsonResource;

class ApproveApprovalResource extends JsonResource
{
    public function __construct($resource)
    {
        // Ensure you call the parent constructor
        parent::__construct($resource);
        $this->resource = $resource;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'approver' => '-',
                'status' => 'approved',
                'type' => '-'
            ],
            'meta' => [
                'success' => true,
                'message' => 'Success Approve Approval Request',
                'pagination' => (object)[],
            ],
        ];
    }
}
