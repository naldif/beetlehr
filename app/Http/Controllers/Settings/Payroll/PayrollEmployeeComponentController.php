<?php

namespace App\Http\Controllers\Settings\Payroll;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminBaseController;
use App\Services\Settings\Payroll\PayrollEmployeeComponentService;
use App\Http\Resources\Settings\Payroll\SubmitPayrollEmployeeComponent;
use App\Http\Requests\Settings\Payroll\UpdatePayrollEmployeeComponentRequest;
use App\Http\Resources\Settings\Payroll\PayrollEmployeeComponentListResource;

class PayrollEmployeeComponentController extends AdminBaseController
{
    public function __construct(PayrollEmployeeComponentService $payrollEmployeeComponentService)
    {
        $this->payrollEmployeeComponentService = $payrollEmployeeComponentService;
    }

    public function getPayrollEmployeeList(Request $request)
    {
        try {
            $data = $this->payrollEmployeeComponentService->getData($request);
            $result = new PayrollEmployeeComponentListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updatePayrollEmployee(UpdatePayrollEmployeeComponentRequest $request)
    {
        try {
            $data = $this->payrollEmployeeComponentService->updateData($request);
            $result = new SubmitPayrollEmployeeComponent($data, 'Success Update Payroll Employee Component');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deletePayrollEmployee($id)
    {
        try {
            $data = $this->payrollEmployeeComponentService->deleteData($id);
            $result = new SubmitPayrollEmployeeComponent($data, 'Success Delete Payroll Employee Component');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
