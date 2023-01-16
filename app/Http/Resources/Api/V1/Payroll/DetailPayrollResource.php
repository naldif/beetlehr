<?php

namespace App\Http\Resources\Api\V1\Payroll;

use App\Services\FileService;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Actions\Utility\Payroll\GetEmployeeSlipComponent;

class DetailPayrollResource extends JsonResource
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
        $payroll_component_value = new GetEmployeeSlipComponent();
        $components_result = $payroll_component_value->handle($this);
        $file_service = new FileService();

        return [
            'data' => [
                'name' => $this->employee_detail->user_detail->name,
                'image' => $this->employee_detail->image ? $file_service->getFileById($this->employee_detail->image)->full_path : null,
                'designation' => $this->employee_detail->designation_detail->name,
                'status' => $this->status,
                'paid_on' => $this->paid_on,
                'total_earning' => (int)$this->earning_total + $this->amount,
                'total_deduction' => (int)$this->deduction_total,
                'total_amount' => number_format($this->total_amount, 2, '.', ''),
                'total_amount_after_pinalty' => null,
                'resign_pinalty_amount' => null,
                'earnings' => $components_result['earnings'],
                'deductions' => $components_result['deductions'],
                'public_url' => null
            ],
            'meta' => [
                'success' => true,
                'message' => $this->message,
                'pagination' => (object)[],
            ],
        ];
    }
}
