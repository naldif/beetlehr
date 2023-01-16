<?php

namespace App\Services\Settings\Payroll;

use App\Models\Employee;
use App\Models\PayrollEmployeeComponent;

class PayrollEmployeeComponentService
{
    public function getData($request)
    {
        $component_id = $request->component_id;
        $search = $request->search;
        $filter_branch = $request->filter_branch;

        $query = Employee::with(['payroll_employee_components' => function ($q) use ($component_id) {
            $q->where('component_id', $component_id);
        }]);

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->whereHas('user_detail', function ($qu) use ($search) {
                $qu->where('name', 'like', '%' . $search . '%');
            });
        });
        $query->when(request('filter_branch', false), function ($q) use ($filter_branch) {
            $q->where('branch_id', $filter_branch);
        });

        return $query->paginate(10);
    }

    public function updateData($request)
    {
        $input = $request->only(['default_amount']);
        $input['status'] = $request->status ? true : false;

        $employeeComponent = PayrollEmployeeComponent::updateOrCreate([
            'employee_id' => $request->employee_id,
            'component_id' => $request->component_id
        ], $input);

        return $employeeComponent;
    }

    public function deleteData($id)
    {
        $employeeComponent = PayrollEmployeeComponent::findOrFail($id);
        $employeeComponent->delete();

        return $employeeComponent;
    }
}
