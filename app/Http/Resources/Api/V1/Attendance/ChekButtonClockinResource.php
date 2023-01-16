<?php

namespace App\Http\Resources\Api\V1\Attendance;

use Illuminate\Http\Resources\Json\JsonResource;

class ChekButtonClockinResource extends JsonResource
{
    private $message;

    public function __construct($resource, $message)
    {
        // Ensure you call the parent constructor
        parent::__construct($resource);
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
        $alreadyClockout = false;
        if ($this['attendance'] !== null) {
            $alreadyClockout = $this['attendance']['clock_out'] !== null ? true : false;
        } 

        return [
            'data' => [
                'type' => $this['type'],
                'message_type' => $alreadyClockout && $this['message_type'] !== null ? 'already clockout' : $this['message_type'],
                'is_already_clockout' => $alreadyClockout,
                'break_type' => $alreadyClockout ? null : $this['break_type'],
                'start_break_time' => $alreadyClockout ? null : $this['start_break_time']
            ],
            'meta' => [
                'success' => true,
                'message' => $this->message,
                'pagination' => (object)[],
            ],
        ];
    }
}
