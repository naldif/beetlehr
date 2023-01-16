<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'admin',
                'is_default' => true,
                'permissions' => ['view_general_dashboard', 'view_leave_management_general', 'view_leave_management_leave_type', 'view_leave_management_leave_quota', 'view_attendance_general', 'view_attendance_holiday_calendar', 'view_payroll_general', 'view_payroll_employee_base_salaries', 'view_payroll_payroll_components', 'view_work_report_general', 'view_systems_authentication', 'view_systems_role_management']
            ],
            [
                'name' => 'employee',
                'is_default' => true,
                'permissions' => []
            ],
            [
                'name' => 'super admin',
                'is_default' => true,
                'permissions' => ['view_general_dashboard']
            ]
        ];

        // Create role and assign permission to role
        foreach ($data as $key => $value) {
            try {
                $role = Role::updateOrCreate([
                    'id' => $key + 1
                ], [
                    'id' => $key + 1,
                    'name' => $value["name"],
                    'is_default' => $value["is_default"],
                ]);

                $role->givePermissionTo($value['permissions']);
            } catch (\Exception $exception) {
                // Do something when the exception 
            }
        }
    }
}
