<?php

namespace App\Http\Controllers\Api\V1\Auth;

use stdClass;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\V1\Auth\UpdateFcmRequest;
use App\Services\Api\V1\Auth\AuthenticationService;
use App\Http\Resources\Api\V1\Auth\UpdateFcmResource;
use App\Http\Resources\Api\V1\Auth\RefreshTokenResource;
use App\Http\Resources\Api\V1\Auth\AuthenticationResource;

class AuthenticationController extends ApiBaseController
{
    public function __construct(AuthenticationService $authenticationService) 
    {
        $this->authenticationService = $authenticationService;
    }

    public function login(Request $request)
    {
        try {
            $data = $this->authenticationService->login($request);
            
            $user = new AuthenticationResource($data, "Success Login");
            return $this->respond($user);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage(), $e->getCode());
        }
    }

    public function logout()
    {
        try {
            auth('employees')->logout();
            return $this->messageSuccess('Success Logout', 200);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage(), $e->getCode());
        }
    }

    public function updateFCM(UpdateFcmRequest $request)
    {
        try {
            $data = $this->authenticationService->updateFcmToken($request);
            
            $result = new UpdateFcmResource($data, "Success Update FCM Token");
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage(), $e->getCode());
        }
    }

    public function refresh()
    {
        try {
            $data = new stdClass();
            $data->token = auth('employees')->refresh();
            
            $final = new RefreshTokenResource($data, 'Success Refresh Token');
            return $this->respond($final);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage(), $e->getCode());
        }
    }
}
