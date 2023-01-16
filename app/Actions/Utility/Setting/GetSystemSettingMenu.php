<?php

namespace App\Actions\Utility\Setting;
use App\Helpers\Menu\Builder;
use App\Helpers\Menu\ModuleAccess;


class GetSystemSettingMenu
{
    public function handle()
    {
        $menu = [
            [
                'text' => 'System Settings',
                'url' => route('settings.systems.authentication.index'),
                'header' => true
            ],
            // [
            //     'text' => 'General',
            //     'url' => route('settings.systems.general.index'),
            //     'icon' =>  'VSystemGeneral',
            // ],
            [
                'text' => 'Authentication',
                'url' => route('settings.systems.authentication.index'),
                'icon' =>  'VAuthentication',
                'can' => 'view_systems_authentication',
            ],
            [
                'text' => 'Role Management',
                'url' => route('settings.systems.role.index'),
                'icon' =>  'VRole',
                'can' => 'view_systems_role_management'
            ],
        ];

        $builderSidebar = new Builder([
            new ModuleAccess(),
        ]);

        return array_values($builderSidebar->transformItems($menu));
    }
}
