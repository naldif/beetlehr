<?php

use App\Http\Controllers\Approval\ApprovalController;


/*
|--------------------------------------------------------------------------
| Approval Routes
|--------------------------------------------------------------------------
|
| Here is where you can register setting related routes for your application.
|
*/



Route::prefix('approval')->name('approval.')->group(function () {
    Route::controller(ApprovalController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('get-data', 'getData')->name('getdata');
        Route::put('{id}/approve', 'approveApproval')->name('approve');
        Route::put('{id}/reject', 'rejectApproval')->name('reject');
    });
});
