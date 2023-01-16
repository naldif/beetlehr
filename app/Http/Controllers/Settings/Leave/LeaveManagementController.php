<?php

namespace App\Http\Controllers\Settings\Leave;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Actions\Options\GetBranchOptions;
use App\Http\Controllers\AdminBaseController;
use App\Actions\Utility\Setting\GetLeaveSettingMenu;
use App\Services\Settings\Leave\LeaveGeneralService;

class LeaveManagementController extends AdminBaseController
{
    public function __construct(
        GetLeaveSettingMenu $getLeaveSettingMenu,
        GetBranchOptions $getBranchListOptions,
        LeaveGeneralService $leaveGeneralService
    ) {
        $this->getLeaveSettingMenu = $getLeaveSettingMenu;
        $this->getBranchListOptions = $getBranchListOptions;
        $this->leaveGeneralService = $leaveGeneralService;
    }

    public function leaveGeneralSettingIndex(Request $request)
    {
        return Inertia::render($this->source . 'settings/leave/general/Index', [
            "title" => 'BattleHR | Setting Leave General',
            "additional" => [
                'menu' => $this->getLeaveSettingMenu->handle(),
                'data' => $this->leaveGeneralService->getResetLeave(),
            ]
        ]);
    }

    public function leaveTypeSettingIndex(Request $request)
    {
        return Inertia::render($this->source . 'settings/leave/type/index', [
            "title" => 'BattleHR | Setting Leave',
            "additional" => [
                'menu' => $this->getLeaveSettingMenu->handle(),
                'branch_list' => $this->getBranchListOptions->handle(),
            ]
        ]);
    }

    public function leaveQuotaSettingIndex(Request $request)
    {
        return Inertia::render($this->source . 'settings/leave/quota/index', [
            "title" => 'BattleHR | Setting Leave',
            "additional" => [
                'menu' => $this->getLeaveSettingMenu->handle(),
                'branch_list' => $this->getBranchListOptions->handle(),
            ]
        ]);
    }
}
