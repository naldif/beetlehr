<?php

namespace App\Http\Controllers\Settings\Employee;

use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\Settings\Employee\CreateEmploymentStatus;
use App\Http\Resources\Settings\Employee\EmployementStatusListResource;
use App\Http\Resources\Settings\Employee\SubmitEmployementStatusResource;
use App\Services\Settings\Employee\EmploymentStatusService;
use Illuminate\Http\Request;

class EmploymentStatusController extends AdminBaseController
{
    public function __construct(EmploymentStatusService $employmentStatusService)
    {
        $this->employmentStatusService = $employmentStatusService;
    }

    public function getEmploymentStatusList(Request $request)
    {
        try {
            $data = $this->employmentStatusService->getData($request);
            $result = new EmployementStatusListResource($data);

            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createEmploymentStatus(CreateEmploymentStatus $request)
    {
        try {
            $data = $this->employmentStatusService->storeEmploymentStatus($request);
            $result = new SubmitEmployementStatusResource($data, 'Success Create Employment Status');

            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateEmploymentStatus($id, CreateEmploymentStatus $request)
    {
        try {
            $data = $this->employmentStatusService->updateEmploymentStatus($id, $request);
            $result = new SubmitEmployementStatusResource($data, 'Success Update Employment Status');

            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteEmploymentStatus($id)
    {
        try {
            $data = $this->employmentStatusService->deleteEmploymentStatus($id);
            $result = new SubmitEmployementStatusResource($data, 'Success delete employment Status');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
