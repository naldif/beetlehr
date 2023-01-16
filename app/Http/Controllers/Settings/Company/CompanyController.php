<?php

namespace App\Http\Controllers\Settings\Company;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Actions\Utility\Setting\GetCompanySettingMenu;
use App\Actions\Options\GetNpwpListOptions;
use App\Actions\Options\GetTimezoneOptions;
use App\Http\Controllers\AdminBaseController;
use App\Services\Settings\Company\ProfileService;
use App\Actions\Options\GetBpjstkRiskLevelOptions;

class CompanyController extends AdminBaseController
{
    public function __construct(
        GetCompanySettingMenu $getCompanySettingMenu,
        ProfileService $profileService,
        GetTimezoneOptions $getTimezoneOptions,
        GetNpwpListOptions $getNpwpListOptions,
        GetBpjstkRiskLevelOptions $getBpjstkRiskLevelOptions
    ) {
        $this->getCompanySettingMenu = $getCompanySettingMenu;
        $this->profileService = $profileService;
        $this->getTimezoneOptions = $getTimezoneOptions;
        $this->getNpwpListOptions = $getNpwpListOptions;
        $this->getBpjstkRiskLevelOptions = $getBpjstkRiskLevelOptions;
    }

    public function profileSettingIndex(Request $request)
    {

        return Inertia::render($this->source . 'settings/company/profile/index', [
            "title" => 'BattleHR | Setting Company',
            "additional" => [
                'menu' => $this->getCompanySettingMenu->handle(),
                'data' => $this->profileService->getProfileCompany()
            ]
        ]);
    }

    public function npwpSettingIndex(Request $request)
    {
        return Inertia::render($this->source . 'settings/company/npwp/index', [
            "title" => 'BattleHR | Setting Company',
            "additional" => [
                'menu' => $this->getCompanySettingMenu->handle()
            ]
        ]);
    }

    public function branchSettingIndex(Request $request)
    {
        return Inertia::render($this->source . 'settings/company/branch/index', [
            "title" => 'BattleHR | Setting Company',
            "additional" => [
                'menu' => $this->getCompanySettingMenu->handle(),
                'timezones' => $this->getTimezoneOptions->handle(),
                'npwp_list' => $this->getNpwpListOptions->handle(),
            ]
        ]);
    }

    public function bpjsSettingIndex(Request $request)
    {
        return Inertia::render($this->source . 'settings/company/bpjs/index', [
            "title" => 'BattleHR | Setting Company',
            "additional" => [
                'menu' => $this->getCompanySettingMenu->handle(),
                'risk_level' => $this->getBpjstkRiskLevelOptions->handle()
            ]
        ]);
    }
}
