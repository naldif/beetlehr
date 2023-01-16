<?php

namespace App\Services\Settings\Leave;

use App\Models\LeaveType;

class LeaveTypeService
{
    public function getData($request)
    {
        $search = $request->search;
        $filter_branch = $request->filter_branch;

        // Get company
        $query = LeaveType::with(['branch_detail']);

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });
        $query->when(request('filter_branch', false), function ($q) use ($filter_branch) {
            $q->where('branch_id', $filter_branch);
        });

        return $query->paginate(10);
    }

    public function storeData($request)
    {
        $input = $request->only(['name', 'branch_id', 'quota']);

        $leavetype = LeaveType::create($input);

        return $leavetype;
    }

    public function updateData($id, $request)
    {
        $input = $request->only(['name', 'branch_id', 'quota']);

        $leavetype = LeaveType::findOrFail($id);
        $leavetype->update($input);

        return $leavetype;
    }

    public function deleteData($id)
    {
        $leavetype = LeaveType::findOrFail($id);
        $leavetype->delete();

        return $leavetype;
    }
}
