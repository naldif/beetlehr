<?php

namespace App\Http\Controllers\Settings\Overtime;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminBaseController;
use App\Actions\Utility\Setting\GetOvertimeSettingMenu;

class OvertimeController extends AdminBaseController
{
    public function __construct(
        GetOvertimeSettingMenu $getOvertimeSettingMenu,
    ) {
        $this->getOvertimeSettingMenu = $getOvertimeSettingMenu;
    }

    public function ruleSettingIndex(Request $request)
    {
        return Inertia::render($this->source . 'settings/overtime/rule/index', [
            "title" => 'BattleHR | Setting Overtime',
            "additional" => [
                'menu' => $this->getOvertimeSettingMenu->handle()
            ]
        ]);
    }
}
