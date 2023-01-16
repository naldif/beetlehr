<?php

namespace App\Actions\Utility\Setting;

use App\Helpers\Menu\Builder;
use App\Helpers\Menu\ModuleAccess;

class GetCompanySettingMenu
{
    public function handle()
    {
       
        $menu = [
            [
                'text' => 'Company Settings',
                'url' => route('settings.company.profile.index'),
                'header' => true,
               
            ],
            [
                'text' => 'Profile',
                'url' => route('settings.company.profile.index'),
                'icon' =>  'VProfile',
                'can'  => 'view_company_profile'
            ],
            [
                'text' => 'NPWP',
                'url' => route('settings.company.npwp.index'),
                'icon' =>  'VNpwp',
                'can' => 'view_company_npwp'
                
            ],
            [
                'text' => 'Branches',
                'url' => route('settings.company.branch.index'),
                'icon' =>  'VMapsPin',
                'can' => 'view_company_branch'
            ],
            [
                'text' => 'BPJS',
                'url' => route('settings.company.bpjs.index'),
                'icon' =>  'VBpjs',
                'can' => ['view_company_bpjs_kesehatan','view_company_bpjs_ketenagakerjaan']
            ],
        ];

        $builderSidebar = new Builder([
            new ModuleAccess(),
        ]);

        return array_values($builderSidebar->transformItems($menu));
    }
}
