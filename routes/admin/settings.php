<?php

use App\Http\Controllers\Settings\Company\NpwpController;
use App\Http\Controllers\Settings\System\SystemController;
use App\Http\Controllers\Settings\Company\CompanyController;
use App\Http\Controllers\Settings\Company\ProfileController;
use App\Http\Controllers\Settings\Leave\LeaveTypeController;
use App\Http\Controllers\Settings\Payroll\PayrollController;
use App\Http\Controllers\Settings\Leave\LeaveQuotaController;
use App\Http\Controllers\Settings\Approval\ApprovalController;
use App\Http\Controllers\Settings\Company\BranchController;
use App\Http\Controllers\Settings\Employee\EmployeeController;
use App\Http\Controllers\Settings\Overtime\OvertimeController;
use App\Http\Controllers\Settings\Leave\LeaveGeneralController;
use App\Http\Controllers\Settings\Employee\DesignationController;
use App\Http\Controllers\Settings\Approval\ApprovalRuleController;
use App\Http\Controllers\Settings\Attendance\AttendanceController;
use App\Http\Controllers\Settings\Company\BpjsKesehatanController;
use App\Http\Controllers\Settings\Leave\LeaveManagementController;
use App\Http\Controllers\Settings\Overtime\OvertimeRuleController;
use App\Http\Controllers\Settings\System\RoleManagementController;
use App\Http\Controllers\Settings\WorkReport\WorkReportController;
use App\Http\Controllers\Settings\Employee\EmployeeGroupController;
use App\Http\Controllers\Settings\Payroll\GeneralSettingController;
use App\Http\Controllers\Settings\Employee\EmployeeGeneralController;
use App\Http\Controllers\Settings\Payroll\PayrollComponentController;
use App\Http\Controllers\Settings\Employee\EmploymentStatusController;
use App\Http\Controllers\Settings\Payroll\EmployeeBaseSalaryController;
use App\Http\Controllers\Settings\Company\BpjsKetenagakerjaanController;
use App\Http\Controllers\Settings\System\AuthenticationSystemController;
use App\Http\Controllers\Settings\Attendance\AttendanceGeneralController;
use App\Http\Controllers\Settings\WorkReport\WorkReportGeneralController;
use App\Http\Controllers\Settings\Attendance\AttendanceManagementController;
use App\Http\Controllers\Settings\Payroll\PayrollEmployeeComponentController;
use App\Http\Controllers\Settings\Payroll\PayrollBranchComponentController;
use App\Http\Controllers\Settings\Attendance\HolidayController as AttendanceHolidayController;
use App\Http\Controllers\Settings\Payroll\PayrollGroupController;

/*
|--------------------------------------------------------------------------
| Settings Routes
|--------------------------------------------------------------------------
|
| Here is where you can register setting related routes for your application.
|
*/

Route::prefix('settings')->name('settings.')->group(function () {
    Route::controller(EmployeeController::class)->prefix('employee')->name('employee.')->group(function () {
        Route::prefix('general')->name('general.')->group(function () {
            Route::get('/', 'generalSettingIndex')->name('index');
            Route::controller(EmployeeGeneralController::class)->group(function () {
                Route::post('update-editable-employee-external-id', 'updateEditableNip')->name('update');
            });
        });

        Route::prefix('designation')->name('designation.')->group(function () {
            Route::get('/', 'designationSettingIndex')->name('index');
            Route::controller(DesignationController::class)->group(function () {
                Route::get('get-data', 'getDesignationList')->name('getdata');
                Route::post('create-designation', 'createDesignation')->name('create');
                Route::put('{id}/update-designation', 'updateDesignation')->name('update');
                Route::delete('{id}/delete-designation', 'deleteDesignation')->name('delete');
            });
        });

        Route::prefix('employment-status')->name('status.')->group(function () {
            Route::get('/', 'employmentStatusSettingIndex')->name('index');
            Route::controller(EmploymentStatusController::class)->group(function () {
                Route::get('get-data', 'getEmploymentStatusList')->name('getdata');
                Route::post('create-employment-status', 'createEmploymentStatus')->name('create');
                Route::put('{id}/update-employment-status', 'updateEmploymentStatus')->name('update');
                Route::delete('{id}/delete-employment-status', 'deleteEmploymentStatus')->name('delete');
            });
        });

        Route::prefix('group')->name('group.')->group(function () {
            Route::get('/', 'employeeGroupSettingIndex')->name('index');
            Route::controller(EmployeeGroupController::class)->group(function () {
                Route::get('get-data', 'getGroupList')->name('getdata');
                Route::post('get-employee-branch', 'getEmployeeByBranch')->name('getEmployeeByBranch');
                Route::post('create-group', 'createEmployeeGroup')->name('create');
                Route::put('{id}/update-group', 'updateEmployeeGroup')->name('update');
                Route::delete('{id}/delete-group', 'deleteEmployeeGroup')->name('delete');
            });
        });
    });

    Route::controller(CompanyController::class)->prefix('company')->name('company.')->group(function () {
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', 'profileSettingIndex')->name('index');
            Route::controller(ProfileController::class)->group(function () {
                Route::post('upload-profile-picture', 'uploadProfilePicture')->name('uploadpicture');
                Route::post('update-profile', 'updateProfile')->name('updateprofile');
            });
        });

        Route::prefix('npwp')->name('npwp.')->group(function () {
            Route::get('/', 'npwpSettingIndex')->name('index');
            Route::controller(NpwpController::class)->group(function () {
                Route::get('get-data', 'getNpwpList')->name('getdata');
                Route::post('create-npwp', 'createNpwp')->name('create');
                Route::put('{id}/update-npwp', 'updateNpwp')->name('update');
                Route::delete('{id}/delete-npwp', 'deleteNpwp')->name('delete');
            });
        });

        Route::prefix('branch')->name('branch.')->group(function () {
            Route::get('/', 'branchSettingIndex')->name('index');
            Route::controller(BranchController::class)->group(function () {
                Route::get('get-data', 'getBranchList')->name('getdata');
                Route::post('create-branch', 'createBranch')->name('create');
                Route::put('{id}/update-branch', 'updateBranch')->name('update');
                Route::delete('{id}/delete-branch', 'deleteBranch')->name('delete');
            });
        });

        Route::prefix('bpjs')->name('bpjs.')->group(function () {
            Route::get('/', 'bpjsSettingIndex')->name('index');
            Route::controller(BpjsKesehatanController::class)->name('bpjsk.')->group(function () {
                Route::get('get-bpjsk-data', 'getBpjskList')->name('getdata');
                Route::post('create-bpjsk', 'createBpjsk')->name('create');
                Route::put('{id}/update-bpjsk', 'updateBpjsk')->name('update');
                Route::delete('{id}/delete-bpjsk', 'deleteBpjsk')->name('delete');
            });

            Route::controller(BpjsKetenagakerjaanController::class)->name('bpjstk.')->group(function () {
                Route::get('get-bpjstk-data', 'getBpjstkList')->name('getdata');
                Route::post('create-bpjstk', 'createBpjstk')->name('create');
                Route::put('{id}/update-bpjstk', 'updateBpjstk')->name('update');
                Route::delete('{id}/delete-bpjstk', 'deleteBpjstk')->name('delete');
            });
        });
    });

    Route::controller(LeaveManagementController::class)->prefix('leave')->name('leave.')->group(function () {
        Route::prefix('general')->name('general.')->group(function () {
            Route::get('/', 'LeaveGeneralSettingIndex')->name('index');
            Route::controller(LeaveGeneralController::class)->group(function () {
                Route::post('update-reset-leave', 'updateResetLeave')->name('update');
            });
        });

        Route::prefix('type')->name('type.')->group(function () {
            Route::get('/', 'leaveTypeSettingIndex')->name('index');
            Route::controller(LeaveTypeController::class)->group(function () {
                Route::get('get-data', 'getTypeList')->name('getdata');
                Route::post('create-type', 'createType')->name('create');
                Route::put('{id}/update-type', 'updateType')->name('update');
                Route::delete('{id}/delete-type', 'deleteType')->name('delete');
            });
        });

        Route::prefix('quota')->name('quota.')->group(function () {
            Route::get('/', 'leaveQuotaSettingIndex')->name('index');
            Route::controller(LeaveQuotaController::class)->group(function () {
                Route::get('get-data', 'getQuotaList')->name('getdata');
                Route::get('get-leave-type', 'getLeaveTypeOptions')->name('getLeaveType');
                Route::post('create-quota', 'createQuota')->name('create');
                Route::put('{id}/update-quota', 'updateQuota')->name('update');
                Route::delete('{id}/delete-quota', 'deleteQuota')->name('delete');
            });
        });
    });

    Route::controller(OvertimeController::class)->prefix('overtime')->name('overtime.')->group(function () {
        Route::prefix('rule')->name('rule.')->group(function () {
            Route::get('/', 'ruleSettingIndex')->name('index');
            Route::controller(OvertimeRuleController::class)->group(function () {
                Route::get('get-data', 'getRuleList')->name('getdata');
                Route::post('create-rule', 'createRule')->name('create');
                Route::put('{id}/update-rule', 'updateRule')->name('update');
                Route::delete('{id}/delete-rule', 'deleteRule')->name('delete');
            });
        });
    });

    Route::controller(ApprovalController::class)->prefix('approval')->name('approval.')->group(function () {
        Route::prefix('rule')->name('rule.')->group(function () {
            Route::get('/', 'ruleSettingIndex')->name('index');
            Route::get('get-data', 'getTypeIndex')->name('gettype');
            Route::get('{id}', 'configRuleIndex')->name('config');

            Route::controller(ApprovalRuleController::class)->name('config.')->prefix('config')->group(function () {
                Route::get('get-approval-branch', 'getApprovalBranchList')->name('getdata');
                Route::get('get-employee-rule', 'getApprovalEmployeeRule')->name('getemployeerule');
                Route::put('update-approval-branch', 'updateApprovalBranch')->name('update');
                Route::delete('{id}/delete-approval-branch', 'deleteApprovalBranch')->name('delete');
            });
        });
    });

    Route::controller(PayrollController::class)->prefix('payroll')->name('payroll.')->group(function () {
        Route::prefix('general')->name('general.')->group(function () {
            Route::get('/', 'generalSettingIndex')->name('index');
            Route::controller(GeneralSettingController::class)->group(function () {
                Route::post('update-general', 'updateGeneral')->name('update');
            });
        });

        Route::prefix('employee-base-salaries')->name('employee-base-salaries.')->group(function () {
            Route::get('/', 'employeeBaseSalariesIndex')->name('index');
            Route::controller(EmployeeBaseSalaryController::class)->group(function () {
                Route::get('get-data', 'getBaseSalaryList')->name('getdata');
                Route::get('get-designation', 'getDesignationOptions')->name('getdesignation');
                Route::get('get-employee', 'getEmployeeOptions')->name('getemployee');
                Route::post('create-data', 'createBaseSalary')->name('create');
                Route::put('{id}/update-data', 'updateBaseSalary')->name('update');
                Route::delete('{id}/delete-data', 'deleteBaseSalary')->name('delete');
            });
        });

        Route::prefix('group')->name('group.')->group(function () {
            Route::get('/', 'payrollGroupIndex')->name('index');
            Route::controller(PayrollGroupController::class)->group(function () {
                Route::get('get-data', 'getPayrollGroup')->name('getdata');
                Route::post('create-data', 'createPayrollGroup')->name('create');
                Route::put('{id}/update-data', 'updatePayrollGroup')->name('update');
                Route::delete('{id}/delete-data', 'deletePayrollGroup')->name('delete');
            });
        });

        Route::prefix('components')->name('components.')->group(function () {
            Route::get('/', 'payrollComponentIndex')->name('index');
            Route::controller(PayrollComponentController::class)->group(function () {
                Route::get('get-data', 'getComponentList')->name('getdata');
                Route::post('create-data', 'createComponent')->name('create');
                Route::put('{id}/update-data', 'updateComponent')->name('update');
                Route::delete('{id}/delete-data', 'deleteComponent')->name('delete');

                Route::get('{id}/set-value', 'setValueComponent')->name('setvalue');
            });

            Route::controller(PayrollBranchComponentController::class)->name('branch.')->group(function () {
                Route::get('get-payroll-branch', 'getPayrollBranchList')->name('getdata');
                Route::put('update-payroll-branch', 'updatePayrollBranch')->name('update');
                Route::delete('{id}/delete-payroll-branch', 'deletePayrollBranch')->name('delete');
            });

            Route::controller(PayrollEmployeeComponentController::class)->name('employee.')->group(function () {
                Route::get('get-payroll-employee', 'getPayrollEmployeeList')->name('getdata');
                Route::put('update-payroll-employee', 'updatePayrollEmployee')->name('update');
                Route::delete('{id}/delete-payroll-employee', 'deletePayrollEmployee')->name('delete');
            });
        });
    });


    Route::controller(AttendanceManagementController::class)->prefix('attendance')->name('attendance.')->group(function () {
        Route::prefix('general')->name('general.')->group(function () {
            Route::get('/', 'attendanceGeneralSettingIndex')->name('index');
            Route::controller(AttendanceGeneralController::class)->group(function () {
                Route::post('update-close-breakup', 'updateCloseBreakup')->name('updateCloseBreakup');
            });
        });

        Route::get('/', 'attendanceSettingIndex')->name('index');
        Route::controller(AttendanceController::class)->group(function () {
            Route::post('update-attendance', 'updateAttendance')->name('update');
        });

        Route::prefix('holiday')->name('holiday.')->group(function () {
            Route::get('/', 'holidayCalendarSettingIndex')->name('index');
            Route::controller(AttendanceHolidayController::class)->group(function () {
                Route::get('get-data', 'getHolidayCalendarList')->name('getdata');
                Route::post('create-holiday', 'createHolidayCalendar')->name('create');
                Route::put('{id}/update-holiday', 'updateHolidayCalendar')->name('update');
                Route::delete('{id}/delete-holiday', 'deleteHolidayCalendar')->name('delete');
            });
        });
    });

    Route::controller(WorkReportController::class)->prefix('work-report')->name('work-report.')->group(function () {
        Route::prefix('general')->name('general.')->group(function () {
            Route::get('/', 'workReportGeneralSettingIndex')->name('index');
            Route::controller(WorkReportGeneralController::class)->group(function () {
                Route::post('update-max-time', 'updateMaxTime')->name('updateMaxTime');
            });
        });
    });

    Route::controller(SystemController::class)->prefix('systems')->name('systems.')->group(function () {
        Route::prefix('authentication')->name('authentication.')->group(function () {
            Route::get('/', 'AuthenticationSettingIndex')->name('index');
            Route::controller(AuthenticationSystemController::class)->group(function () {
                Route::post('update-lock-user-device', 'updateLockUserDevice')->name('updateLockUserDevice');
            });
        });

        Route::prefix('role')->name('role.')->group(function () {
            Route::get('/', 'roleSettingIndex')->name('index');
            Route::controller(RoleManagementController::class)->group(function () {
                Route::get('get-data', 'getRoleList')->name('getdata');
                Route::get('add-role', 'createRolePage')->name('createpage');
                Route::get('{id}/edit-role', 'editRolePage')->name('editpage');
                Route::post('add-role', 'storeNewRole')->name('storerole');
                Route::put('{id}/update-role', 'updateRole')->name('updateRole');
                Route::delete('{id}/delete-role', 'deleteRole')->name('deleterole');
            });
        });
    });
});
