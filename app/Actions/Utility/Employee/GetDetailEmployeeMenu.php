<?php

namespace App\Actions\Utility\Employee;


class GetDetailEmployeeMenu
{
    public function handle($id = null)
    {
        return [
            [
                'text' => 'Detail Information',
                'url' => route('employment.employee.show', ['id' => $id]),
                'icon' =>  'VDetailInformation',
            ],
            [
                'text' => 'Attendance Log',
                'url' => route('employment.employee.attendance-log.index', ['id' => $id]),
                'icon' =>  'VAttendanceLog',
            ]
        ];
    }
}
