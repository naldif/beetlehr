<?php

namespace App\Http\Resources\Api\V1\Attendance;

use Illuminate\Http\Resources\Json\JsonResource;

class CheckAttendanceClockedResource extends JsonResource
{
    private $message;

    public function __construct($resource, $message)
    {
        // Ensure you call the parent constructor
        parent::__construct($resource);
        $this->resource = $resource;
        $this->message = $message;
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
                'accepted' => $this['accepted'],
                'message' => $this['messageValidation']
            ],
            'meta' => [
                'success' => $this['accepted'],
                'code' => isset($this['status']) ? $this['status'] : 200,
                'message' => $this->message,
                'pagination' => (object)[],
            ],
        ];
    }
}
