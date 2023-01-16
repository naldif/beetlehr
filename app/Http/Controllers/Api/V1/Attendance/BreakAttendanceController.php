<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController;
use App\Services\Api\V1\Attendance\BreakAttendanceService;
use App\Http\Requests\Api\V1\Attendance\SubmitBreakRequest;
use App\Actions\Utility\Attendance\CheckAttendanceHomeStatus;
use App\Http\Resources\Api\V1\Attendance\SubmitBreakResource;
use App\Http\Resources\Api\V1\Attendance\BreakSettingResource;
use App\Http\Requests\Api\V1\Attendance\BreakCheckLocationRequest;
use App\Http\Resources\Api\V1\Attendance\BreakCheckLocationResource;

class BreakAttendanceController extends ApiBaseController
{
    public function __construct(BreakAttendanceService $breakAttendanceService)
    {
        $this->breakAttendanceService = $breakAttendanceService;
    }

    public function checkBreakLocation(BreakCheckLocationRequest $request)
    {
        try {
            $data = $this->breakAttendanceService->checkLocation($request);

            $return = new BreakCheckLocationResource($data, 'Success Check Location');
            return $this->respond($return);
        } catch (\Throwable $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function submitBreak(SubmitBreakRequest $request)
    {
        try {
            $check_attendance_status = new CheckAttendanceHomeStatus();
            $data = $check_attendance_status->handle($request->date);

            if ($data['message_type']) {
                $result = $this->breakAttendanceService->clockBreakAbnormal($request);
            } else {
                $result = $this->breakAttendanceService->clockBreak($request);
            }

            $return = new SubmitBreakResource($result, 'Success Submit Break', $request->type);
            return $this->respond($return);
        } catch (\Throwable $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function setting()
    {
        try {
            $return = new BreakSettingResource('Success Get Break Setting');
            return $this->respond($return);
        } catch (\Throwable $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
