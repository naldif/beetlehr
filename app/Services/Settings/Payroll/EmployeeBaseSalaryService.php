<?php

namespace App\Services\Settings\Payroll;

use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\LeaveQuota;
use App\Models\Designation;
use App\Models\EmployeeBaseSalary;

class EmployeeBaseSalaryService
{
    public function getData($request)
    {
        $search = $request->search;
        $filter_branch = $request->filter_branch;
        $filter_designation = $request->filter_designation;

        $query = EmployeeBaseSalary::whereHas('employee')->with(['employee_detail']);

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->whereHas('employee_detail.user_detail', function ($qu) use ($search) {
                $qu->where('name', 'like', '%' . $search . '%');
            });
        });
        $query->when(request('filter_branch', false), function ($q) use ($filter_branch) {
            $q->whereHas('employee', function ($qu) use ($filter_branch) {
                $qu->where('branch_id', $filter_branch);
            });
        });
        $query->when(request('filter_designation', false), function ($q) use ($filter_designation) {
            $q->whereHas('employee', function ($qu) use ($filter_designation) {
                $qu->where('designation_id', $filter_designation);
            });
        });

        return $query->paginate(10);
    }

    public function getDesignationOptions($request)
    {
        $branch_id = $request->branch_id;

        $query = Designation::query();

        // Filter By Params
        $query->when(request('branch_id', false), function ($q) use ($branch_id) {
            if ($branch_id !== 'all') {
                $q->whereHas('employees', function ($qu) use ($branch_id) {
                    $qu->where('branch_id', $branch_id);
                });
            }
        });

        $designationOptions = [
            'all' => 'All Designations'
        ];
        foreach ($query->get()->pluck('name', 'id') as $key => $value) {
            $designationOptions[$key] = $value;
        }

        return $designationOptions;
    }

    public function getEmployeeOptions($request)
    {
        $search = $request->search;
        $branch_id = $request->branch_id;
        $designation_id = $request->designation_id;

        $query = Employee::with(['user_detail']);

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->whereHas('user_detail', function ($qu) use ($search) {
                $qu->where('name', 'like', '%' . $search . '%');
            });
        });
        $query->when(request('branch_id', false), function ($q) use ($branch_id) {
            if ($branch_id !== 'all') {
                $q->where('branch_id', $branch_id);
            }
        });
        $query->when(request('designation_id', false), function ($q) use ($designation_id) {
            if ($designation_id !== 'all') {
                $q->where('designation_id', $designation_id);
            }
        });

        $employeeOptions = [];
        foreach ($query->get() as $employee) {
            $employeeOptions[$employee->id] = $employee->user_detail->name;
        }

        return $employeeOptions;
    }

    public function storeData($request)
    {
        $input = $request->only(['type', 'amount']);
        $employee_id = $request->employee_id;
        $branch_id = $request->branch_id;
        $designation_id = $request->designation_id;

        $query = Employee::query();

        // Filter By Params
        $query->when(request('employee_id', false), function ($q) use ($employee_id) {
            $q->whereIn('id', $employee_id);
        });
        $query->when(request('branch_id', false), function ($q) use ($branch_id) {
            if ($branch_id !== 'all') {
                $q->where('branch_id', $branch_id);
            }
        });
        $query->when(request('designation_id', false), function ($q) use ($designation_id) {
            if ($designation_id !== 'all') {
                $q->where('designation_id', $designation_id);
            }
        });

        $employees = $query->get()->pluck('id');

        foreach ($employees as $key => $value) {
            $input['employee_id'] = $value;
            EmployeeBaseSalary::updateOrCreate([
                'employee_id' => $value
            ], $input);
        }

        return true;
    }

    public function updateData($id, $request)
    {
        $input = $request->only(['type', 'amount']);

        $employeeSalary = EmployeeBaseSalary::findOrFail($id);
        $employeeSalary->update($input);

        return $employeeSalary;
    }

    public function deleteData($id)
    {
        $employeeSalary = EmployeeBaseSalary::findOrFail($id);
        $employeeSalary->delete();

        return $employeeSalary;
    }
}
