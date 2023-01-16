<?php

namespace App\Actions\Utility\Setting;

use App\Helpers\Menu\Builder;
use App\Helpers\Menu\ModuleAccess;

class GetApprovalSettingMenu
{
    public function handle()
    {
        $menu =  [
            [
                'text' => 'Approval Settings',
                'url' => route('settings.approval.rule.index'),
                'header' => true,
            ],
            [
                'text' => 'Rules',
                'url' => route('settings.approval.rule.index'),
                'icon' =>  'VBook',
                'can'  => 'view_approval_rule'
            ]
        ];

        $builderSidebar = new Builder([
            new ModuleAccess(),
        ]);

        return array_values($builderSidebar->transformItems($menu));
    }
}
