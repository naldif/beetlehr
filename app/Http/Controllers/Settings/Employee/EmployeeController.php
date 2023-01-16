<?php

namespace App\Http\Controllers\Settings\Employee;

use App\Actions\Options\GetBranchOptions;
use App\Actions\Utility\Setting\GetSettingEmployeeMenu;
use App\Http\Controllers\AdminBaseController;
use App\Services\Settings\Employee\GeneralEmployeeService;
use Inertia\Inertia;

class EmployeeController extends AdminBaseController
{

    public function __construct(
        GetSettingEmployeeMenu $getSettingEmployeeMenu,
        GetBranchOptions $getBranchOptions,
        GeneralEmployeeService $generalEmployeeService
    ) {
        $this->getSettingEmployeeMenu = $getSettingEmployeeMenu;
        $this->getBranch = $getBranchOptions;
        $this->generalEmployeeService = $generalEmployeeService;
    }

    public function generalSettingIndex()
    {
        return Inertia::render($this->source . 'settings/employee/general/Index', [
            "title" => 'BattleHR | Setting General Employee',
            "additional" => [
                'menu' => $this->getSettingEmployeeMenu->handle(),
                'data' => $this->generalEmployeeService->getEditableNip()
            ]
        ]);
    }

    public function designationSettingIndex()
    {
        return Inertia::render($this->source . 'settings/employee/designation/Index', [
            "title" => 'BattleHR | Setting Designation',
            "additional" => [
                'menu' => $this->getSettingEmployeeMenu->handle(),
            ]
        ]);
    }

    public function employmentStatusSettingIndex()
    {
        return Inertia::render($this->source . 'settings/employee/employmentStatus/Index', [
            "title" => 'BattleHR | Setting Employment Status',
            "additional" => [
                'menu' => $this->getSettingEmployeeMenu->handle(),
                'employment_type_list' => [
                    'pkwt' => 'Pegawai Tidak Tetap (PKWT)',
                    'pkwtt' => 'Pegawai Tetap (PKWTT)',
                ]
            ]
        ]);
    }

    public function employeeGroupSettingIndex()
    {
        return Inertia::render($this->source . 'settings/employee/group/Index', [
            "title" => 'BattleHR | Setting Employee Group',
            "additional" => [
                'menu' => $this->getSettingEmployeeMenu->handle(),
                'branch_list' => $this->getBranch->handle()
            ]
        ]);
    }
}
