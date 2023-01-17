<?php

namespace App\Http\Controllers\Payroll\Payroll;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;
use App\Actions\Options\GetBranchOptions;
use App\Helpers\Utility\Payroll\Calculate;
use App\Helpers\Utility\Payroll\Component;
use App\Http\Controllers\AdminBaseController;
use App\Services\Payroll\Payroll\RunPayrollService;
use App\Http\Requests\Payroll\Payroll\GeneratePayroll;
use App\Http\Requests\Payroll\Payroll\PaidAllSlipRequest;
use App\Http\Resources\Payroll\Payroll\SubmitPayrollResource;
use App\Http\Requests\Payroll\Payroll\UpdatePayrollSlipRequest;
use App\Http\Resources\Payroll\Payroll\PayrollSlipListResource;
use App\Http\Resources\Payroll\Payroll\PayrollEmployeeListResource;

class PayrollController extends AdminBaseController
{
    public function __construct(
        GetBranchOptions $getBranchOptions,
        RunPayrollService $runPayrollService,
        FileService $fileService
    ) {
        $this->getBranchOptions = $getBranchOptions;
        $this->runPayrollService = $runPayrollService;
        $this->fileService = $fileService;

        // Filter Branches
        $branchOptions = [
            'all' => 'All Branches'
        ];
        foreach ($this->getBranchOptions->handle() as $key => $value) {
            $branchOptions[$key] = $value;
        }

        $this->title = "BattleHR | Payroll";
        $this->path = "payroll/payroll/index";
        $this->data = [
            'branches_list' => $branchOptions
        ];
    }

    public function getData(Request $request)
    {
        try {
            $data = $this->runPayrollService->getData($request);

            $result = new PayrollSlipListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function generatePayroll(GeneratePayroll $request)
    {
        try {
            $dates = collect($request->date)->map(function ($q) {
                return Carbon::parse($q)->format('Y-m-d');
            })->toArray();

            // Calculate payroll base on branch selected
            if ($request->branch_id != 'all') {
                DB::beginTransaction();
                
                $this->runPayrollService->calculatePayroll($request->branch_id, $dates[0], $dates[1]);

                DB::commit();
            } else {
                DB::beginTransaction();

                $payroll_slip = $this->runPayrollService->createSlipPayrollAllPlacement($dates[0], $dates[1]);

                $this->runPayrollService->calculatePayroll(null, $dates[0], $dates[1], $payroll_slip);

                DB::commit();
            }

            $result = new SubmitPayrollResource(true, 'Success Generate Payroll');
            return $this->respond($result);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteSlip($id)
    {
        try {
            $data = $this->runPayrollService->deleteSlip($id);

            $result = new SubmitPayrollResource($data, 'Success Delete Payroll');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function payrollEmployee($id, Request $request)
    {
        return Inertia::render($this->source . 'payroll/payroll/payrollemployee/index', [
            "title" => 'BattleHR | Payroll',
            "additional" => [
                'payroll_slip_id' => $id
            ]
        ]);
    }

    public function getPayrollEmployeeData($id, Request $request)
    {
        try {
            $data = $this->runPayrollService->getPayrollEmployeeData($id, $request);

            $result = new PayrollEmployeeListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function payrollEmployeeDetail($id)
    {
        $payrollEmployee = $this->runPayrollService->getPayrollEmployeeDetail($id);
        return Inertia::render($this->source . 'payroll/payroll/payrollemployee/detail', [
            "title" => 'BattleHR | Payroll',
            "additional" => [
                'payroll_employee' => $payrollEmployee,
                'payroll_date' => Carbon::parse($payrollEmployee->payroll_slip->date)->format('F Y'),
                'base_salary' => number_format($payrollEmployee->amount, 2, ',', '.'),
                'total_earning' => number_format($payrollEmployee->earning_total + $payrollEmployee->amount, 2, ',', '.'),
                'total_deduction' => number_format($payrollEmployee->deduction_total, 2, ',', '.'),
                'take_home_pay' => number_format($payrollEmployee->total_amount, 2, ',', '.'),
                'earning_components' => $this->runPayrollService->getEarningEmployeeSlipComponents($id),
                'deduction_components' => $this->runPayrollService->getDeductionEmployeeSlipComponents($payrollEmployee)
            ]
        ]);
    }

    public function payrollEmployeeEdit($id)
    {
        $payrollEmployee = $this->runPayrollService->getPayrollEmployeeDetail($id);
        $payrollComponent = Component::getComponentPayroll($payrollEmployee->employee_detail->branch_id, $payrollEmployee->employee_id);

        return Inertia::render($this->source . 'payroll/payroll/payrollemployee/edit', [
            "title" => 'BattleHR | Payroll',
            "additional" => [
                'payroll_employee' => $payrollEmployee,
                'payroll_date' => Carbon::parse($payrollEmployee->payroll_slip->date)->format('F Y'),
                'base_salary' => number_format($payrollEmployee->amount, 2, ',', '.'),
                'total_earning' => number_format($payrollEmployee->earning_total + $payrollEmployee->amount, 2, ',', '.'),
                'total_deduction' => number_format($payrollEmployee->deduction_total, 2, ',', '.'),
                'take_home_pay' => number_format($payrollEmployee->total_amount, 2, ',', '.'),
                'earning_components' => $this->runPayrollService->getEarningEmployeeSlipComponents($id),
                'deduction_components' => $this->runPayrollService->getDeductionEmployeeSlipComponents($payrollEmployee),
                'payroll_earning_components' => collect($payrollComponent)->where('type', 'earning')->values()->map(function($q) use($payrollEmployee) {
                    $value = Calculate::calculateDefaultAmountComponent($q, $payrollEmployee->employee_id, $payrollEmployee->employee_detail->branch_id);

                    return [
                        'id' => $q->id,
                        'name' => $q->name,
                        'is_editable' => $q->is_editable,
                        'is_taxable' => $q->is_taxable,
                        'type' => $q->type,
                        'value' => (int) $value,
                        'amount' => number_format($value, 2, ',', '.')
                    ];
                }),
                'payroll_deduction_components' => collect($payrollComponent)->where('type', 'deduction')->values()->map(function ($q) use ($payrollEmployee) {
                    $value = Calculate::calculateDefaultAmountComponent($q, $payrollEmployee->employee_id, $payrollEmployee->employee_detail->branch_id);

                    return [
                        'id' => $q->id,
                        'name' => $q->name,
                        'is_editable' => $q->is_editable,
                        'is_taxable' => $q->is_taxable,
                        'type' => $q->type,
                        'value' => (int) $value,
                        'amount' => number_format($value, 2, ',', '.')
                    ];
                })
            ]
        ]);
    }

    public function payrollEmployeeUpdate($id, UpdatePayrollSlipRequest $request)
    {
        try {
            $slip = $this->runPayrollService->updateStatusEmployeeSlip($id, $request);
            $this->runPayrollService->updateEmployeeSlip($slip, $request);          

            $result = new SubmitPayrollResource(true, 'Success Update Payroll');
            return $this->respond($result);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->exceptionError($e->getMessage());
        }
    }

    public function payrollEmployeePaidAll($id, PaidAllSlipRequest $request)
    {
        try {
            $this->runPayrollService->paidAllSlipEmployee($id, $request);

            $result = new SubmitPayrollResource(true, 'Success Paid All Slip Payroll');
            return $this->respond($result);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->exceptionError($e->getMessage());
        }
    }

    public function payrollEmployeeDelete($id)
    {
        try {
            $this->runPayrollService->deleteSlipEmployee($id);

            $result = new SubmitPayrollResource(true, 'Success Delete Slip Payroll');
            return $this->respond($result);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->exceptionError($e->getMessage());
        }
    }
}
