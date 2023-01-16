<?php

namespace App\Http\Controllers\Settings\Employee;

use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\Settings\Employee\DesignationRequest;
use App\Http\Resources\Settings\Employee\DesignationListResource;
use App\Http\Resources\Settings\Employee\SubmitDesignationResource;
use App\Services\Settings\Employee\DesignationService;
use Illuminate\Http\Request;

class DesignationController extends AdminBaseController
{
    public function __construct(DesignationService $designationService)
    {
        $this->designationService = $designationService;
    }

    public function getDesignationList(Request $request)
    {
        try {
            $data = $this->designationService->getData($request);

            $result = new DesignationListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createDesignation(DesignationRequest $request)
    {
        try {
            $data = $this->designationService->storeDesignation($request);
            $result = new SubmitDesignationResource($data, 'Success Create Designation');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateDesignation($id, DesignationRequest $request)
    {
        try {
            $data = $this->designationService->updateDesignation($id, $request);
            $result = new SubmitDesignationResource($data, 'Success Update Designation');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteDesignation($id)
    {
        try {
            $data = $this->designationService->deleteDesignation($id);
            $result = new SubmitDesignationResource($data, 'Success Delete Designation');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
