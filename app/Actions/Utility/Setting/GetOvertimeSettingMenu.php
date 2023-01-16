<?php

namespace App\Actions\Utility\Setting;

use App\Helpers\Menu\Builder;
use App\Helpers\Menu\ModuleAccess;

class GetOvertimeSettingMenu
{
    public function handle()
    {
        $menu = [
            [
                'text' => 'Overtime Settings',
                'url' => route('settings.overtime.rule.index'),
                'header' => true
            ],
            [
                'text' => 'Rules',
                'url' => route('settings.overtime.rule.index'),
                'icon' =>  'VBook',
                'can' => 'view_overtime_rule'
            ]
        ];

        $builderSidebar = new Builder([
            new ModuleAccess(),
        ]);

        return array_values($builderSidebar->transformItems($menu));
    }
}
