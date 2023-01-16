<?php

namespace App\Services\Api\V1\Payroll;

use Carbon\Carbon;
use App\Helpers\Utility\Authentication;
use App\Models\PayrollEmployeeSlip;

class PayrollService
{
    public function getData($request)
    {
        // Required Init Data
        $employee = Authentication::getEmployeeLoggedIn();
        $per_page = $request->per_page ? $request->per_page : 10;
        $today = Carbon::now();
        $month = $request->month ?: $today->format('m');
        $year = $request->year ?: $today->format('Y');

        // Get Payroll Slip
        $query = PayrollEmployeeSlip::with(['payroll_slip'])->where('employee_id', $employee->id)->where('status', 'paid');

        // Filter By Params
        $query->when(request('month', false), function ($q) use ($month) {
            $q->whereHas('payroll_slip', function ($qu) use ($month) {
                $qu->whereMonth('created_at', $month);
            });
        });

        $query->when(request('year', false), function ($q) use ($year) {
            $q->whereHas('payroll_slip', function ($qu) use ($year) {
                $qu->whereYear('created_at', $year);
            });
        });

        return $query->paginate($per_page);
    }

    public function getDetail($id)
    {
        $payrollSlip = PayrollEmployeeSlip::with(['employee_detail.user_detail', 'employee_detail.designation_detail'])->findOrFail($id);
        return $payrollSlip;
    }
}
