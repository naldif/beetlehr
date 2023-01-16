<?php

namespace App\Http\Controllers\Api\V1\Leave;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController;
use App\Services\Api\V1\Leave\LeaveService;
use App\Http\Requests\Api\V1\Leave\CreateLeaveRequest;
use App\Http\Resources\Api\V1\Leave\LeaveListResource;
use App\Http\Resources\Api\V1\Leave\LeaveDetailResource;
use App\Http\Resources\Api\V1\Leave\LeaveTypeListResource;
use App\Http\Resources\Api\V1\Leave\LeaveQuotaListResource;

class LeaveController extends ApiBaseController
{
    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function getLeaveQuota(Request $request)
    {
        try {
            $data = $this->leaveService->getLeaveQuota($request);

            $result = new LeaveQuotaListResource($data, 'Success Get Leave Quota Data');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getLeaveType(Request $request)
    {
        try {
            $data = $this->leaveService->getLeaveType($request);

            $result = new LeaveTypeListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getLeave(Request $request)
    {
        try {
            $data = $this->leaveService->getLeave($request);

            $result = new LeaveListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getLeaveDetail($id)
    {
        try {
            $data = $this->leaveService->getLeaveDetail($id);

            $result = new LeaveDetailResource($data, 'Success Get Leave Detail');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createLeave(CreateLeaveRequest $request)
    {
        try {
            $data = $this->leaveService->createLeave($request);

            $result = new LeaveDetailResource($data, 'Success Create Leave');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function cancelLeave($id)
    {
        try {
            $data = $this->leaveService->cancelLeave($id);

            $result = new LeaveDetailResource($data, 'Success Cancel Leave');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
