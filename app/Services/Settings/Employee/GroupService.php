<?php

namespace App\Services\Settings\Employee;

use App\Models\Employee;
use App\Models\EmployeeGroup;
use App\Models\Group;

class GroupService
{
    public function getData($request)
    {
        $search = $request->search;
        $employee_id = Employee::get()->pluck('id');
        $data = Group::with(['employee_groups', 'employee_groups.employee.branch'])->whereHas('employee_groups', function ($query) use ($employee_id) {
            $query->whereIn('employee_id', $employee_id);
        })->when(request('search', false), function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });;

        return $data->paginate(10);
    }

    public function storeGroup($request)
    {
        $group = Group::create(['name' => $request->name]);

        $data = [];
        foreach ($request->employee_id as $key => $employee_id) {
            $data[$key] = [
                'group_id' => $group->id,
                'employee_id' => $employee_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $employeeGroup =  EmployeeGroup::insert($data);

        return $employeeGroup;
    }
    public function updateGroup($id, $request)
    {
        $group = Group::findOrFail($id)->update([
            'name' => $request->name
        ]);

        EmployeeGroup::where('group_id', $id)->delete();

        $data = [];
        foreach ($request->employee_id as $key => $employee_id) {
            $data[$key] = [
                'group_id' => $id,
                'employee_id' => $employee_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $employeeGroup =  EmployeeGroup::insert($data);
        return $employeeGroup;
    }
    public function deleteGroup($id)
    {
        $group = Group::findOrFail($id);
        $group->delete();

        return $group;
    }
}
