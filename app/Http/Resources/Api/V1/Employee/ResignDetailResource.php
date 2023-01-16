<?php

namespace App\Http\Resources\Api\V1\Employee;

use Carbon\Carbon;
use App\Services\FileService;
use App\Helpers\Utility\Authentication;
use Illuminate\Http\Resources\Json\JsonResource;

class ResignDetailResource extends JsonResource
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
        $employee = Authentication::getEmployeeLoggedIn();
        $timestamp = Carbon::parse($this->updated_at)->timezone($employee->branch_detail->timezone)->format('d M Y H:i:s');
        $file_service = new FileService();
        $file = $file_service->getFileById($this->file);
        
        if ($this->status === 'waiting') {
            $label = 'Waiting';
        } elseif ($this->status === 'approved') {
            $label = 'Approved at ' . $timestamp;
        } elseif ($this->status === 'rejected') {
            $label = 'Declined at ' . $timestamp;
        } elseif ($this->status === 'cancelled') {
            $label = 'Cancelled at ' . $timestamp;
        }

        return [
            'data' => [
                'id' => $this->id,
                'label' => $label,
                'status' => $this->status,
                'date' => Carbon::parse($this->date)->timezone($employee->branch_detail->timezone)->format('d M Y'),
                'end_contract' => Carbon::parse($this->end_contract)->timezone($employee->branch_detail->timezone)->format('d M Y'),
                'reason' => $this->reason,
                'is_according_procedure' => $this->is_according_procedure === 1 ? true : false,
                'url_file' => $file->full_path,
                'file_name' => $file->file_name
            ],
            'meta' => [
                'success' => true,
                'message' => $this->message,
                'pagination' => (object)[],
            ],
        ];
    }
}
