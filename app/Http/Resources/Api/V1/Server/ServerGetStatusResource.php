<?php

namespace App\Http\Resources\Api\V1\Server;

use Illuminate\Http\Resources\Json\JsonResource;

class ServerGetStatusResource extends JsonResource
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
                'tenant' => [
                    'name' => $this['name'],
                    'url' => request()->getSchemeAndHttpHost(),
                    'logo' => $this['logo'],
                    'status' => $this['status'],
                ],
                'serverVersion' => config('app.serverVersion'),
                'minimumClientVersion' => config('app.minimumClientVersion')
            ],
            'meta' => [
                'success' => true,
                'message' => $this->message,
                'pagination' => (object)[],
            ],
        ];
    }
}
