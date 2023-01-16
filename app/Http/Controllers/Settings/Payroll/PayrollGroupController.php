<?php

namespace App\Http\Controllers\Settings\Payroll;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminBaseController;
use App\Services\Settings\Payroll\PayrollGroupService;
use App\Http\Resources\Settings\Payroll\SubmitPayrollResource;
use App\Http\Requests\Settings\Payroll\CreatePayrollGroupRequest;
use App\Http\Requests\Settings\Payroll\UpdatePayrollGroupRequest;
use App\Http\Resources\Settings\Payroll\PayrollGroupListResource;

class PayrollGroupController extends AdminBaseController
{
    public function __construct(PayrollGroupService $payrollGroupService)
    {
        $this->payrollGroupService = $payrollGroupService;
    }

    public function getPayrollGroup(Request $request)
    {
        try {
            $data = $this->payrollGroupService->getData($request);

            $result = new PayrollGroupListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createPayrollGroup(CreatePayrollGroupRequest $request)
    {
        try {
            $data = $this->payrollGroupService->storeData($request);

            $result = new SubmitPayrollResource($data, 'Success Create Payroll Group');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updatePayrollGroup($id, UpdatePayrollGroupRequest $request)
    {
        try {
            $data = $this->payrollGroupService->updateData($id, $request);

            $result = new SubmitPayrollResource($data, 'Success Update Payroll Group');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deletePayrollGroup($id)
    {
        try {
            $data = $this->payrollGroupService->deleteData($id);

            $result = new SubmitPayrollResource($data, 'Success Delete Payroll Group');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
