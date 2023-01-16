<?php

namespace App\Http\Resources\Attendances\Schedule;

use Illuminate\Http\Resources\Json\JsonResource;

class SubmitScheduleResource extends JsonResource
{
    private $message;

    public function __construct($resource, $message,$success = true)
    {
        // Ensure you call the parent constructor
        parent::__construct($resource);
        $this->resource = $resource;
        $this->message = $message;
        $this->success = $success;
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
                'id' => $this->id ?? ''
            ],
            'meta' => [
                'success' => $this->success,
                'message' => $this->message,
                'pagination' => (object)[],
            ],
        ];
    }
}
