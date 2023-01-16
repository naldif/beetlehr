<?php

namespace App\Http\Controllers\Approval;

use Illuminate\Http\Request;
use App\Services\Approval\ApprovalService;
use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\Approval\RejectApprovalRequest;
use App\Http\Resources\Approval\ApprovalListResource;
use App\Http\Resources\Approval\SubmitApprovalResource;

class ApprovalController extends AdminBaseController
{
    public function __construct(
        ApprovalService $approvalService,
    ) {
        $this->approvalService = $approvalService;
        
        $this->title = "BattleHR | Approval";
        $this->path = "approval/index";
        $this->data = [];
    }

    public function getData(Request $request)
    {
        try {
            $data = $this->approvalService->getData($request);

            $result = new ApprovalListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function approveApproval($id, Request $request)
    {
        try {
            $data = $this->approvalService->approveApproval($id, $request);

            $result = new SubmitApprovalResource($data, 'Success Approve Approval');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function rejectApproval($id, RejectApprovalRequest $request)
    {
        try {
            $data = $this->approvalService->rejectApproval($id, $request);

            $result = new SubmitApprovalResource($data, 'Success Reject Approval');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
