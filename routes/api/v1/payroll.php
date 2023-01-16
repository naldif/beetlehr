<?php

use App\Http\Controllers\Api\V1\Payroll\PayrollController;

/*
|--------------------------------------------------------------------------
| Payroll Routes
|--------------------------------------------------------------------------
|
| Here is where you can register payroll related routes for your application.
|
*/



Route::controller(PayrollController::class)->group(function () {
    Route::get('payroll', 'getPayrollList');
    Route::get('payroll/{id}', 'getDetailPayroll');
});
