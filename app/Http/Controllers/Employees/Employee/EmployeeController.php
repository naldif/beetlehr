<?php

namespace App\Http\Controllers\Employees\Employee;

use Inertia\Inertia;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Actions\Utility\GetFile;
use Illuminate\Support\Facades\DB;
use App\Actions\Options\GetRoleOptions;
use App\Actions\Options\GetBranchOptions;
use App\Actions\Options\GetBpjskNppOptions;
use App\Actions\Options\GetBpjstkNppOptions;
use App\Http\Controllers\AdminBaseController;
use App\Actions\Options\GetDesignationOptions;
use App\Actions\Options\GetPtkpTaxListOptions;
use App\Actions\Options\GetPayrollGroupOptions;
use App\Actions\Options\GetBpjstkComponentOptions;
use App\Actions\Options\GetEmploymentStatusOptions;
use App\Services\Employee\Employee\EmployeeService;
use App\Actions\Utility\Employee\GetDetailEmployeeMenu;
use App\Http\Requests\Employee\Employee\ValidateFinanceRequest;
use App\Http\Resources\Employees\Employee\EmployeeListResource;
use App\Http\Requests\Employee\Employee\ValidateBasicInfoRequest;
use App\Http\Resources\Employees\Employee\SubmitEmployeeResource;
use App\Http\Requests\Employee\Employee\ValidateEmploymentDataRequest;

class EmployeeController extends AdminBaseController
{
    public function __construct(
        EmployeeService $employeeService, GetDesignationOptions $getDesignationOptions,
        GetRoleOptions $getRoleOptions, GetBpjskNppOptions $getBpjskNppOptions,
        GetBpjstkNppOptions $getBpjstkNppOptions, GetBpjstkComponentOptions $getBpjstkComponentOptions,
        GetBranchOptions $getBranchOptions, GetEmploymentStatusOptions $getEmploymentStatusOptions,
        GetPtkpTaxListOptions $getPtkpTaxListOptions, GetPayrollGroupOptions $getPayrollGroupOptions,
        GetDetailEmployeeMenu $getDetailEmployeeMenu
    ){
        $this->employeeService = $employeeService;
        $this->getDesignationOptions = $getDesignationOptions;
        $this->getRoleOptions = $getRoleOptions;
        $this->getBpjskNppOptions = $getBpjskNppOptions;
        $this->getBpjstkNppOptions = $getBpjstkNppOptions;
        $this->getBpjstkComponentOptions = $getBpjstkComponentOptions;
        $this->getBranchOptions = $getBranchOptions;
        $this->getEmploymentStatusOptions = $getEmploymentStatusOptions;
        $this->getPtkpTaxListOptions = $getPtkpTaxListOptions;
        $this->getPayrollGroupOptions = $getPayrollGroupOptions;
        $this->getDetailEmployeeMenu = $getDetailEmployeeMenu;

        // Filter Branches
        $branchOptions = [
            'all' => 'All Branches'
        ];
        foreach ($this->getBranchOptions->handle() as $key => $value) {
            $branchOptions[$key] = $value;
        }

        // Employee List 
        $employeeLists = [];
        $chooses = Employee::with(['branch_detail', 'user_detail'])->get();
        foreach ($chooses as $choose) {
            $employeeLists[$choose->id] = $choose->user_detail->name . ' - ' . $choose->branch_detail->name;
        }

        $this->title = "BattleHR | Employee";
        $this->path = "employees/employee/index";
        $this->data = [
            'designation_list' => $this->getDesignationOptions->handle(),
            'role_list' => $this->getRoleOptions->handle(),
            'employee_list' => $employeeLists,
            'bpjsk_npp' => $this->getBpjskNppOptions->handle(),
            'bpjstk_npp' => $this->getBpjstkNppOptions->handle(),
            'bpjstk_components' => $this->getBpjstkComponentOptions->handle(),
            'branch_list' => $this->getBranchOptions->handle(),
            'employment_list' => $this->getEmploymentStatusOptions->handle(),
            'ptkp_tax_list' => $this->getPtkpTaxListOptions->handle(),
            'employment_data' => $this->getEmploymentStatusOptions->completeHandle(),
            'payroll_group_list' => $this->getPayrollGroupOptions->handle(),
            'employee_settings' => $this->employeeService->getEmployeeSettingRelated(),
            'branches_filter' => $branchOptions
        ];
    }

    public function show($id)
    {
        $getFile = new GetFile();
        $employee = $this->employeeService->getDetailEmployee($id);
        
        // Employee List 
        $employeeLists = [];
        $chooses = Employee::with(['branch_detail', 'user_detail'])->get();
        foreach ($chooses as $choose) {
            $employeeLists[$choose->id] = $choose->user_detail->name . ' - ' . $choose->branch_detail->name;
        }

        return Inertia::render($this->source . 'employees/employee/detail/index', [
            "title" => 'BattleHR | Employee',
            "additional" => [
                'menu' => $this->getDetailEmployeeMenu->handle($id),
                'employee' => $employee,
                'employee_picture' => $employee->image ? $getFile->handle($employee->image)->full_path : asset('img/beetlehr-logo.svg'),
                'employee_role' => $employee->user_detail->getRoleNames(),
                'employee_role_id' => $employee->user_detail->roles()->pluck('id'),
                'designation_list' => $this->getDesignationOptions->handle(),
                'role_list' => $this->getRoleOptions->handle(),
                'bpjsk_npp' => $this->getBpjskNppOptions->handle(),
                'bpjstk_npp' => $this->getBpjstkNppOptions->handle(),
                'bpjstk_components' => $this->getBpjstkComponentOptions->handle(),
                'branch_list' => $this->getBranchOptions->handle(),
                'employment_list' => $this->getEmploymentStatusOptions->handle(),
                'employee_list' => $employeeLists,
                'ptkp_tax_list' => $this->getPtkpTaxListOptions->handle(),
                'employment_data' => $this->getEmploymentStatusOptions->completeHandle(),
                'payroll_group_list' => $this->getPayrollGroupOptions->handle(),
                'employee_settings' => $this->employeeService->getEmployeeSettingRelated()
            ]
        ]);
    }

    public function getData(Request $request)
    {
        try {
            $data = $this->employeeService->getData($request);

            $result = new EmployeeListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function validateBasicInfo(ValidateBasicInfoRequest $request)
    {
        try {
            return $this->respond('Success Validate Basic Info');
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function validateFinance(ValidateFinanceRequest $request)
    {
        try {
            return $this->respond('Success Validate Finance');
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function validateEmploymentData(ValidateEmploymentDataRequest $request)
    {
        try {
            return $this->respond('Success Validate Employment Data');
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createEmployee(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $this->employeeService->storeData($request);

            $result = new SubmitEmployeeResource($data, 'Success Create Employee');
            DB::commit();
            return $this->respond($result);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateEmployee($id, Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $this->employeeService->updateData($id, $request);

            $result = new SubmitEmployeeResource($data, 'Success Update Employee');
            DB::commit();
            return $this->respond($result);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteEmployee($id)
    {
        try {
            DB::beginTransaction();
            $data = $this->employeeService->deleteData($id);

            $result = new SubmitEmployeeResource($data, 'Success Delete Employee');
            DB::commit();
            return $this->respond($result);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->exceptionError($e->getMessage());
        }
    }
}
