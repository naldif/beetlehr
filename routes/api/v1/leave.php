<?php

use App\Http\Controllers\Api\V1\Leave\LeaveController;

/*
|--------------------------------------------------------------------------
| Leave API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register leave related routes for your application.
|
*/



Route::controller(LeaveController::class)->group(function () {
    Route::get('leave-quota', 'getLeaveQuota');
    Route::get('leave-type', 'getLeaveType');

    Route::get('leave', 'getLeave');
    Route::post('leave', 'createLeave');
    Route::get('leave/{id}', 'getLeaveDetail');
    Route::put('leave/{id}/cancel', 'cancelLeave');
});