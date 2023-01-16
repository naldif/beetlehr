<?php

namespace App\Actions\Utility;

use App\Actions\Utility\Setting\GetCompanySettingMenu;
use App\Actions\Utility\Setting\GetSettingEmployeeMenu;
use App\Actions\Utility\Setting\GetApprovalSettingMenu;
use App\Actions\Utility\Setting\GetLeaveSettingMenu;
use App\Actions\Utility\Setting\GetAttendanceSettingMenu;
use App\Actions\Utility\Setting\GetPayrollSettingMenu;
use App\Actions\Utility\Setting\GetWorkReportSettingMenu;
use App\Actions\Utility\Setting\GetSystemSettingMenu;

class GetDashboardSidebarMenu
{
    public function handle()
    {
        $getCompanySettingMenu = new GetCompanySettingMenu();
        $getEmployeeSettingMenu = new GetSettingEmployeeMenu();
        $getApprovalSettingMenu = new GetApprovalSettingMenu();
        $getLeaveSettingMenu = new GetLeaveSettingMenu();
        $getAttendanceSettingMenu = new GetAttendanceSettingMenu();
        $getPayrollSettingMenu = new GetPayrollSettingMenu();
        $getWorkReportSettingMenu = new GetWorkReportSettingMenu();
        $getSystemSettingMenu = new GetSystemSettingMenu();
        return [
            [
                'text' => 'Dashboard',
                'url'  => route('dashboard.index'),
                'icon' => 'VDashboard',
                'can'  => 'view_general_dashboard'
            ],
            [
                'text' => 'Employee',
                'icon' => 'VEmployee',
                'group' => true,
                'can'  => ['view_employee_management_employee', 'view_employee_management_resign_management'],
                'submenu' => [
                    [
                        'text' => 'Employee',
                        'url'  => route('employment.employee.index'),
                        'can'  => 'view_employee_management_employee',
                    ],
                    [
                        'text' => 'Resign Management',
                        'url'  => route('employment.resign-management.index'),
                        'can'  => 'view_employee_management_resign_management'
                    ]
                ],
            ],
            [
                'text' => 'Attendance',
                'icon' => 'VAttendanceSolid',
                'group' => true,
                'can'   => ['view_employee_management_shift', 'view_employee_management_attendance', 'view_employee_management_schedule'],
                'submenu' => [
                    [
                        'text' => 'Shift',
                        'url'  => route('attendance.shift.index'),
                        'can'  => 'view_employee_management_shift'
                    ],
                    [
                        'text' => 'Schedule',
                        'url'  => route('attendance.schedule.index'),
                        'can'  => 'view_employee_management_schedule'
                    ],
                    [
                        'text' => 'Attendance',
                        'url'  => route('attendance.attendance-overview.index'),
                        'can'  => 'view_employee_management_attendance'
                    ]
                ]
            ],
            [
                'text' => 'Approval',
                'url'  => route('approval.index'),
                'icon' => 'VApproval',
                'can'  => 'view_employee_management_approval'
            ],
            [
                'text' => 'Leave Management',
                'url'  => route('leave.index'),
                'icon' => 'VPlane',
                'can'  => 'view_employee_management_leave'
            ],
            [
                'text' => 'Payroll',
                'icon' => 'VPayroll',
                'group' => true,
                'can'  => 'view_payroll_management_payroll',
                'submenu' => [
                    [
                        'text' => 'Run Payroll',
                        'url'  => route('payroll.run.index'),
                        'can'  => 'view_payroll_management_payroll',
                    ]
                ]
            ],
            [
                'text' => 'Notice Board',
                'url'  => route('notice-board.index'),
                'icon' => 'VNoticeBoard',
                'can'  => 'view_notice_board_management'
            ],
            [
                'text' => 'Settings',
                'icon' => 'VSetting',
                'group' => true,
                'can' => ['view_company_profile', 'view_company_npwp', 'view_company_branch', 'view_company_bpjs_kesehatan', 'view_company_bpjs_ketenagakerjaan', 'view_employee_general', 'view_employee_designation', 'view_employee_employment_status', 'view_employee_group', 'view_approval_rule', 'view_leave_management_general', 'view_leave_management_leave_type', 'view_leave_management_leave_quota', 'view_attendance_general', 'view_attendance_holiday_calendar', 'view_payroll_general', 'view_payroll_employee_base_salaries', 'view_payroll_payroll_components', 'view_work_report_general', 'view_systems_authentication', 'view_systems_role_management'],
                'submenu' => [
                    [
                        'text' => 'Company',
                        'url'  => $getCompanySettingMenu->handle()[1]['url'] ?? route('settings.company.profile.index'),
                        'can'  => ['view_company_profile', 'view_company_npwp', 'view_company_branch', 'view_company_bpjs_kesehatan', 'view_company_bpjs_ketenagakerjaan']
                    ],
                    // [
                    //     'module_id' => [1, 2, 3],
                    //     'text' => 'Employee',
                    //     'url'  => route('settings.employee.index')
                    // ]
                    [
                        // 'module_id' => [1, 2, 3],
                        'text' => 'Employee',
                        'url'  => $getEmployeeSettingMenu->handle()[1]['url'] ?? route('settings.employee.general.index'),
                        'can'  => ['view_employee_general', 'view_employee_designation', 'view_employee_employment_status', 'view_employee_group']
                    ],
                    // [
                    //     'text' => 'Overtime',
                    //     'url'  => route('settings.overtime.rule.index')
                    // ],
                    [
                        'text' => 'Approval',
                        'url'  => $getApprovalSettingMenu->handle()[1]['url'] ?? route('settings.approval.rule.index'),
                        'can'  => 'view_approval_rule'
                    ],
                    [
                        'text' => 'Leave Management',
                        'url'  => $getLeaveSettingMenu->handle()[1]['url'] ?? route('settings.leave.general.index'),
                        'can'  => ['view_leave_management_general', 'view_leave_management_leave_type', 'view_leave_management_leave_quota']
                    ],
                    [
                        'text' => 'Attendance',
                        'url'  => $getAttendanceSettingMenu->handle()[1]['url'] ?? route('settings.attendance.general.index'),
                        'can'  => ['view_attendance_general', 'view_attendance_holiday_calendar']
                    ],
                    [
                        'text' => 'Payroll',
                        'url'  => $getPayrollSettingMenu->handle()[1]['url'] ?? route('settings.payroll.general.index'),
                        'can'  => ['view_payroll_general', 'view_payroll_employee_base_salaries', 'view_payroll_payroll_components']
                    ],
                    // [
                    //     'text' => 'Work Report',
                    //     'url'  => $getWorkReportSettingMenu->handle()[1]['url'] ?? route('settings.work-report.general.index'),
                    //     'can'  => 'view_work_report_general'
                    // ],
                    [
                        'text' => 'Systems',
                        'url'  => $getSystemSettingMenu->handle()[1]['url'] ?? route('settings.systems.authentication.index'),
                        'can'  => ['view_systems_authentication', 'view_systems_role_management']
                    ]
                ],
            ]
        ];
    }
}
