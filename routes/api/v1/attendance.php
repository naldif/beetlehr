<?php

use App\Http\Controllers\Api\V1\Attendance\ScheduleController;
use App\Http\Controllers\Api\V1\Attendance\AttendanceController;
use App\Http\Controllers\Api\V1\Attendance\BreakAttendanceController;

/*
|--------------------------------------------------------------------------
| Attendance API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register setting related routes for your application.
|
*/



Route::controller(ScheduleController::class)->group(function () {
    Route::get('schedule', 'getSchedule');
});

Route::controller(AttendanceController::class)->group(function () {
    Route::get('attendance-log', 'getAttendanceLog');
    Route::get('attendance-overview', 'getAttendanceOverview');
    Route::get('attendance-detail/{date}', 'getAttendanceDetail');

    Route::post('check-button-clockin', 'checkButtonClockin');   
    Route::post('attendance-check-clocked', 'checkAttendanceClocked');
    Route::post('attendance-image', 'uploadAttendanceImage');
    Route::post('attendance-check-location', 'checkAttendanceLocation');
    Route::post('attendance-check-before-clock', 'checkAttendanceBeforeClock');
    Route::post('attendance-clock', 'attendanceClock');

    Route::post('attendances/offline', 'syncOfflineAttendance');
    Route::post('attendances/cancel-attendance', 'cancelAttendanceOffline');
});

Route::controller(BreakAttendanceController::class)->prefix('attendance')->group(function () {
    Route::post('break-check-location', 'checkBreakLocation');
    Route::post('break', 'submitBreak');
    Route::get('break-setting', 'setting');
});