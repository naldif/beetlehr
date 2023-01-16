<?php

namespace App\Actions\Utility\Setting;

use App\Helpers\Menu\Builder;
use App\Helpers\Menu\ModuleAccess;

class GetPayrollSettingMenu
{
    public function handle()
    {
        $menu = [
            [
                'text' => 'Payroll Settings',
                'url' => route('settings.payroll.general.index'),
                'header' => true
            ],
            [
                'text' => 'General',
                'url' => route('settings.payroll.general.index'),
                'icon' =>  'VGeneral',
                'can' => 'view_payroll_general'
            ],
            [
                'text' => 'Payroll Group',
                'url' => route('settings.payroll.group.index'),
                'icon' =>  'VGroup',
                'can' => 'view_payroll_group'
            ],
            [
                'text' => 'Employee Base Salaries',
                'url' => route('settings.payroll.employee-base-salaries.index'),
                'icon' =>  'VMoney',
                'can' => 'view_payroll_employee_base_salaries'
            ],
            [
                'text' => 'Payroll Components',
                'url' => route('settings.payroll.components.index'),
                'icon' =>  'VGear',
                'can' => 'view_payroll_payroll_components'
            ]
        ];

        $builderSidebar = new Builder([
            new ModuleAccess(),
        ]);

        return array_values($builderSidebar->transformItems($menu));
    }
}
