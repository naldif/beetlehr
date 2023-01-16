<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\File\FileController;
use App\Http\Controllers\Api\V1\Auth\AuthenticationController;
use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Server\ServerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1/employee'], function () {
     Route::prefix('authentication')->group(function () {  
        Route::controller(ForgotPasswordController::class)->group(function () {
            Route::post('/password/email', 'sendResetLinkEmail'); 
        });
        Route::post('/password/reset', 'ResetPasswordController@reset')->name('api.reset.password');

        Route::controller(AuthenticationController::class)->group(function () {
            Route::post('login', 'login');
            
            Route::middleware(['JwtMiddleware', 'ContractChecker', 'UserDevice'])->group(function () {
                Route::get('logout', 'logout');
                Route::post('refresh', 'refresh');
            });
        });
    });  

    Route::middleware(['JwtMiddleware', 'ContractChecker', 'UserDevice'])->group(function () {
        Route::controller(AuthenticationController::class)->group(function () {
            Route::put('fcm-token', 'updateFCM');
        });

        Route::controller(FileController::class)->group(function () {
            Route::post('optional-files', 'uploadOptionalMultipleFile');
            Route::post('files', 'uploadMultipleFile');
            Route::post('offline/files', 'uploadMultipleFile');
        });

        require __DIR__ . '/api/v1/attendance.php';
        require __DIR__ . '/api/v1/noticeboard.php';
        require __DIR__ . '/api/v1/payroll.php';
        require __DIR__ . '/api/v1/employee.php';
        require __DIR__ . '/api/v1/leave.php';
        require __DIR__ . '/api/v1/approval.php';
    });
});

// Server 
Route::controller(ServerController::class)->group(function () {
    Route::get('v1/server/status', 'getStatus');
});
