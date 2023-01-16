<?php

namespace App\Http\Controllers\Api\V1\Employee;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Validation\ValidationException;
use App\Services\Api\V1\Employee\ProfileService;
use App\Http\Requests\Api\V1\Employee\UpdateProfileRequest;
use App\Http\Resources\Api\V1\Employee\ProfileDetailResource;

class ProfileController extends ApiBaseController
{
    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function getProfileData()
    {
        try {
            $data = $this->profileService->getData();

            $result = new ProfileDetailResource($data, 'Success Get Profile Data');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $this->profileService->updateData($request);

            $result = new ProfileDetailResource($data, 'Success Update Profile Data');
            DB::commit();
            return $this->respond($result);
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($e instanceof ValidationException) {
                return $this->exceptionError($e->errors());
            }

            return $this->exceptionError($e->getMessage());
        }
    }
}
