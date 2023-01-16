<?php

use App\Http\Controllers\Employees\Employee\EmployeeController;
use App\Http\Controllers\Employees\Employee\AttendanceLogController;
use App\Http\Controllers\Employees\Resign\ResignManagementController;


/*
|--------------------------------------------------------------------------
| Employee Routes
|--------------------------------------------------------------------------
|
| Here is where you can register setting related routes for your application.
|
*/

Route::prefix('employment')->name('employment.')->group(function () {
    Route::controller(EmployeeController::class)->prefix('employee')->name('employee.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('get-data', 'getData')->name('getdata');
        Route::post('validate-basic-info', 'validateBasicInfo')->name('validateinfo');
        Route::post('validate-finance', 'validateFinance')->name('validatefinance');
        Route::post('validate-employment-data', 'validateEmploymentData')->name('validateemploymentdata');
        Route::post('create', 'createEmployee')->name('create');
        Route::get('{id}', 'show')->name('show');
        Route::post('{id}/update', 'updateEmployee')->name('update');
        Route::delete('{id}', 'deleteEmployee')->name('delete');

        Route::controller(AttendanceLogController::class)->prefix('attendance-log')->name('attendance-log.')->group(function () {
            Route::get('{id}', 'attendanceLogIndex')->name('index');
            Route::get('{id}/get-data', 'getData')->name('getdata');
            Route::get('{id}/get-attendance-overview', 'getAttendanceLogOverviewData')->name('getdataoverview');
        });
    });

    Route::controller(ResignManagementController::class)->prefix('resign-management')->name('resign-management.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('get-data', 'getData')->name('getdata');
        Route::get('/{id}/download-resign-file', 'downloadResignFile')->name('downloadfile');
        Route::post('create', 'createResign')->name('create');
        Route::put('{id}/update', 'updateStatus')->name('update');
        Route::delete('{id}', 'deleteResign')->name('delete');
    });
});
