<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // General Group
            [
                'group' => 'general',
                'sub_group' => [
                    [
                        'name' => 'dashboard',
                        'permission' => [
                            [
                                'name' => 'view_general_dashboard',
                                'label' => 'View Dashboard Overview',
                            ]
                        ]
                    ]
                ]
            ],

            // Employee Management Group
            [
                'group' => 'employee_management',
                'sub_group' => [
                    [
                        'name' => 'employee',
                        'permission' => [
                            [
                                'name' => 'view_employee_management_employee',
                                'label' => 'View Employee',
                            ]
                        ]
                    ],
                    [
                        'name' => 'resign_management',
                        'permission' => [
                            [
                                'name' => 'view_employee_management_resign_management',
                                'label' => 'View Resign Management',
                            ]
                        ]
                    ],
                    [
                        'name' => 'attendance',
                        'permission' => [
                            [
                                'name' => 'view_employee_management_attendance',
                                'label' => 'View Attendance',
                            ]
                        ]
                    ],
                    [
                        'name' => 'shift',
                        'permission' => [
                            [
                                'name' => 'view_employee_management_shift',
                                'label' => 'View Shift',
                            ]
                        ]
                    ],
                    [
                        'name' => 'schedule',
                        'permission' => [
                            [
                                'name' => 'view_employee_management_schedule',
                                'label' => 'View Schedule',
                            ]
                        ]
                    ],
                    [
                        'name' => 'leave',
                        'permission' => [
                            [
                                'name' => 'view_employee_management_leave',
                                'label' => 'View Leave',
                            ]
                        ]
                            ],
                    [
                        'name' => 'approval',
                        'permission' => [
                            [
                                'name' => 'view_employee_management_approval',
                                'label' => 'View Approval',
                            ]
                        ]
                    ]
                ]
            ],

            // Payroll Management Group
            [
                'group' => 'payroll_management',
                'sub_group' => [
                    [
                        'name' => 'payroll',
                        'permission' => [
                            [
                                'name' => 'view_payroll_management_payroll',
                                'label' => 'View Payroll',
                            ]
                        ]
                    ]
                ]
            ],

            // Notice Board Management
            [
                'group' => 'notice_board_management',
                'sub_group' => [
                    [
                        'name' => 'notice_board',
                        'permission' => [
                            [
                                'name' => 'view_notice_board_management',
                                'label' => 'View Notive Board',
                            ]
                        ]
                    ]
                ]
            ],

            // Setting Company
            [
                'group' => 'setting_company',
                'sub_group' => [
                    [
                        'name' => 'profile',
                        'permission' => [
                            [
                                'name' => 'view_company_profile',
                                'label' => 'View Profile',
                            ]
                        ]
                    ],
                    [
                        'name' => 'npwp',
                        'permission' => [
                            [
                                'name' => 'view_company_npwp',
                                'label' => 'View Npwp',
                            ]
                        ]
                    ],
                    [
                        'name' => 'branch',
                        'permission' => [
                            [
                                'name' => 'view_company_branch',
                                'label' => 'View Branch',
                            ]
                        ]
                    ],
                    [
                        'name' => 'bpjs_kesehatan',
                        'permission' => [
                            [
                                'name' => 'view_company_bpjs_kesehatan',
                                'label' => 'View Bpjs Kesehatan',
                            ]
                        ]
                    ],
                    [
                        'name' => 'bpjs_ketenegakerjaan',
                        'permission' => [
                            [
                                'name' => 'view_company_bpjs_ketenagakerjaan',
                                'label' => 'View Bpjs Ketenagakerjaan',
                            ]
                        ]
                    ],
                ]
            ],

            // Setting Employee
            [
                'group' => 'setting_employee',
                'sub_group' => [
                    [
                        'name' => 'general',
                        'permission' => [
                            [
                                'name' => 'view_employee_general',
                                'label' => 'View General',
                            ]
                        ]
                    ],
                    [
                        'name' => 'designation',
                        'permission' => [
                            [
                                'name' => 'view_employee_designation',
                                'label' => 'View Designation',
                            ]
                        ]
                    ],
                    [
                        'name' => 'employment_status',
                        'permission' => [
                            [
                                'name' => 'view_employee_employment_status',
                                'label' => 'View Employment Status',
                            ]
                        ]
                    ],
                    [
                        'name' => 'group',
                        'permission' => [
                            [
                                'name' => 'view_employee_group',
                                'label' => 'View Group',
                            ]
                        ]
                    ]
                ]
            ],

            // // Setting Overtime
            // [
            //     'group' => 'setting_overtime',
            //     'sub_group' => [
            //         [
            //             'name' => 'rules',
            //             'permission' => [
            //                 [
            //                     'name' => 'view_overtime_rule',
            //                     'label' => 'View Rule',
            //                 ]
            //             ]
            //         ],
            //     ]
            // ],

            // Setting Approval
            [
                'group' => 'setting_approval',
                'sub_group' => [
                    [
                        'name' => 'rules',
                        'permission' => [
                            [
                                'name' => 'view_approval_rule',
                                'label' => 'View Rule',
                            ]
                        ]
                    ],
                ]
            ],

            // Setting Leave Management
            [
                'group' => 'setting_leave_management',
                'sub_group' => [
                    [
                        'name' => 'general',
                        'permission' => [
                            [
                                'name' => 'view_leave_management_general',
                                'label' => 'View Leave Management',
                            ]
                        ]
                    ],
                    [
                        'name' => 'leave_type',
                        'permission' => [
                            [
                                'name' => 'view_leave_management_leave_type',
                                'label' => 'View Leave Type',
                            ]
                        ]
                    ],
                    [
                        'name' => 'leave_quota',
                        'permission' => [
                            [
                                'name' => 'view_leave_management_leave_quota',
                                'label' => 'View Leave Quota',
                            ]
                        ]
                    ],
                ]
            ],

            // Setting Attendance
            [
                'group' => 'setting_attendance',
                'sub_group' => [
                    [
                        'name' => 'general',
                        'permission' => [
                            [
                                'name' => 'view_attendance_general',
                                'label' => 'View General',
                            ]
                        ]
                    ],
                    [
                        'name' => 'attendance',
                        'permission' => [
                            [
                                'name' => 'view_attendance',
                                'label' => 'View Attendance',
                            ]
                        ]
                    ],
                    [
                        'name' => 'holiday_calendar',
                        'permission' => [
                            [
                                'name' => 'view_attendance_holiday_calendar',
                                'label' => 'View Holiday Calendar',
                            ]
                        ]
                    ]
                ]
            ],

            // Setting Payroll
            [
                'group' => 'setting_payroll',
                'sub_group' => [
                    [
                        'name' => 'general',
                        'permission' => [
                            [
                                'name' => 'view_payroll_general',
                                'label' => 'View General',
                            ]
                        ]
                    ],
                    [
                        'name' => 'group',
                        'permission' => [
                            [
                                'name' => 'view_payroll_group',
                                'label' => 'View Payroll Group',
                            ]
                        ]
                    ],
                    [
                        'name' => 'employee_base_salaries',
                        'permission' => [
                            [
                                'name' => 'view_payroll_employee_base_salaries',
                                'label' => 'View Employee Base Salaries',
                            ]
                        ]
                    ],
                    [
                        'name' => 'payroll_components',
                        'permission' => [
                            [
                                'name' => 'view_payroll_payroll_components',
                                'label' => 'View Payroll Components',
                            ]
                        ]
                    ]
                ]
            ],

            // Setting Work Report
            // [
            //     'group' => 'setting_work_report',
            //     'sub_group' => [
            //         [
            //             'name' => 'general',
            //             'permission' => [
            //                 [
            //                     'name' => 'view_work_report_general',
            //                     'label' => 'View General',
            //                 ]
            //             ]
            //         ]
            //     ]
            // ],

            // Setting Systems
            [
                'group' => 'setting_systems',
                'sub_group' => [
                    [
                        'name' => 'authentication',
                        'permission' => [
                            [
                                'name' => 'view_systems_authentication',
                                'label' => 'View Authentication',
                            ]
                        ]
                    ],
                    [
                        'name' => 'role_management',
                        'permission' => [
                            [
                                'name' => 'view_systems_role_management',
                                'label' => 'View Role Management',
                            ]
                        ]
                    ]
                ]
            ],
        ];

        // Create permissions 
        try {
            $newPermission = [];
            foreach ($permissions as $group) {
                foreach ($group['sub_group'] as $sub_group) {
                    foreach ($sub_group['permission'] as $permission) {
                        // Create a new one with current id
                        Permission::updateOrCreate([
                            'name' => $permission['name']
                        ], [
                            'name' => $permission["name"],
                            'label' => $permission["label"],
                            'guard_name' => 'web',
                            'group' => $group['group'],
                            'sub_group' => $sub_group['name']
                        ]);

                        array_push($newPermission, $permission['name']);
                    }
                }
            }
            // Get All Permission From database and get the difference then delete it
            $permissionsDb = Permission::get()->pluck('name');
            $diff = array_diff($permissionsDb->toArray(), $newPermission);

            $permissionReadyToDelete = Permission::whereIn('name', array_values($diff))->delete();
        } catch (\Exception $exception) {
            // Do something with the exception
        }
    }
}
