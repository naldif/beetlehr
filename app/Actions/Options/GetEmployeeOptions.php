<?php

namespace App\Actions\Options;

use App\Models\Employee;

class GetEmployeeOptions
{
    public function handle($branch_id)
    {
        $employee = Employee::whereHas('user')->with(['user_detail'])->where('branch_id', $branch_id)->get()->pluck('user_detail.name', 'id');
        return $employee;
    }
}
