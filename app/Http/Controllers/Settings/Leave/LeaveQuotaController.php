<?php

namespace App\Http\Controllers\Settings\Leave;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminBaseController;
use App\Services\Settings\Leave\LeaveQuotaService;
use App\Http\Requests\Settings\Leave\CreateLeaveQuotaRequest;
use App\Http\Requests\Settings\Leave\UpdateLeaveQuotaRequest;
use App\Http\Resources\Settings\Leave\LeaveQuotaListResource;
use App\Http\Resources\Settings\Leave\SubmitLeaveQuotaResource;

class LeaveQuotaController extends AdminBaseController
{
    public function __construct(LeaveQuotaService $leaveQuotaService)
    {
        $this->leaveQuotaService = $leaveQuotaService;
    }

    public function getQuotaList(Request $request)
    {
        try {
            $data = $this->leaveQuotaService->getData($request);

            $result = new LeaveQuotaListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getLeaveTypeOptions(Request $request)
    {
        try {
            $data = $this->leaveQuotaService->getLeaveTypeOptions($request);
            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createQuota(CreateLeaveQuotaRequest $request)
    {
        try {
            $data = $this->leaveQuotaService->storeData($request);
            $result = new SubmitLeaveQuotaResource($data, 'Success Generate Leave Quota');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateQuota($id, UpdateLeaveQuotaRequest $request)
    {
        try {
            $data = $this->leaveQuotaService->updateData($id, $request);
            $result = new SubmitLeaveQuotaResource($data, 'Success Update Leave Quota');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteQuota($id)
    {
        try {
            $data = $this->leaveQuotaService->deleteData($id);
            $result = new SubmitLeaveQuotaResource($data, 'Success Delete Leave Quota');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
