<?php

namespace App\Http\Resources\Api\V1\Employee;

use App\Services\FileService;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileDetailResource extends JsonResource
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
        $file_service = new FileService();

        return [
            'data' => [
                'name' => $this->user_detail->name,
                'designation' => $this->designation_detail->name,
                'email' => $this->user_detail->email,
                'phone_number' => $this->phone_number,
                'address' => $this->address,
                'account_number' => $this->account_number,
                'image' => $this->image ? $file_service->getFileById($this->image)->full_path : null,
                'nip' => $this->nip
            ],
            'meta' => [
                'success' => true,
                'message' => $this->message,
                'pagination' => (object)[],
            ],
        ];
    }
}
