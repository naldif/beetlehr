<?php

namespace App\Http\Controllers\Settings\Leave;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminBaseController;
use App\Services\Settings\Leave\LeaveTypeService;
use App\Http\Requests\Settings\Leave\CreateLeaveTypeRequest;
use App\Http\Requests\Settings\Leave\UpdateLeaveTypeRequest;
use App\Http\Resources\Settings\Leave\LeaveTypeListResource;
use App\Http\Resources\Settings\Leave\SubmitLeaveTypeResource;

class LeaveTypeController extends AdminBaseController
{
    public function __construct(LeaveTypeService $leaveTypeService)
    {
        $this->leaveTypeService = $leaveTypeService;
    }

    public function getTypeList(Request $request)
    {
        try {
            $data = $this->leaveTypeService->getData($request);

            $result = new LeaveTypeListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createType(CreateLeaveTypeRequest $request)
    {
        try {
            $data = $this->leaveTypeService->storeData($request);
            $result = new SubmitLeaveTypeResource($data, 'Success Create Leave Type');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateType($id, UpdateLeaveTypeRequest $request)
    {
        try {
            $data = $this->leaveTypeService->updateData($id, $request);
            $result = new SubmitLeaveTypeResource($data, 'Success Update Leave Type');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteType($id)
    {
        try {
            $data = $this->leaveTypeService->deleteData($id);
            $result = new SubmitLeaveTypeResource($data, 'Success Delete Leave Type');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
