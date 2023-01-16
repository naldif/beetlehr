<?php

namespace App\Http\Controllers\Attendance\Attendance;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Actions\Options\GetBranchOptions;
use App\Http\Controllers\AdminBaseController;
use App\Services\Attendance\Attendance\AttendanceOverviewService;
use App\Http\Resources\Attendances\Attendance\AttendanceOverviewResource;

class AttendanceOverviewController extends AdminBaseController
{
    public function __construct(GetBranchOptions $getBranchOptions, AttendanceOverviewService $attendanceOverviewService)
    {
        $this->getBranchOptions = $getBranchOptions;
        $this->attendanceOverviewService = $attendanceOverviewService;

        $this->title = "BattleHR | Attendance";
        $this->path = "attendance/attendance/Index";
        $this->data = [
            'branch_list' => $this->getBranchOptions->handle()
        ];
    }

    public function getAttendanceListDate(Request $request)
    {
        try {
            $data = $this->attendanceOverviewService->getAttendanceListDate($request);
            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getAttendanceRecap(Request $request)
    {
        try {
            $data = $this->attendanceOverviewService->getAttendanceRecap($request);
            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getAttendanceOverviewData(Request $request)
    {
        try {
            $data = $this->attendanceOverviewService->getAttendanceOverview($request);
            $result = new AttendanceOverviewResource($data, 'Success Get Attendance Overview');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getAttendanceList(Request $request)
    {
        try {
            $data = $this->attendanceOverviewService->getAttendanceList($request);
            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
