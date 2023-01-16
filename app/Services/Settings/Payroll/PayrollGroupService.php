<?php

namespace App\Services\Settings\Payroll;

use App\Models\PayrollGroup;

class PayrollGroupService
{
    public function getData($request)
    {
        $search = $request->search;

        $query = PayrollGroup::query();

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });

        return $query->paginate(10);
    }

    public function storeData($request)
    {
        $input = $request->only(['name']);

        $payrollGroup = PayrollGroup::create($input);
        return $payrollGroup;
    }

    public function updateData($id, $request)
    {
        $input = $request->only(['name']);

        $payrollGroup = PayrollGroup::findOrFail($id);
        $payrollGroup->update($input);

        return $payrollGroup;
    }

    public function deleteData($id)
    {
        $payrollGroup = PayrollGroup::findOrFail($id);
        $payrollGroup->delete();

        return $payrollGroup;
    }
}
