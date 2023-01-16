<?php

namespace App\Http\Resources\Api\V1\Attendance;

use Illuminate\Http\Resources\Json\JsonResource;

class BreakCheckLocationResource extends JsonResource
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
                'placement_latitude' => $this['branch_latitude'],
                'placement_longitude' =>  $this['branch_longitude'],
                'max_radius' => $this['max_radius'],
                'address' => $this['address'],
                'distance' => $this['distance'],
            ],
            'meta' => [
                'success' => true,
                'message' => $this->message,
                'pagination' => (object)[],
            ],
        ];
    }
}
