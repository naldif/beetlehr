<?php

namespace App\Services\Settings\Payroll;

use App\Models\PayrollComponent;

class PayrollComponentService
{
    public function getData($request)
    {
        $search = $request->search;

        $query = PayrollComponent::query();

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });

        return $query->paginate(10);
    }

    public function detailData($id)
    {
        $payrollComponent = PayrollComponent::findOrFail($id);

        return $payrollComponent;
    }

    public function storeData($request)
    {
        $input = $request->only(['name', 'type', 'is_editable', 'is_taxable']);

        $payrollComponent = PayrollComponent::create($input);

        return $payrollComponent;
    }

    public function updateData($id, $request)
    {
        $payrollComponent = PayrollComponent::findOrFail($id);

        if($request->action === 'deduction_late') {
            $input = $request->only(['is_editable', 'is_taxable']);
            $input['custom_attribute']['action'] = $payrollComponent->custom_attribute['action'];
            $input['custom_attribute']['late_tolerance'] = $request->late_tolerance;
        }else if ($request->action === 'earning_overtime') {
            $input = $request->only(['is_editable', 'is_taxable']);
        }else {
            $input = $request->only(['name', 'is_editable', 'is_taxable']);
        }

        $payrollComponent->update($input);

        return $payrollComponent;
    }

    public function deleteData($id)
    {
        $payrollComponent = PayrollComponent::findOrFail($id);
        $payrollComponent->delete();

        return $payrollComponent;
    }
}
