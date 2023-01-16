<?php

namespace App\Http\Controllers\Employees\Employee;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminBaseController;
use App\Services\Employee\Employee\EmployeeService;
use App\Actions\Utility\Employee\GetDetailEmployeeMenu;
use App\Services\Employee\Employee\AttendanceLogService;
use App\Http\Resources\Employees\Employee\AttendanceLogListResource;
use App\Http\Resources\Employees\Employee\AttendanceOverviewResource;

class AttendanceLogController extends AdminBaseController
{
    public function __construct(
        EmployeeService $employeeService, AttendanceLogService $attendanceLogService,
        GetDetailEmployeeMenu $getDetailEmployeeMenu
    ) {
        $this->employeeService = $employeeService;;
        $this->getDetailEmployeeMenu = $getDetailEmployeeMenu;
        $this->attendanceLogService = $attendanceLogService;
    }

    public function attendanceLogIndex($id)
    {
        $employee = $this->employeeService->getDetailEmployee($id);

        return Inertia::render($this->source . 'employees/employee/attendanceLog/index', [
            "title" => 'BattleHR | Employee',
            "additional" => [
                'menu' => $this->getDetailEmployeeMenu->handle($id),
                'employee' => $employee
            ]
        ]);
    }

    public function getAttendanceLogOverviewData($id, Request $request)
    {
         try {
            $employee = $this->employeeService->getDetailEmployee($id);
            
            $data = $this->attendanceLogService->getAttendanceLogOverview($employee, $request);
            $result = new AttendanceOverviewResource($data, 'Success Get Attendance Overview');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getData($id, Request $request)
    {
        try {
            $employee = $this->employeeService->getDetailEmployee($id);
            
            $data = $this->attendanceLogService->getData($employee, $request);
            $result = new AttendanceLogListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
