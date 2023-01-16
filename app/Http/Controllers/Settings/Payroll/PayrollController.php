<?php

namespace App\Http\Controllers\Settings\Payroll;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Actions\Options\GetBranchOptions;
use App\Http\Controllers\AdminBaseController;
use App\Actions\Options\GetDesignationOptions;
use App\Actions\Utility\Setting\GetPayrollSettingMenu;
use App\Services\Settings\Payroll\GeneralSettingService;

class PayrollController extends AdminBaseController
{
    public function __construct(
        GetPayrollSettingMenu $getPayrollSettingMenu,
        GeneralSettingService $generalSettingService,
        GetBranchOptions $getBranchOptions,
        GetDesignationOptions $getDesignationOptions
    ) {
        $this->getPayrollSettingMenu = $getPayrollSettingMenu;
        $this->generalSettingService = $generalSettingService;
        $this->getBranchOptions = $getBranchOptions;
        $this->getDesignationOptions = $getDesignationOptions;
    }

    public function generalSettingIndex(Request $request)
    {
        return Inertia::render($this->source . 'settings/payroll/general/index', [
            "title" => 'BattleHR | Setting Payroll',
            "additional" => [
                'menu' => $this->getPayrollSettingMenu->handle(),
                'data' => $this->generalSettingService->getPayrollSettings()
            ]
        ]);
    }

    public function payrollGroupIndex(Request $request)
    {
        return Inertia::render($this->source . 'settings/payroll/group/index', [
            "title" => 'BattleHR | Setting Payroll',
            "additional" => [
                'menu' => $this->getPayrollSettingMenu->handle()
            ]
        ]);
    }

    public function employeeBaseSalariesIndex(Request $request)
    {
        $branchOptions = [
            'all' => 'All Branches'
        ];
        foreach ($this->getBranchOptions->handle() as $key => $value) {
            $branchOptions[$key] = $value;
        }

        return Inertia::render($this->source . 'settings/payroll/employeeBaseSalary/index', [
            "title" => 'BattleHR | Setting Payroll',
            "additional" => [
                'menu' => $this->getPayrollSettingMenu->handle(),
                'data' => $this->generalSettingService->getPayrollSettings(),
                'branch_list' => $branchOptions,
                'branch_filter' => $this->getBranchOptions->handle(),
                'designation_filter' => $this->getDesignationOptions->handle(),
            ]
        ]);
    }

    public function payrollComponentIndex(Request $request)
    {
        $branchOptions = [
            'all' => 'All Branches'
        ];
        foreach ($this->getBranchOptions->handle() as $key => $value) {
            $branchOptions[$key] = $value;
        }

        return Inertia::render($this->source . 'settings/payroll/payrollComponent/index', [
            "title" => 'BattleHR | Setting Payroll',
            "additional" => [
                'menu' => $this->getPayrollSettingMenu->handle(),
                'data' => $this->generalSettingService->getPayrollSettings(),
                'branch_list' => $branchOptions,
                'branch_filter' => $this->getBranchOptions->handle(),
                'designation_filter' => $this->getDesignationOptions->handle(),
            ]
        ]);
    }
}
