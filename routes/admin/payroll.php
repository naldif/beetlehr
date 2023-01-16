<?php

use App\Http\Controllers\Payroll\Payroll\PayrollController;


/*
|--------------------------------------------------------------------------
| Payroll Routes
|--------------------------------------------------------------------------
|
| Here is where you can register setting related routes for your application.
|
*/



Route::prefix('payroll')->name('payroll.')->group(function () {
    Route::controller(PayrollController::class)->prefix('run')->name('run.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('get-data', 'getData')->name('getdata');
        Route::post('generate-payroll', 'generatePayroll')->name('generate');
        Route::delete('{id}/delete-slip', 'deleteSlip')->name('deleteslip');
        Route::get('{id}/payroll-employee', 'payrollEmployee')->name('payrollemployee');
        Route::post('{id}/paid-all', 'payrollEmployeePaidAll')->name('payrollemployee.paidall');
        Route::get('{id}/get-payroll-employee-data', 'getPayrollEmployeeData')->name('getpayrollemployeedata');

        Route::get('{id}/payroll-employee/detail', 'payrollEmployeeDetail')->name('payrollemployee.detail');
        Route::get('{id}/payroll-employee/edit', 'payrollEmployeeEdit')->name('payrollemployee.edit');
        Route::put('{id}/payroll-employee/update', 'payrollEmployeeUpdate')->name('payrollemployee.update');
        Route::delete('{id}/payroll-employee/delete', 'payrollEmployeeDelete')->name('payrollemployee.delete');
    });
});
