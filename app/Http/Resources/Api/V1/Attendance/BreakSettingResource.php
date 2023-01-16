<?php

namespace App\Http\Resources\Api\V1\Attendance;

use App\Models\Setting;
use Illuminate\Http\Resources\Json\JsonResource;

class BreakSettingResource extends JsonResource
{
    private $message;

    public function __construct($message)
    {
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
        $close_page = Setting::where('key', 'close_break_page')->first();

        return [
            'data' => [
                'is_can_close_page' => $close_page && $close_page->value == 1 ? true : false,
            ],
            'meta' => [
                'success' => true,
                'message' => $this->message,
                'pagination' => (object)[],
            ],
        ];
    }
}
