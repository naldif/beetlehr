<?php

namespace App\Http\Controllers\Settings\Approval;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminBaseController;
use App\Services\Settings\Approval\ApprovalRuleService;
use App\Http\Resources\Settings\Approval\SubmitApprovalRuleResource;
use App\Http\Resources\Settings\Approval\ApprovalBranchListResource;

class ApprovalRuleController extends AdminBaseController
{
    public function __construct(ApprovalRuleService $approvalRuleService)
    {
        $this->approvalRuleService = $approvalRuleService;
    }

    public function getApprovalBranchList(Request $request)
    {
        try {
            $data = $this->approvalRuleService->getApprovalBranches($request);
            $result = new ApprovalBranchListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getApprovalEmployeeRule(Request $request)
    {
        try {
            $data = $this->approvalRuleService->getApprovalEmployeeRule($request);
            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateApprovalBranch(Request $request)
    {
        try {
            $data = $this->approvalRuleService->updateApprovalRule($request);
            $result = new SubmitApprovalRuleResource($data, 'Success Update Approval Rule');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteApprovalBranch($id)
    {
        try {
            $data = $this->approvalRuleService->deleteApprovalRule($id);
            $result = new SubmitApprovalRuleResource($data, 'Success Delete Approval Rule');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
