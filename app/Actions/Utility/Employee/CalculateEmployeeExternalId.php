<?php

namespace App\Actions\Utility\Employee;

use Carbon\Carbon;
use App\Models\Employee;


class CalculateEmployeeExternalId
{
    public function handle($startDate, $employeeOrderId = null)
    {
        if ($employeeOrderId) {
            $incrementalId = $employeeOrderId;
        } else {
            $incrementalId = $this->calculateIncrementalEmployeeId();
        }
        $requiredAdded =  4 - strlen($incrementalId);

        $zero = '';
        $string = $incrementalId;
        for ($i = 0; $i < $requiredAdded; $i++) {
            $zero .= '0';
        }

        $firstFormat = Carbon::parse($startDate)->format('ym');
        $lastFormat = $zero . $string;

        return $firstFormat . $lastFormat;
    }

    public function calculateIncrementalEmployeeId()
    {
        $currentId = 0;
        $latestEmployee = Employee::where('employee_input_order', '!=', null)->orderBy('employee_input_order', 'desc')->first();

        if ($latestEmployee) {
            $incrementalId = $latestEmployee->employee_input_order + 1;
            $currentId += $incrementalId;
        } else {
            $currentId += 1;
        }

        return $currentId;
    }
}
