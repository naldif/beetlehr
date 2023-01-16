<?php

namespace App\Actions\Utility\Payroll;

use App\Services\Payroll\Payroll\RunPayrollService;


class GetEmployeeSlipComponent
{
    public function __construct()
    {
        $this->payrollService = new RunPayrollService();
    }

    public function handle($slip)
    {
        return [
            'earnings' => $this->getEarningComponents($slip),
            'deductions' => $this->getDeductionComponents($slip)
        ];
    }

    public function getEarningComponents($slip)
    {
        $baseSalary = [
            [
                'name' => 'Base Salary',
                'amount' => number_format($slip->amount, 2, '.', '')
            ]
        ];

        $component_earnings = $this->payrollService->getEarningEmployeeSlipComponents($slip->id);
        $earning_results = collect($component_earnings)->map(function ($q) {
            return [
                'name' => $q['name'],
                'amount' => number_format($q['value'], 2, '.', '')
            ];
        })->toArray();

        return array_merge($baseSalary, $earning_results);
    }

    public function getDeductionComponents($slip)
    {
        $component_deductions = $this->payrollService->getDeductionEmployeeSlipComponents($slip);
        $deduction_results = collect($component_deductions)->map(function ($q) {
            return [
                'name' => $q['name'],
                'amount' => number_format($q['value'], 2, '.', '')
            ];
        })->toArray();

        return $deduction_results;
    }
}
