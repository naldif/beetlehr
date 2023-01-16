<?php

namespace App\Http\Resources\Api\V1\Leave;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\LeaveQuota;
use App\Helpers\Utility\Authentication;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LeaveListResource extends ResourceCollection
{
    protected $employee;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $employee = Authentication::getEmployeeLoggedIn();
        $this->employee = $employee;

        $total_quota = LeaveQuota::where('employee_id', $employee->id)->whereHas('leave_type', function ($query) use ($employee) {
            $query->where('branch_id', $employee->branch_id);
        })->get()->sum('quota');
        
        return [
            'data' => $this->transformCollection($this->collection),
            'quota' => $total_quota,
            'meta' => [
                "success" => true,
                "message" => "Success Get Leave Lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        $timestamp = Carbon::parse($data->updated_at)->timezone($this->employee->branch_detail->timezone)->format('d M Y H:i:s');

        if ($data->status === 'waiting') {
            $label = 'Waiting';
        } elseif ($data->status === 'approved') {
            $label = 'Approved at ' . $timestamp;
        } elseif ($data->status === 'rejected') {
            $label = 'Declined at ' . $timestamp;
        } elseif ($data->status === 'cancelled') {
            $label = 'Cancelled at ' . $timestamp;
        }

        if ($data->start_date === $data->end_date) {
            $date = Carbon::parse($data->start_date)->format('d M Y');
        } else {
            $date = Carbon::parse($data->start_date)->format('d M Y') . ' - ' . Carbon::parse($data->end_date)->format('d M Y');
        }

        $period = CarbonPeriod::create($data->start_date, $data->end_date);
        $dates = $period->toArray();

        return [
            'id' => $data->id,
            'leave_type' => $data->leave_type_detail->name,
            'status' => $data->status,
            'start_date' => $date,
            'total_date' => count($dates),
            'label' => $label
        ];
    }

    private function transformCollection($collection)
    {
        return $collection->transform(function ($data) {
            return $this->transformData($data);
        });
    }

    private function metaData()
    {
        return [
            "total" => $this->total(),
            "count" => $this->count(),
            "per_page" => (int)$this->perPage(),
            "current_page" => $this->currentPage(),
            "total_pages" => $this->lastPage(),
            "links" => [
                "next" => $this->nextPageUrl()
            ],
        ];
    }
}
