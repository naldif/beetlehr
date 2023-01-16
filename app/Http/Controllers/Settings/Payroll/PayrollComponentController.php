<?php

namespace App\Http\Controllers\Settings\Payroll;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Actions\Options\GetBranchOptions;
use App\Http\Controllers\AdminBaseController;
use App\Actions\Utility\Setting\GetPayrollSettingMenu;
use App\Services\Settings\Payroll\PayrollComponentService;
use App\Http\Requests\Settings\Payroll\CreatePayrollComponentRequest;
use App\Http\Requests\Settings\Payroll\UpdatePayrollComponentRequest;
use App\Http\Resources\Settings\Payroll\PayrollComponentListResource;
use App\Http\Resources\Settings\Payroll\SubmitPayrollComponentResource;

class PayrollComponentController extends AdminBaseController
{
    public function __construct(
        PayrollComponentService $payrollComponentService,
        GetPayrollSettingMenu $getPayrollSettingMenu,
        GetBranchOptions $getBranchListOptions
    ) {
        $this->payrollComponentService = $payrollComponentService;
        $this->getPayrollSettingMenu = $getPayrollSettingMenu;
        $this->getBranchListOptions = $getBranchListOptions;
    }

    public function getComponentList(Request $request)
    {
        try {
            $data = $this->payrollComponentService->getData($request);

            $result = new PayrollComponentListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createComponent(CreatePayrollComponentRequest $request)
    {
        try {
            $data = $this->payrollComponentService->storeData($request);
            $result = new SubmitPayrollComponentResource($data, 'Success Create Payroll Component');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateComponent($id, UpdatePayrollComponentRequest $request)
    {
        try {
            $data = $this->payrollComponentService->updateData($id, $request);
            $result = new SubmitPayrollComponentResource($data, 'Success Update Payroll Component');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteComponent($id)
    {
        try {
            $data = $this->payrollComponentService->deleteData($id);
            $result = new SubmitPayrollComponentResource($data, 'Success Delete Payroll Component');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function setValueComponent($id)
    {
        return Inertia::render($this->source . 'settings/payroll/payrollComponent/setValue', [
            "title" => 'BattleHR | Setting Payroll',
            "additional" => [
                'menu' => $this->getPayrollSettingMenu->handle(),
                'data' => $this->payrollComponentService->detailData($id),
                'branch_list' => $this->getBranchListOptions->handle(),
            ]
        ]);
    }
}
