<?php

use App\Http\Controllers\Leave\LeaveController;


/*
|--------------------------------------------------------------------------
| Leave Routes
|--------------------------------------------------------------------------
|
| Here is where you can register setting related routes for your application.
|
*/



Route::prefix('leave')->name('leave.')->group(function () {
    Route::controller(LeaveController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('get-data', 'getData')->name('getdata');
        Route::get('get-leave-type', 'getLeaveType')->name('getleavetype');
        Route::post('create', 'createData')->name('create');
        Route::get('/{id}/download-leave-file', 'donwloadLeaveFile')->name('downloadfile');
        Route::put('{id}/update', 'updateStatus')->name('update');
        Route::delete('{id}/delete', 'deleteData')->name('delete');
    });
});
