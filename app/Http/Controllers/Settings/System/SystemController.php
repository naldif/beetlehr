<?php

namespace App\Http\Controllers\Settings\System;

use App\Actions\Utility\Setting\GetSystemSettingMenu;
use App\Http\Controllers\AdminBaseController;
use App\Http\Resources\Settings\Systems\GetGeneralSystemResource;
use App\Services\Settings\Systems\AuthenticationService;
use App\Services\Settings\Systems\GeneralSystemService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SystemController extends AdminBaseController
{
    public function __construct(
        GetSystemSettingMenu $getSystemSettingMenu,
        AuthenticationService $authenticationService
    ) {
        $this->getSystemSettingMenu = $getSystemSettingMenu;
        $this->authenticationService = $authenticationService;
    }

    // public function SystemSettingIndex()
    // {
    //     return Inertia::render($this->source . 'settings/systems/general/Index', [
    //         "title" => 'BattleHR | Setting System General',
    //         "additional" => [
    //             'menu' => $this->getSystemSettingMenu->handle(),
    //             'data' => $this->generalSystemService->getGeneralSystemSettings()
    //         ]
    //     ]);
    // }

    public function AuthenticationSettingIndex()
    {
        return Inertia::render($this->source . 'settings/systems/authentication/Index', [
            "title" => 'BattleHR | Setting System Authentication',
            "additional" => [
                'menu' => $this->getSystemSettingMenu->handle(),
                'data' => $this->authenticationService->getData()
            ]
        ]);
    }

    public function roleSettingIndex()
    {
        return Inertia::render($this->source . 'settings/systems/role/Index', [
            "title" => 'BattleHR | Setting System Authentication',
            "additional" => [
                'menu' => $this->getSystemSettingMenu->handle(),
                'data' => $this->authenticationService->getData()
            ]
        ]);
    }
}
