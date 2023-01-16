<?php

namespace App\Http\Controllers\Settings\Company;

use App\Services\FileService;
use App\Http\Controllers\AdminBaseController;
use App\Services\Settings\Company\ProfileService;
use App\Http\Requests\Settings\Company\UpdateProfileRequest;
use App\Http\Requests\Settings\Company\UpdateProfilePictureRequest;
use App\Http\Resources\Settings\Company\SubmitCompanyProfileResource;

class ProfileController extends AdminBaseController
{
    public function __construct(FileService $fileService, ProfileService $profileService)
    {
        $this->fileService = $fileService;
        $this->profileService = $profileService;
    }

    public function uploadProfilePicture(UpdateProfilePictureRequest $request)
    {
        try {
            $file = $this->fileService->uploadFile($request->file('file'));
            $data = $this->profileService->updateProfilePicture($file);

            $result = new SubmitCompanyProfileResource($data, 'Success Change Profile Picture');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $data = $this->profileService->updateProfile($request);

            $result = new SubmitCompanyProfileResource($data, 'Success Change Profile');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
