<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Http\Request;

class ContractChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $data = auth('employees')->user();
        $employee = Employee::with(['employment_status_detail'])->where('user_id', $data->id)->first();
        
        // Validate Contract Employee
        $today = Carbon::now()->format('Y-m-d');
        $end_date = Carbon::parse($employee->end_date)->format('Y-m-d');
        if ($employee->employment_status_detail->pkwt_type === 'pkwt' && $today > $end_date) {
            auth('employees')->logout();
            return $this->messageSuccess('Your contract is finished. You was logged out automatically', 200);
        }

        return $next($request);
    }
}
