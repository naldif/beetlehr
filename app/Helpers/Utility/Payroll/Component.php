<?php

namespace App\Helpers\Utility\Payroll;

use App\Models\PayrollComponent;


class Component
{
    public static function getComponentPayroll($branch_id, $employee_id, $component_finals_id = [])
    {
        if(count($component_finals_id) > 0) {
            $components = PayrollComponent::whereIn('id', $component_finals_id)->get();
        }else{
            $components = PayrollComponent::whereHas('employee_components', function ($q) use ($employee_id) {
                $q->where('employee_id', $employee_id)->where('status', true);
            })->orWhereHas('branch_components', function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id)->where('status', true);
            })->get();
        }
 
        return $components;
    }

    public static function getTaxableComponent($branch_id, $employee_id, $component_finals_id = [])
    {
        if (count($component_finals_id) > 0) {
            $components = PayrollComponent::whereIn('id', $component_finals_id)->where('is_taxable', true)->get();
        } else {
            $components = PayrollComponent::whereHas('employee_components', function ($q) use ($employee_id) {
                $q->where('employee_id', $employee_id)->where('status', true);
            })->orWhereHas('branch_components', function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id)->where('status', true);
            })->where('is_taxable', true)->get();
        }

        return $components;
    }
}
