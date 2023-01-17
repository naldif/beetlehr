<?php

namespace App\Helpers\Utility\Payroll;

use Carbon\Carbon;
use App\Models\Setting;
use App\Models\Attendance;
use App\Models\BpjskSetting;
use App\Models\PphRangeRule;
use App\Models\BpjstkSetting;
use App\Models\BpjstkComponent;
use App\Models\PayrollSlipComponent;
use App\Actions\Utility\Attendance\CalculateAttendanceStatus;
use App\Actions\Utility\Attendance\CalculateAttendanceWorkHours;

class Calculate
{
    public static function getSettingRelated()
    {
        // Get setting related to attendance
        $settings = Setting::whereIn('key', ['payroll_istaxable'])->get(['key', 'value'])->keyBy('key')
        ->transform(function ($setting) {
            return $setting->value;
        })->toArray();

        return $settings;
    }

    public static function calculateBaseSalaries($employee, $startDate, $endDate)
    {
        $type = $employee->payroll_employee_base_salary->type;
        $user_id = $employee->user_id;
        $salary = $employee->payroll_employee_base_salary->amount;

        if ($type === 'hourly') {
            $result = self::calculateHourlySalary($startDate, $endDate, $user_id, $salary);
        } elseif ($type === 'daily') {
            $result = self::calculateDailySalary($startDate, $endDate, $user_id, $salary);
        } else {
            $result = self::calculateMonthlySalary($startDate, $endDate, $salary);
        }

        return $result;
    }

    public static function calculateHourlySalary($startDate, $endDate, $user_id, $salary)
    {
        $attendances = Attendance::whereBetween('date_clock', [$startDate, $endDate])->where('user_id', $user_id)->get();

        $calculateWorkHours = new CalculateAttendanceWorkHours($attendances);
        $totalHours = $calculateWorkHours->calculateTotalWorkHours();

        return $salary * $totalHours;
    }

    public static function calculateDailySalary($startDate, $endDate, $user_id, $salary)
    {
        $attendances = Attendance::whereBetween('date_clock', [$startDate, $endDate])->where('user_id', $user_id)->get();

        $calculateStatus = new CalculateAttendanceStatus($attendances);
        $present = $calculateStatus->calculatePresentStatus();

        return $salary * $present;
    }

    public static function calculateMonthlySalary($startDate, $endDate, $salary)
    {
        $to = Carbon::parse($endDate);
        $from = Carbon::parse($startDate);
        $totalMonth = $to->diffInMonths($from);

        return $salary * $totalMonth;
    }

    public static function countAllComponentPayroll($employeeSlips, $start_date, $end_date, $branch_id)
    {
        $earningTotal = [];
        $deductionTotal = [];
        foreach ($employeeSlips as $employeeSlip) {
            // Get All Component  
            $branch_id = $employeeSlip->employee_detail->branch_id;
            $componentsFinal = Component::getComponentPayroll($branch_id, $employeeSlip->employee_id);
            $taxableComponents = Component::getTaxableComponent($branch_id, $employeeSlip->employee_id);

            foreach ($componentsFinal as $component) {
                $calculateDataComponent = [
                    'component' => $component,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'user_id' => $employeeSlip->employee_detail->user_id,
                    'employee_id' => $employeeSlip->employee_id,
                    'baseSalary' => $employeeSlip->amount,
                    'branch_id' => $branch_id,
                ];

                if ($component->type === 'deduction') {
                    // Count deduction each component and push into array
                    $deductionEachComponent = self::countDeductionEachComponent($calculateDataComponent);
                    $amount = round($deductionEachComponent, 2, PHP_ROUND_HALF_UP);
                    $data = [
                        'employee_id' => $employeeSlip->employee_detail->id,
                        'amount' => $amount
                    ];
                    array_push($deductionTotal, $data);

                    // Push Deduction Component Data
                    $slipComponent = [
                        'payroll_employee_slip_id' => $employeeSlip->id,
                        'payroll_component_id' => $component->id,
                        'value' => $amount
                    ];
                    PayrollSlipComponent::create($slipComponent);
                } else {
                    // Count earning each component and push into array  
                    $earningEachComponent = self::countEarningEachComponent($calculateDataComponent);
                    $amount = round($earningEachComponent, 2, PHP_ROUND_HALF_UP);
                    $data = [
                        'employee_id' => $employeeSlip->employee_detail->id,
                        'amount' => $amount
                    ];
                    array_push($earningTotal, $data);

                    // Push earning Component Data
                    $slipComponent = [
                        'payroll_employee_slip_id' => $employeeSlip->id,
                        'payroll_component_id' => $component->id,
                        'value' => $amount
                    ];

                    PayrollSlipComponent::create($slipComponent);
                }
            }

            $employee_id = $employeeSlip->employee_id;
            $earningResult = self::array_filter_by_value($earningTotal, 'employee_id', $employee_id);
            $deductionResult = self::array_filter_by_value($deductionTotal, 'employee_id', $employee_id);
            $bpjskResult = self::Bpjsk($employeeSlip->employee_detail, $employeeSlip->amount);
            $bpjstkResult = self::Bpjstk($employeeSlip->employee_detail, $employeeSlip->amount);
            $payroll_settings = self::getSettingRelated();

            if ($payroll_settings['payroll_istaxable'] == 1 && $employeeSlip->employee_detail->ptkp_status_detail !== null) {
                $taxableResult = self::countFinalAmountTaxable($taxableComponents, $employeeSlip, $start_date, $end_date, $bpjskResult, $bpjstkResult);
            } else {
                $taxableResult = 0;
            }
            
            $updateAmountEmployeeSlip['tax_value'] = $taxableResult;
            $updateAmountEmployeeSlip['earning_total'] = array_sum(array_column($earningResult, 'amount'));
            $updateAmountEmployeeSlip['deduction_total'] = array_sum(array_column($deductionResult, 'amount')) + $bpjskResult +  $bpjstkResult['JHT'] + $bpjstkResult['JKM'] + $bpjstkResult['JP'] + $bpjstkResult['JKK'] + $taxableResult;
            $updateAmountEmployeeSlip['bpjsk_value'] = $bpjskResult;
            $updateAmountEmployeeSlip['jht'] = $bpjstkResult['JHT'];
            $updateAmountEmployeeSlip['jkm'] = $bpjstkResult['JKM'];
            $updateAmountEmployeeSlip['jp'] = $bpjstkResult['JP'];
            $updateAmountEmployeeSlip['jkk'] = $bpjstkResult['JKK'];

            if ($employeeSlip->amount > 0) {
                $takeHomePay = round(($employeeSlip->amount + $updateAmountEmployeeSlip['earning_total']) - $updateAmountEmployeeSlip['deduction_total'], 2, PHP_ROUND_HALF_UP);
                $updateAmountEmployeeSlip['total_amount'] = $takeHomePay;
            } else {
                $updateAmountEmployeeSlip['total_amount'] = 0;
            }

            $employeeSlip->update($updateAmountEmployeeSlip);
        }
    }

    public static function countUpdatedComponentPayroll(
        $slip, $component_earning, $component_deduction, $taxable_component_earning, $taxable_component_deduction
    ){
        // Delete All Current Slip Component
        PayrollSlipComponent::where('payroll_employee_slip_id', $slip->id)->delete();

        // Create a new income and deduction component
        foreach($component_earning as $earning) {
            $slipComponent = [
                'payroll_employee_slip_id' => $slip->id,
                'payroll_component_id' => $earning['payroll_component_id'],
                'value' => $earning['value']
            ];
            PayrollSlipComponent::create($slipComponent);
        }

        foreach ($component_deduction as $deduction) {
            $slipComponent = [
                'payroll_employee_slip_id' => $slip->id,
                'payroll_component_id' => $deduction['payroll_component_id'],
                'value' => $deduction['value']
            ];
            PayrollSlipComponent::create($slipComponent);
        }

        $earningResult = collect($component_earning)->sum('value');
        $deductionResult = collect($component_deduction)->sum('value');
        $earningTaxableResult = collect($taxable_component_earning)->sum('value');
        $deductionTaxableResult = collect($taxable_component_deduction)->sum('value');
        $bpjskResult = self::Bpjsk($slip->employee_detail, $slip->amount);
        $bpjstkResult = self::Bpjstk($slip->employee_detail, $slip->amount);
        $payroll_settings = self::getSettingRelated();

        if ($payroll_settings['payroll_istaxable'] == 1 && $slip->employee_detail->ptkp_status_detail !== null) {
            $taxableAmount = round(($slip->amount + $earningTaxableResult) - ($deductionTaxableResult + $bpjskResult +  $bpjstkResult['JHT'] + $bpjstkResult['JKM'] + $bpjstkResult['JP'] + $bpjstkResult['JKK']), 2, PHP_ROUND_HALF_UP);
            $taxableResult = self::PPh21($slip->employee_detail, $taxableAmount);
        } else {
            $taxableResult = 0;
        }
        
        $updateAmountEmployeeSlip['tax_value'] = $taxableResult;
        $updateAmountEmployeeSlip['earning_total'] = $earningResult;
        $updateAmountEmployeeSlip['deduction_total'] = $deductionResult + $bpjskResult +  $bpjstkResult['JHT'] + $bpjstkResult['JKM'] + $bpjstkResult['JP'] + $bpjstkResult['JKK'] + $taxableResult;
        $updateAmountEmployeeSlip['bpjsk_value'] = $bpjskResult;
        $updateAmountEmployeeSlip['jht'] = $bpjstkResult['JHT'];
        $updateAmountEmployeeSlip['jkm'] = $bpjstkResult['JKM'];
        $updateAmountEmployeeSlip['jp'] = $bpjstkResult['JP'];
        $updateAmountEmployeeSlip['jkk'] = $bpjstkResult['JKK'];

        if ($slip->amount > 0) {
            $takeHomePay = round(($slip->amount + $updateAmountEmployeeSlip['earning_total']) - $updateAmountEmployeeSlip['deduction_total'], 2, PHP_ROUND_HALF_UP);
            $updateAmountEmployeeSlip['total_amount'] = $takeHomePay;
        } else {
            $updateAmountEmployeeSlip['total_amount'] = 0;
        }

        $slip->update($updateAmountEmployeeSlip);
    }

    public static function Bpjsk($employee, $umr = 0)
    {
        if ($employee->is_use_bpjsk) {
            $bpjsk = BpjskSetting::find($employee->bpjsk_setting_id);
            $employeePrecentage = env('BPJSK_EMPLOYEE_PRECENTAGE', 1);

            if ($employee->is_use_bpjsk_specific_amount) {
                $bpjskAmount = round(($employeePrecentage * $employee->bpjsk_specific_amount) / 100, 2, PHP_ROUND_HALF_UP);
            } elseif ($bpjsk->minimum_value) {
                $bpjskAmount = round(($employeePrecentage * $bpjsk->minimum_value) / 100, 2, PHP_ROUND_HALF_UP);
            } else {
                $bpjskAmount = round(($employeePrecentage * $umr) / 100, 2, PHP_ROUND_HALF_UP);
            }
        } else {
            $bpjskAmount = null;
        }

        return $bpjskAmount;
    }

    public static function Bpjstk($employee, $umr = 0)
    {
        $bpjstkResult = [];
        if ($employee->is_use_bpjstk) {
            $bpjstk = BpjstkSetting::find($employee->bpjstk_setting_id);
            $bpjstkComponent = BpjstkComponent::get();

            // Define Base Amount
            if ($employee->is_use_bpjstk_specific_amount) {
                $amount = $employee->bpjstk_specific_amount;
            } elseif ($bpjstk->minimum_value) {
                $amount = $bpjstk->minimum_value;
            } else {
                $amount = $umr;
            }

            // Calculate JHT (Jaminan Hari Tua)
            if ($employee->bpjstk_old_age && $bpjstk->old_age) {
                $oldAgePrecentage = collect($bpjstkComponent)->filter(function ($item) {
                    return $item->key_name == 'old_age';
                })->first()->employee_precentage;

                $bpjstkResult['JHT'] = round(($oldAgePrecentage * $amount) / 100, 2, PHP_ROUND_HALF_UP);
            } else {
                $bpjstkResult['JHT'] = null;
            }

            // Calculate JKM (Jaminan Kematian)
            if ($employee->bpjstk_life_insurance && $bpjstk->life_insurance) {
                $lifeInsurancePrecentage = collect($bpjstkComponent)->filter(function ($item) {
                    return $item->key_name == 'life_insurance';
                })->first()->company_precentage;

                $bpjstkResult['JKM'] = round(($lifeInsurancePrecentage * $amount) / 100, 2, PHP_ROUND_HALF_UP);
            } else {
                $bpjstkResult['JKM'] = null;
            }

            // Calculate JP (Jaminan Pension)
            if ($employee->bpjstk_pension_time && $bpjstk->pension_time) {
                $pensionTimePrecentage = collect($bpjstkComponent)->filter(function ($item) {
                    return $item->key_name == 'pension_time';
                })->first()->employee_precentage;

                $bpjstkResult['JP'] = round(($pensionTimePrecentage * $amount) / 100, 2, PHP_ROUND_HALF_UP);
            } else {
                $bpjstkResult['JP'] = null;
            }

            // Calculate JKK (Jaminan Kecelakaan Kerja)
            if ($bpjstk->bpjstk_risk_level_id) {
                $bpjstkResult['JKK'] = round(($bpjstk->bpjstk_risk_level->precentage * $amount) / 100, 2, PHP_ROUND_HALF_UP);
            } else {
                $bpjstkResult['JKK'] = null;
            }
        } else {
            $bpjstkResult['JHT'] = null;
            $bpjstkResult['JKM'] = null;
            $bpjstkResult['JP'] = null;
            $bpjstkResult['JKK'] = null;
        }

        return $bpjstkResult;
    }

    public static function PPh21($employee, $salary)
    {
        $salaryYear = $salary * 12;

        if ($employee->ptkp_tax_list_id) {
            $finalTax = 0;
            $pphRules = PphRangeRule::get();
            $salaryTaxable = $salaryYear - $employee->ptkp_status_detail->value;

            foreach ($pphRules as $rule) {
                if ($salaryTaxable > 0) {
                    if ($salaryTaxable > $rule->end_range) {
                        $finalTax += ($rule->percentage * $rule->end_range) / 100;
                    } else {
                        $finalTax += ($rule->percentage * $salaryTaxable) / 100;
                    }
                }

                $salaryTaxable = $salaryTaxable - $rule->end_range;
            }

            $finalTax = round($finalTax / 12, 2, PHP_ROUND_HALF_UP);
        } else {
            $finalTax = 0;
        }

        return $finalTax;
    }

    public static function countFinalAmountTaxable($taxableComponents, $employeeSlip, $start_date, $end_date, $bpjskResult, $bpjstkResult)
    {
        $earningTotal = [];
        $deductionTotal = [];

        foreach ($taxableComponents as $component) {
            $calculateDataComponent = [
                'component' => $component,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'user_id' => $employeeSlip->employee_detail->user_id,
                'employee_id' => $employeeSlip->employee_id,
                'baseSalary' => $employeeSlip->amount,
                'branch_id' => $employeeSlip->employee_detail->branch_id,
            ];

            if ($component->type == 'Deduction') {
                // Count deduction each component and push into array
                $deductionEachComponent = self::countDeductionEachComponent($calculateDataComponent);
                $data = [
                    'employee_id' => $employeeSlip->employee_detail->id,
                    'amount' => round($deductionEachComponent, 2, PHP_ROUND_HALF_UP)
                ];
                array_push($deductionTotal, $data);
            } else {
                // Count earning each component and push into array  
                $earningEachComponent = self::countEarningEachComponent($calculateDataComponent);
                $data = [
                    'employee_id' => $employeeSlip->employee_detail->id,
                    'amount' => round($earningEachComponent, 2, PHP_ROUND_HALF_UP)
                ];
                array_push($earningTotal, $data);
            }
        }

        $employee_id = $employeeSlip->employee_detail->id;
        $earningResult = self::array_filter_by_value($earningTotal, 'employee_id', $employee_id);
        $deductionResult = self::array_filter_by_value($deductionTotal, 'employee_id', $employee_id);

        $finalEarningTotal = array_sum(array_column($earningResult, 'amount'));
        $finalDeductionTotal = array_sum(array_column($deductionResult, 'amount'));

        if ($employeeSlip->amount > 0) {
            $bpjskValue = $bpjskResult ? $bpjskResult : 0;
            $jhtValue = $bpjstkResult['JHT'] ? $bpjstkResult['JHT'] : 0;
            $jkmValue = $bpjstkResult['JKM'] ? $bpjstkResult['JKM'] : 0;
            $jpValue = $bpjstkResult['JP'] ? $bpjstkResult['JP'] : 0;
            $jkkValue = $bpjstkResult['JKK'] ? $bpjstkResult['JKK'] : 0;

            $finalAmount = round(($employeeSlip->amount + $finalEarningTotal) - ($finalDeductionTotal + $bpjskValue + $jhtValue + $jkmValue + $jpValue + $jkkValue), 2, PHP_ROUND_HALF_UP);
        } else {
            $finalAmount = 0;
        }

        return self::PPh21($employeeSlip->employee_detail, $finalAmount);
    }

    public static function array_filter_by_value($my_array, $index, $value)
    {
        $new_array = [];
        if (is_array($my_array) && count($my_array) > 0) {
            foreach (array_keys($my_array) as $key) {
                $temp[$key] = $my_array[$key][$index];

                if ($temp[$key] == $value) {
                    $new_array[$key] = $my_array[$key];
                }
            }
        }
        return $new_array;
    }

    public static function countEarningEachComponent($calculateDataComponent)
    {
        // Init Required Data
        $component = $calculateDataComponent['component'];
        $employee_id = $calculateDataComponent['employee_id'];
        $branch_id = $calculateDataComponent['branch_id'];
        
        $amount = self::calculateDefaultAmountComponent($component, $employee_id, $branch_id);

        return $amount;
    }

    public static function countDeductionEachComponent($calculateDataComponent)
    {
        // Init Required Data
        $component = $calculateDataComponent['component'];
        $start_date = $calculateDataComponent['start_date'];
        $end_date = $calculateDataComponent['end_date'];
        $user_id = $calculateDataComponent['user_id'];
        $employee_id = $calculateDataComponent['employee_id'];
        $branch_id = $calculateDataComponent['branch_id'];

        if ($component->name === 'Potongan Terlambat' && $component->is_mandatory === 1) {
            $customAttributeLate = $component->custom_attribute;

            if ($customAttributeLate) {
                $formattedLate = $customAttributeLate['late_tolerance'] < 10 ? '0' . (string)$customAttributeLate['late_tolerance'] : $customAttributeLate['late_tolerance'];
                $toleranceLate = Carbon::createFromFormat('i', $formattedLate)->format('H:i:s');
                $totalCountableLate = Attendance::whereBetween('date_clock', [$start_date, $end_date])->where('user_id', $user_id)
                    ->where('is_late_clock_in', 1)->where('total_late_clock_in', '>', $toleranceLate)
                    ->count();

                $componentAmount = self::calculateDefaultAmountComponent($component, $employee_id, $branch_id);
                $amount = $componentAmount * $totalCountableLate;
            } else {
                $amount = 0;
            }
        } else {
            $amount = self::calculateDefaultAmountComponent($component, $employee_id, $branch_id);
        }

        return $amount;
    }

    public static function calculateDefaultAmountComponent($component, $employee_id, $branch_id)
    {
        $employeeComponent = $component->employee_components ? $component->employee_components->where('employee_id', $employee_id)->where('status', true)->first() : null;
        $branchComponent = $component->branch_components ? $component->branch_components->where('branch_id', $branch_id)->where('status', true)->first() : null;
        $employeeComponentCheck = isset($employeeComponent) && $employeeComponent->default_amount !== null;
        $branchComponentCheck = isset($branchComponent) && $branchComponent->default_amount !== null;

        if($employeeComponentCheck) {
            return $employeeComponent->default_amount;
        }elseif ($branchComponentCheck) {
            return $branchComponent->default_amount;
        }else{
            return 0;
        }
    }
}
