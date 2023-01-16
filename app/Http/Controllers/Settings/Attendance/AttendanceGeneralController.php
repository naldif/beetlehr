<?php

namespace App\Http\Controllers\Settings\Attendance;

use App\Http\Controllers\AdminBaseController;
use App\Http\Resources\Settings\Attendance\SubmitGeneralAttendanceResource;
use App\Services\Settings\Attendance\AttendanceGeneralService;
use Illuminate\Http\Request;

class AttendanceGeneralController extends AdminBaseController
{
    public function __construct(AttendanceGeneralService $attendanceGeneralService)
    {
        $this->attendanceGeneralService = $attendanceGeneralService;
    }
    public function updateCloseBreakup(Request $request)
    {
        try {
            $data = $this->attendanceGeneralService->updateCloseBreakup($request);

            $result = new SubmitGeneralAttendanceResource($data, 'Success Change Close Breakup Settings');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
