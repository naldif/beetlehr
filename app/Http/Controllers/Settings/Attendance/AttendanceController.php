<?php

namespace App\Http\Controllers\Settings\Attendance;

use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\Settings\Attendance\UpdateAttendance;
use App\Http\Resources\Settings\Attendance\SubmitGeneralAttendanceResource;
use App\Services\Settings\Attendance\AttendanceService;

class AttendanceController extends AdminBaseController
{
    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function updateAttendance(UpdateAttendance $request)
    {
        try {
            $data = $this->attendanceService->updateData($request);

            $result = new SubmitGeneralAttendanceResource($data, 'Success Change Attendance Settings');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
