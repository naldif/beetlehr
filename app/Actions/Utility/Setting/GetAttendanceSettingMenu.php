<?php

namespace App\Actions\Utility\Setting;

use App\Helpers\Menu\Builder;
use App\Helpers\Menu\ModuleAccess;

class GetAttendanceSettingMenu
{
    public function handle()
    {
        $menu =  [
            [
                'text' => 'Attendance Settings',
                'url' => route('settings.attendance.general.index'),
                'header' => true,
            ],
            [
                'text' => 'General',
                'url' => route('settings.attendance.general.index'),
                'icon' =>  'VSystemGeneral',
                'can'  => 'view_attendance_general'
            ],
            [
                'text' => 'Attendance',
                'url' => route('settings.attendance.index'),
                'icon' =>  'VAttendance',
                'can' => 'view_attendance'
            ],
            [
                'text' => 'Holiday Calendar',
                'url' => route('settings.attendance.holiday.index'),
                'icon' =>  'VCalendar',
                'can'  => 'view_attendance_holiday_calendar'
            ],
        ];

        $builderSidebar = new Builder([
            new ModuleAccess(),
        ]);

        return array_values($builderSidebar->transformItems($menu));
    }
}
