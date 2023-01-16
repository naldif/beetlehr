<?php

namespace App\Services\Settings\Employee;

use App\Models\Designation;

class DesignationService
{
    public function getData($request)
    {
        $search = $request->search;

        // Get designation
        $query = Designation::query();

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });

        return $query->paginate(10);
    }

    public function storeDesignation($request)
    {
        $input = $request->only(['name']);

        $designation = Designation::create($input);

        return $designation;
    }

    public function updateDesignation($id, $request)
    {
        $input = $request->only(['name']);

        $designation = Designation::findOrFail($id);
        $designation->update($input);

        return $designation;
    }

    public function deleteDesignation($id)
    {
        $designation = Designation::findOrFail($id);
        $designation->delete();

        return $designation;
    }
}
