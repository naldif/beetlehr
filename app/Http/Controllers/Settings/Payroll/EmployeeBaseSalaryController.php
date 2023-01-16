<?php

namespace App\Http\Controllers\Settings\Payroll;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminBaseController;
use App\Services\Settings\Payroll\EmployeeBaseSalaryService;
use App\Http\Requests\Settings\Payroll\UpdateEmployeeSalaryRequest;
use App\Http\Requests\Settings\Payroll\GenerateEmployeeSalaryRequest;
use App\Http\Resources\Settings\Payroll\EmployeeBaseSalaryListResource;
use App\Http\Resources\Settings\Payroll\SubmitGenerateEmployeeSalaryResource;

class EmployeeBaseSalaryController extends AdminBaseController
{
    public function __construct(EmployeeBaseSalaryService $employeeBaseSalaryService)
    {
        $this->employeeBaseSalaryService = $employeeBaseSalaryService;
    }

    public function getBaseSalaryList(Request $request)
    {
        try {
            $data = $this->employeeBaseSalaryService->getData($request);

            $result = new EmployeeBaseSalaryListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getDesignationOptions(Request $request)
    {
        try {
            $data = $this->employeeBaseSalaryService->getDesignationOptions($request);
            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getEmployeeOptions(Request $request)
    {
        try {
            $data = $this->employeeBaseSalaryService->getEmployeeOptions($request);
            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createBaseSalary(GenerateEmployeeSalaryRequest $request)
    {
        try {
            $data = $this->employeeBaseSalaryService->storeData($request);
            $result = new SubmitGenerateEmployeeSalaryResource($data, 'Success Generate Employee Salaries');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateBaseSalary($id, UpdateEmployeeSalaryRequest $request)
    {
        try {
            $data = $this->employeeBaseSalaryService->updateData($id, $request);
            $result = new SubmitGenerateEmployeeSalaryResource($data, 'Success Update Employee Salary');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteBaseSalary($id)
    {
        try {
            $data = $this->employeeBaseSalaryService->deleteData($id);
            $result = new SubmitGenerateEmployeeSalaryResource($data, 'Success Delete Employee Salary');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
