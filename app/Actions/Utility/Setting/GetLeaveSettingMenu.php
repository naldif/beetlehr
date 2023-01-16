<?php

namespace App\Actions\Utility\Setting;

use App\Helpers\Menu\Builder;
use App\Helpers\Menu\ModuleAccess;

class GetLeaveSettingMenu
{
    public function handle()
    {
        $menu =  [
            [
                'text' => 'Leave Management Settings',
                'url' => route('settings.leave.type.index'),
                'header' => true,
            ],
            [
                'text' => 'General',
                'url' => route('settings.leave.general.index'),
                'icon' =>  'VSystemGeneral',
                'can' => 'view_leave_management_general'
            ],
            [
                'text' => 'Leave Type',
                'url' => route('settings.leave.type.index'),
                'icon' =>  'VBook',
                'can' => 'view_leave_management_leave_type'
            ],
            [
                'text' => 'Leave Quota',
                'url' => route('settings.leave.quota.index'),
                'icon' =>  'VList',
                'can' => 'view_leave_management_leave_quota'
            ],
        ];

        $builderSidebar = new Builder([
            new ModuleAccess(),
        ]);

        return array_values($builderSidebar->transformItems($menu));
    }
}
