<?php

use App\Http\Controllers\NoticeBoard\NoticeBoardController;

/*
|--------------------------------------------------------------------------
| Notice Board Routes
|--------------------------------------------------------------------------
|
| Here is where you can register setting related routes for your application.
|
*/



Route::prefix('notice-board')->name('notice-board.')->group(function () {
    Route::controller(NoticeBoardController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('get-data', 'getData')->name('getdata');
        Route::post('create', 'createData')->name('create');
        Route::post('{id}/update', 'updateData')->name('update');
        Route::delete('{id}/delete', 'deleteData')->name('delete');
    });
});
