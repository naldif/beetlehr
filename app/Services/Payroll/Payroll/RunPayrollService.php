<?php

namespace App\Services\Payroll\Payroll;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\PayrollSlip;
use App\Models\PayrollEmployeeSlip;
use App\Models\PayrollSlipComponent;
use App\Helpers\Utility\Payroll\Calculate;

class RunPayrollService
{
    public function getData($request)
    {
        $search = $request->search;
        $filter_branch = $request->filter_branch;

        // Get Payroll Slip Data
        $query = PayrollSlip::with(['branch_detail', 'created_by_detail', 'employee_slips']);

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->whereHas('branch_detail', function ($qu) use ($search) {
                $qu->where('name', 'like', '%' . $search . '%');
            });
        });
        $query->when(request('filter_branch', false), function ($q) use ($filter_branch) {
            if ($filter_branch !== 'all') {
                $q->where('branch_id', $filter_branch);
            }
        });

        return $query->orderBy('id', 'desc')->paginate(10);
    }

    public function getPayrollEmployeeData($id, $request)
    {
        $search = $request->search;

        // Get Payroll Slip Data
        $query = PayrollEmployeeSlip::with(['employee_detail'])->where('payroll_slip_id', $id);

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->whereHas('employee_detail', function ($qu) use ($search) {
                $qu->whereHas('user_detail', function ($qe) use ($search) {
                    $qe->where('name', 'like', '%' . $search . '%');
                });
            });
        });

        return $query->paginate(10);
    }

    public function getEarningEmployeeSlipComponents($id)
    {
        $earningSlipComponents = PayrollSlipComponent::where('payroll_employee_slip_id', $id)->whereHas('payroll_component_detail', function ($q) {
            $q->where('type', 'earning');
        })->get()->map(function ($q) {
            return [
                'id' => $q->id,
                'payroll_component_id' => $q->payroll_component_id,
                'name' => $q->payroll_component_detail->name,
                'type' => $q->payroll_component_detail->type,
                'is_editable' => $q->payroll_component_detail->is_editable,
                'is_taxable' => $q->payroll_component_detail->is_taxable,
                'value' => (int)$q->value,
                'amount' => number_format($q->value, 2, ',', '.')
            ];
        });
        
        return $earningSlipComponents;
    }

    public function getDeductionEmployeeSlipComponents($employeeSlip)
    {
        $deductionComponents = PayrollSlipComponent::where('payroll_employee_slip_id', $employeeSlip->id)->whereHas('payroll_component_detail', function ($q) {
            $q->where('type', 'deduction');
        })->get()->map(function ($q) {
            return [
                'id' => $q->id,
                'payroll_component_id' => $q->payroll_component_id,
                'name' => $q->payroll_component_detail->name,
                'type' => $q->payroll_component_detail->type,
                'is_editable' => $q->payroll_component_detail->is_editable,
                'is_taxable' => $q->payroll_component_detail->is_taxable,
                'value' => (int)$q->value,
                'amount' => number_format($q->value, 2, ',', '.')
            ];
        });

        $extDeduction = [];

        if($employeeSlip->bpjsk_value){
            $data = [
                'id' => null,
                'name' => 'Bpjs Kesehatan',
                'payroll_component_id' => null,
                'value' => (int)$employeeSlip->bpjsk_value,
                'amount' =>  number_format($employeeSlip->bpjsk_value, 2, ',', '.')
            ];

            array_push($extDeduction, $data);
        } 
        
        if($employeeSlip->jkk) {
            $data = [
                'id' => null,
                'name' => 'Bpjs Jaminan Kecelakaan Kerja',
                'payroll_component_id' => null,
                'value' => (int)$employeeSlip->jkk,
                'amount' =>  number_format($employeeSlip->jkk, 2, ',', '.')
            ];

            array_push($extDeduction, $data);
        } 
        
        if ($employeeSlip->jht) {
            $data = [
                'id' => null,
                'name' => 'Bpjs Jaminan Hari Tua',
                'payroll_component_id' => null,
                'value' => (int)$employeeSlip->jht,
                'amount' =>  number_format($employeeSlip->jht, 2, ',', '.')
            ];

            array_push($extDeduction, $data);
        } 
        
        if ($employeeSlip->jkm) {
            $data = [
                'id' => null,
                'name' => 'Bpjs Jaminan Kematian',
                'payroll_component_id' => null,
                'value' => (int)$employeeSlip->jkm,
                'amount' =>  number_format($employeeSlip->jkm, 2, ',', '.')
            ];

            array_push($extDeduction, $data);
        } 
        
        if ($employeeSlip->jp) {
            $data = [
                'id' => null,
                'name' => 'Bpjs Jaminan Pensiun',
                'payroll_component_id' => null,
                'value' => (int)$employeeSlip->jp,
                'amount' =>  number_format($employeeSlip->jp, 2, ',', '.')
            ];

            array_push($extDeduction, $data);
        } 
        
        if ($employeeSlip->tax_value) {
            $data = [
                'id' => null,
                'name' => 'Pajak PPh21',
                'payroll_component_id' => null,
                'value' => (int)$employeeSlip->tax_value,
                'amount' =>  number_format($employeeSlip->tax_value, 2, ',', '.')
            ];

            array_push($extDeduction, $data);
        }

        return array_merge($extDeduction, $deductionComponents->toArray());
    }

    public function getPayrollEmployeeDetail($id)
    {
        $payrollEmployee = PayrollEmployeeSlip::with(['employee_detail.user_detail', 'employee_detail.designation_detail', 'employee_detail.branch_detail', 'payroll_slip'])->findOrFail($id);
        return $payrollEmployee;
    }

    public function deleteSlip($id)
    {
        $slip = PayrollSlip::findOrFail($id);
        $slip->delete();

        return $slip;
    }

    public function calculatePayroll($branch_id, $start_date, $end_date, $branch_slip = null)
    {
        // Create Payroll Slip
        if ($branch_slip !== null) {
            $employees = Employee::whereHas('payroll_employee_base_salary')->with(['payroll_employee_base_salary'])->get();
            $payroll_slip = $branch_slip;
        } else {
            $employees = Employee::whereHas('payroll_employee_base_salary')->with(['payroll_employee_base_salary'])->where('branch_id', $branch_id)->get();
            $payroll_slip = $this->createSlipPayroll($start_date, $branch_id, $end_date);
        }

        // Create Employee Slip
        $this->createEmployeeSlip($employees, $payroll_slip, $start_date, $end_date);

        // Calculate Earning, Deduction and Final Amount Each Employee
        $employeeSlips = PayrollEmployeeSlip::with(['employee_detail', 'payroll_slip'])->where('payroll_slip_id', $payroll_slip->id)->get();
        Calculate::countAllComponentPayroll($employeeSlips, $start_date, $end_date, $branch_id);

        // Update Slip Total Amount
        $updateTotalAmount = PayrollSlip::find($payroll_slip->id);
        $updateTotalAmount->update([
            'total_amount' => $employeeSlips->sum('total_amount')
        ]);

        return true;
    }

    public function createSlipPayroll($start_date, $branch_id, $end_date)
    {
        $inputPayrolSlips = [
            'date' => $start_date,
            'end_date' => $end_date,
            'branch_id' => $branch_id,
            'created_by' => auth()->user()->id
        ];

        return PayrollSlip::create($inputPayrolSlips);
    }

    public function createSlipPayrollAllPlacement($start_date, $end_date)
    {
        $inputPayrolSlips = [
            'date' => $start_date,
            'end_date' => $end_date,
            'is_all_branch' => true,
            'created_by' => auth()->user()->id
        ];

        return PayrollSlip::create($inputPayrolSlips);
    }

    public function createEmployeeSlip($employees, $payroll_slip, $start_date, $end_date)
    {
        foreach ($employees as $employee) {
            $inputs = [];
            $inputs['employee_id'] = $employee->id;
            $inputs['payroll_slip_id'] = $payroll_slip->id;
            $inputs['status'] = 'Generated';
            if ($employee->payroll_employee_base_salary) {
                $inputs['payroll_employee_base_salary_id'] = $employee->payroll_employee_base_salary->id;
                $inputs['amount'] = Calculate::calculateBaseSalaries($employee, $start_date, $end_date);
            } else {
                throw new \Exception('Didnt have default UMR. Please set all salaries in employee base salaries');
            }

            PayrollEmployeeSlip::create($inputs);
        }
    }

    public function updateStatusEmployeeSlip($id, $request)
    {
        $inputs = $request->only(['paid_on', 'status']);

        $slip = PayrollEmployeeSlip::with(['employee_detail', 'payroll_slip'])->findOrFail($id);
        $slip->update($inputs);

        return $slip;
    }

    public function updateEmployeeSlip($slip, $request)
    {
        // Validate Earning and Deduction if value null
        $earning_components = $request->earningComponents;
        $deduction_components = $request->deductionComponents;
        $earning_value_check = collect($earning_components)->whereNull('value')->count();
        $deduction_value_check = collect($deduction_components)->whereNull('value')->count();

        if($earning_value_check > 0 || $deduction_value_check > 0) {
            throw new \Exception('Please fill all value component before save', 400);
        }

        // Get Requirement Data
        $earning_component_finals = collect($earning_components)->where('payroll_component_id', '!=', null)->where('value', '!=', null)->values()->toArray();
        $deduction_component_finals = collect($deduction_components)->where('payroll_component_id', '!=', null)->where('value', '!=', null)->values()->toArray();
        $taxable_earning_component_finals = collect($earning_components)->where('is_taxable', true)->where('payroll_component_id', '!=', null)->where('value', '!=', null)->values()->toArray();
        $taxable_deduction_component_finals = collect($deduction_components)->where('is_taxable', true)->where('payroll_component_id', '!=', null)->where('value', '!=', null)->values()->toArray();


        $temp_earning = array_unique(array_column($earning_component_finals, 'payroll_component_id'));
        $final_earning = array_intersect_key($earning_component_finals, $temp_earning);
        $temp_deduction = array_unique(array_column($deduction_component_finals, 'payroll_component_id'));
        $final_deduction = array_intersect_key($deduction_component_finals, $temp_deduction);

        $temp_taxable_earning = array_unique(array_column($taxable_earning_component_finals, 'payroll_component_id'));
        $final_taxable_earning = array_intersect_key($taxable_earning_component_finals, $temp_taxable_earning);
        $temp_taxable_deduction = array_unique(array_column($taxable_deduction_component_finals, 'payroll_component_id'));
        $final_taxable_deduction = array_intersect_key($taxable_deduction_component_finals, $temp_taxable_deduction);

        // Calculate Earning, Deduction and Final Amount Each Employee
        Calculate::countUpdatedComponentPayroll(
            $slip, $final_earning, $final_deduction, $final_taxable_earning, $final_taxable_deduction
        );

        // Update Slip Total Amount
        $employeeSlips = PayrollEmployeeSlip::where('payroll_slip_id', $slip->payroll_slip_id)->get();
        $updateTotalAmount = PayrollSlip::find($slip->payroll_slip_id);
        $updateTotalAmount->update([
            'total_amount' => $employeeSlips->sum('total_amount')
        ]);

        return true;
    }

    public function paidAllSlipEmployee($id, $request)
    {
        $slip = PayrollEmployeeSlip::where('payroll_slip_id', $id)->update([
            'paid_on' => $request->paid_date,
            'status' => 'Paid'
        ]);

        return $slip;
    }

    public function deleteSlipEmployee($id)
    {
        $slip = PayrollEmployeeSlip::findOrFail($id);
        $payroll_slip_id = $slip->payroll_slip_id;
        $slip->delete();
        
        // Update Slip Total Amount
        $employeeSlips = PayrollEmployeeSlip::where('payroll_slip_id', $payroll_slip_id)->get();
        $updateTotalAmount = PayrollSlip::find($payroll_slip_id);
        $updateTotalAmount->update([
            'total_amount' => $employeeSlips->sum('total_amount')
        ]);

        return true;
    }
}
