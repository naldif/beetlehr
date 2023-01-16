<?php

namespace App\Actions\Utility\Setting;

use App\Helpers\Menu\Builder;
use App\Helpers\Menu\ModuleAccess;

class GetSettingEmployeeMenu
{
    public function handle()
    {
        $menu = [
            [
                'text' => 'Employee Settings',
                'url' => route('settings.employee.general.index'),
                'header' => true
            ],
            [
                'text' => 'General',
                'url' => route('settings.employee.general.index'),
                'icon' =>  'VSystemGeneral',
                'can' => 'view_employee_general'
            ],
            [
                'text' => 'Designation',
                'url' => route('settings.employee.designation.index'),
                'icon' =>  'VDesignation',
                'can' => 'view_employee_designation'
            ],
            [
                'text' => 'Employment Status',
                'url' => route('settings.employee.status.index'),
                'icon' =>  'VEmploymentStatus',
                'can' => 'view_employee_employment_status'
            ],
            [
                'text' => 'Group',
                'url' => route('settings.employee.group.index'),
                'icon' =>  'VGroup',
                'can' => 'view_employee_group'
            ],
        ];

        $builderSidebar = new Builder([
            new ModuleAccess(),
        ]);

        return array_values($builderSidebar->transformItems($menu));
    }
}
