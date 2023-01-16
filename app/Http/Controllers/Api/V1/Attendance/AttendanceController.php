<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController;
use App\Services\Api\V1\Attendance\AttendanceService;
use App\Actions\Utility\Attendance\CheckAttendanceHomeStatus;
use App\Http\Resources\Api\V1\Attendance\UploadImageResource;
use App\Actions\Utility\Attendance\CheckAttendanceClockedToday;
use App\Http\Requests\Api\V1\Attendance\AttendanceClockRequest;
use App\Http\Resources\Api\V1\Attendance\AttendanceLogResource;
use App\Http\Resources\Api\V1\Attendance\AttendanceClockResource;
use App\Http\Resources\Api\V1\Attendance\AttendanceDetailResource;
use App\Http\Resources\Api\V1\Attendance\ChekButtonClockinResource;
use App\Http\Requests\Api\V1\Attendance\CheckAttendanceClockRequest;
use App\Http\Resources\Api\V1\Attendance\AttendanceOverviewResource;
use App\Http\Requests\Api\V1\Attendance\UploadImageAttendanceRequest;
use App\Http\Requests\Api\V1\Attendance\CheckAttendanceLocationRequest;
use App\Http\Resources\Api\V1\Attendance\CheckAttendanceClockedResource;
use App\Http\Resources\Api\V1\Attendance\CheckAttendanceLocationResource;
use App\Http\Requests\Api\V1\Attendance\CheckAttendanceBeforeClockRequest;
use App\Http\Resources\Api\V1\Attendance\CheckAttendanceBeforeClockResource;

class AttendanceController extends ApiBaseController
{
    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function getAttendanceLog(Request $request)
    {
        try {
            $data = $this->attendanceService->getAttendanceLogData($request);

            $result = new AttendanceLogResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getAttendanceOverview(Request $request)
    {
        try {
            $data = $this->attendanceService->getAttendanceOverviewData($request);

            $result = new AttendanceOverviewResource($data, 'Success Get Attendance Overview');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getAttendanceDetail($date, Request $request)
    {
        try {
            $check_attendance_status = new CheckAttendanceHomeStatus();
            $status = $check_attendance_status->handle($date);
            $data = $this->attendanceService->getAttendanceDetailData($date, $request, $status);
            $result = (new AttendanceDetailResource($data))->status($status['message_type']);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function checkButtonClockin()
    {
        try {
            $today = Carbon::now();
            $check_attendance_status = new CheckAttendanceHomeStatus();
            $data = $check_attendance_status->handle($today);

            $result = new ChekButtonClockinResource($data, 'Success Check Button Clockin');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function checkAttendanceClocked(CheckAttendanceClockRequest $request)
    {
        try {
            $check_attendance_status = new CheckAttendanceClockedToday();
            $data = $check_attendance_status->handle($request);

            $result = new CheckAttendanceClockedResource($data, 'Success Check Attendance Clocked');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function uploadAttendanceImage(UploadImageAttendanceRequest $request)
    {
        try {
            $data = $this->attendanceService->uploadImage($request);
            $result = new UploadImageResource($data, 'Success Upload Image', $request->status);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function checkAttendanceLocation(CheckAttendanceLocationRequest $request)
    {
        try {
            $data = $this->attendanceService->checkLocation($request);
            $result = new CheckAttendanceLocationResource($data, 'Success Check Attendance Location');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function checkAttendanceBeforeClock(CheckAttendanceBeforeClockRequest $request)
    {
        try {
            $data = $this->attendanceService->checkAttendanceBeforeClock($request);
            $result = new CheckAttendanceBeforeClockResource($data, 'Success Check Attendance Before Clock');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function attendanceClock(AttendanceClockRequest $request)
    {
        try {
            $data = $this->attendanceService->attendanceClock($request);
            $result = new AttendanceClockResource($data, 'Success Submit Attendance');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function syncOfflineAttendance(Request $request)
    {
        try {
            $this->attendanceService->syncAttendanceOffline($request);
            return $this->respond([
                'meta' => [
                    'success' => true,
                    'message' => 'Success Sync Data',
                    'pagination' => (object) []
                ],
            ]);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function cancelAttendanceOffline()
    {
        try {
            $this->attendanceService->cancelAttendanceOffline();
            return $this->respond([
                'meta' => [
                    'success' => true,
                    'message' => 'Success Cancel Attendance',
                    'pagination' => (object) []
                ],
            ]);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
