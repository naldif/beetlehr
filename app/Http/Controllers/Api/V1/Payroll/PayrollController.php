<?php

namespace App\Http\Controllers\Api\V1\Payroll;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController;
use App\Services\Api\V1\Payroll\PayrollService;
use App\Http\Resources\Api\V1\Payroll\PayrollListResource;
use App\Http\Resources\Api\V1\Payroll\DetailPayrollResource;

class PayrollController extends ApiBaseController
{
    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function getPayrollList(Request $request)
    {
        try {
            $data = $this->payrollService->getData($request);

            $user = new PayrollListResource($data);
            return $this->respond($user);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getDetailPayroll($id)
    {
        try {
            $data = $this->payrollService->getDetail($id);

            $user = new DetailPayrollResource($data, 'Success Get Detail Payroll');
            return $this->respond($user);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
