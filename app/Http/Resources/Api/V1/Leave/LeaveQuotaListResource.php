<?php

namespace App\Http\Resources\Api\V1\Leave;

use Illuminate\Http\Resources\Json\JsonResource;

class LeaveQuotaListResource extends JsonResource
{
    private $message;

    public function __construct($resource, $message)
    {
        parent::__construct($resource);
        $this->message = $message;
        $this->resource = $resource;
    }
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'total_quota' => $this->total_quota,
                'quota_remaining' => $this->total_quota_remaining,
                'leave_quotas' => $this->leave_quotas
            ],
            'meta' => [
                'success' => true,
                'message' => $this->message,
                'pagination' => (object)[]
            ]
        ];
    }
}
