<?php

namespace App\Http\Controllers\Settings\System;

use App\Http\Controllers\AdminBaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Settings\Systems\SubmitAuthenticationResource;
use App\Services\Settings\Systems\AuthenticationService;
use Illuminate\Http\Request;

class AuthenticationSystemController extends AdminBaseController
{
    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }
    public function updateLockUserDevice(Request $request)
    {
        try {
            $data = $this->authenticationService->updateData($request);

            $result = new SubmitAuthenticationResource($data, 'Success Change Lock User Device');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
