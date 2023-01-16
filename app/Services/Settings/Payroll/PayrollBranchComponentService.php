<?php

namespace App\Services\Settings\Payroll;

use App\Models\Branch;
use App\Models\PayrollBranchComponent;

class PayrollBranchComponentService
{
    public function getData($request)
    {
        $component_id = $request->component_id;
        $search = $request->search;

        $query = Branch::with(['payroll_branch_components' => function ($q) use ($component_id) {
            $q->where('component_id', $component_id);
        }]);

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });

        return $query->paginate(10);
    }

    public function updateData($request)
    {
        $input = $request->only(['default_amount']);
        $input['status'] = $request->status ? true : false;

        $branchComponent = PayrollBranchComponent::updateOrCreate([
            'branch_id' => $request->branch_id,
            'component_id' => $request->component_id
        ], $input);

        return $branchComponent;
    }

    public function deleteData($id)
    {
        $branchComponent = PayrollBranchComponent::findOrFail($id);
        $branchComponent->delete();

        return $branchComponent;
    }
}
