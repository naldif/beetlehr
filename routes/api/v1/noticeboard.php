<?php

use App\Http\Controllers\Api\V1\NoticeBoard\NoticeBoardController;


/*
|--------------------------------------------------------------------------
| Notice Board Routes
|--------------------------------------------------------------------------
|
| Here is where you can register setting related routes for your application.
|
*/



Route::controller(NoticeBoardController::class)->group(function () {
    Route::get('notice-board', 'getNoticeBoard');
});
