<?php

namespace App\Http\Controllers\Settings\Payroll;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminBaseController;
use App\Services\Settings\Payroll\PayrollBranchComponentService;
use App\Http\Resources\Settings\Payroll\SubmitPayrollBranchComponent;
use App\Http\Requests\Settings\Payroll\UpdatePayrollBranchComponentRequest;
use App\Http\Resources\Settings\Payroll\PayrollBranchComponentListResource;

class PayrollBranchComponentController extends AdminBaseController
{
    public function __construct(PayrollBranchComponentService $payrollBranchComponentService)
    {
        $this->payrollBranchComponentService = $payrollBranchComponentService;
    }

    public function getPayrollBranchList(Request $request)
    {
        try {
            $data = $this->payrollBranchComponentService->getData($request);
            $result = new PayrollBranchComponentListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updatePayrollBranch(UpdatePayrollBranchComponentRequest $request)
    {
        try {
            $data = $this->payrollBranchComponentService->updateData($request);
            $result = new SubmitPayrollBranchComponent($data, 'Success Update Payroll Branch Component');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deletePayrollBranch($id)
    {
        try {
            $data = $this->payrollBranchComponentService->deleteData($id);
            $result = new SubmitPayrollBranchComponent($data, 'Success Delete Payroll Branch Component');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
