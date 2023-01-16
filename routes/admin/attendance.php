<?php

use App\Http\Controllers\Attendance\Schedule\ScheduleController;
use App\Http\Controllers\Attendance\Shift\ShiftController;
use App\Http\Controllers\Attendance\Attendance\AttendanceOverviewController;


/*
|--------------------------------------------------------------------------
| Attendance Routes
|--------------------------------------------------------------------------
|
| Here is where you can register setting related routes for your application.
|
*/



Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::controller(ShiftController::class)->prefix('shift')->name('shift.')->group(function () {
        Route::get('/', 'getShiftIndex')->name('index');
        Route::get('get-data', 'getShiftList')->name('getdata');
        Route::post('create-data', 'createShift')->name('create');
        Route::put('{id}/update-data', 'editShift')->name('update');
        Route::delete('{id}/delete-data', 'deleteShift')->name('delete');
    });

    Route::controller(AttendanceOverviewController::class)->prefix('attendance')->name('attendance-overview.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('get-attendance-header', 'getAttendanceListDate')->name('getattendanceheader');
        Route::get('get-data', 'getAttendanceList')->name('getdata');
        Route::get('get-attendance-recap', 'getAttendanceRecap')->name('getdatarecap');
        Route::get('get-attendance-overview', 'getAttendanceOverviewData')->name('getdataoverview');
    });
});

Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::controller(ScheduleController::class)->prefix('schedule')->name('schedule.')->group(function () {
        Route::get('/', 'getScheduleIndex')->name('index');
        Route::get('get-data', 'getSchedule')->name('getdata');
        Route::get('get-shift-options', 'getShiftOptions')->name('getshiftOptions');
        Route::get('get-report', 'getReport')->name('getreport');
        Route::get('{branch_id}/get-group-by-branch', 'getGroupByBranch')->name('getGroupByBranch');
        Route::get('{branch_id}/get-employee-by-branch', 'getEmployeeByBranch')->name('getEmployeeByBranch');
        Route::post('create-bulk-schedule', 'bulkStore')->name('createBulk');
        Route::post('{user_id}/update-schedule', 'updateSchedule')->name('update');
        // Route::put('{id}/update-data', 'editShift')->name('update');
        // Route::delete('{id}/delete-data', 'deleteShift')->name('delete');
    });
});
