<?php

namespace App\Http\Controllers\Api\V1\Approval;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController;
use App\Services\Api\V1\Approval\ApprovalService;
use App\Http\Requests\Api\V1\Approval\RejectApprovalRequest;
use App\Http\Resources\Api\V1\Approval\ApprovalListResource;
use App\Http\Requests\Api\V1\Approval\ApproveApprovalRequest;
use App\Http\Resources\Api\V1\Approval\ApprovalDetailResource;
use App\Http\Resources\Api\V1\Approval\RejectApprovalResource;
use App\Http\Resources\Api\V1\Approval\ApproveApprovalResource;

class ApprovalController extends ApiBaseController
{
    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    public function getApprovalData(Request $request)
    {
        try {
            $data = $this->approvalService->getData($request);  

            $result = new ApprovalListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getDetailApproval($id)
    {
        try {
            $data = $this->approvalService->getDetailApproval($id);

            $result = new ApprovalDetailResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function approveApproval($id, ApproveApprovalRequest $request)
    {
        try {
            $data = $this->approvalService->approveApproval($id, $request);

            $result = new ApproveApprovalResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function rejectApproval($id, RejectApprovalRequest $request)
    {
        try {
            $data = $this->approvalService->rejectApproval($id, $request);

            $result = new RejectApprovalResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
