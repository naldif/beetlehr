<?php

use App\Http\Controllers\Api\V1\Employee\ResignController;
use App\Http\Controllers\Api\V1\Employee\ProfileController;

/*
|--------------------------------------------------------------------------
| Employee API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register setting related routes for your application.
|
*/


Route::controller(ResignController::class)->group(function () {
    Route::get('resign', 'getResignData');
    Route::post('resign', 'createResign');
    Route::put('resign/{id}/cancel', 'cancelResign');
});

Route::controller(ProfileController::class)->group(function () {
    Route::get('profile', 'getProfileData');
    Route::post('profile/update', 'updateProfile');
});
