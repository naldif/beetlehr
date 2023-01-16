<?php

namespace App\Http\Resources\Api\V1\Leave;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Services\FileService;
use App\Helpers\Utility\Authentication;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveDetailResource extends JsonResource
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
        $file_service = new FileService();
        $timestamp = Carbon::parse($this->updated_at)->timezone($employee->branch_detail->timezone)->format('d M Y H:i:s');

        if ($this->status === 'waiting') {
            $label = 'Waiting';
        } elseif ($this->status === 'approved') {
            $label = 'Approved at ' . $timestamp;
        } elseif ($this->status === 'rejected') {
            $label = 'Declined at ' . $timestamp;
        } elseif ($this->status === 'cancelled') {
            $label = 'Cancelled at ' . $timestamp;
        }

        if ($this->start_date === $this->end_date) {
            $date = Carbon::parse($this->start_date)->format('d M Y');
        } else {
            $date = Carbon::parse($this->start_date)->format('d M Y') . ' - ' . Carbon::parse($this->end_date)->format('d M Y');
        }

        $period = CarbonPeriod::create($this->start_date, $this->end_date);
        $dates = $period->toArray();
        
        return [
            'data' => [
                'id' => $this->id,
                'leave_type' => $this->leave_type_detail->name,
                'status' => $this->status,
                'start_date' => $date,
                'total_date' => count($dates),
                'label' => $label,
                'reason' => $this->reason,
                'reject_reason' => $this->reject_reason,
                'file_url' => $file_service->getFileById($this->file)->full_path
            ],
            'meta' => [
                'success' => true,
                'message' => $this->message,
                'pagination' => (object)[],
            ],
        ];
    }
}
