<?php

namespace App\Actions\Utility\Setting;

use App\Helpers\Menu\Builder;
use App\Helpers\Menu\ModuleAccess;

class GetWorkReportSettingMenu
{
    public function handle()
    {
        $menu =  [
            [
                'text' => 'Work Report Settings',
                'url' => route('settings.work-report.general.index'),
                'header' => true
            ],
            [
                'text' => 'General',
                'url' => route('settings.work-report.general.index'),
                'icon' =>  'VSystemGeneral',
                'can' => 'view_work_report_general'
            ],

        ];

        $builderSidebar = new Builder([
            new ModuleAccess(),
        ]);

        return array_values($builderSidebar->transformItems($menu));
    }
}
