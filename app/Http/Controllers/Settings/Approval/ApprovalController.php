<?php

namespace App\Http\Controllers\Settings\Approval;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminBaseController;
use App\Actions\Utility\Setting\GetApprovalSettingMenu;
use App\Services\Settings\Approval\ApprovalRuleService;
use App\Http\Resources\Settings\Approval\ApprovalTypeListResource;

class ApprovalController extends AdminBaseController
{
    public function __construct(
        GetApprovalSettingMenu $getApprovalSettingMenu,
        ApprovalRuleService $approvalRuleService
    ) {
        $this->getApprovalSettingMenu = $getApprovalSettingMenu;
        $this->approvalRuleService = $approvalRuleService;
    }

    public function getTypeIndex(Request $request)
    {
        try {
            $data = $this->approvalRuleService->getTypeData($request);

            $result = new ApprovalTypeListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function ruleSettingIndex(Request $request)
    {
        return Inertia::render($this->source . 'settings/approval/rule/index', [
            "title" => 'BattleHR | Setting Approval',
            "additional" => [
                'menu' => $this->getApprovalSettingMenu->handle()
            ]
        ]);
    }

    public function configRuleIndex($id, Request $request)
    {
        return Inertia::render($this->source . 'settings/approval/rule/config/index', [
            "title" => 'BattleHR | Setting Approval',
            "additional" => [
                'menu' => $this->getApprovalSettingMenu->handle(),
                'data' => $this->approvalRuleService->getTypeDetail($id)
            ]
        ]);
    }
}
