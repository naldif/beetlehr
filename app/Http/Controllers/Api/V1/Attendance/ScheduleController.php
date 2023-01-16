<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController;
use App\Services\Api\V1\Attendance\ScheduleService;
use App\Http\Resources\Api\V1\Attendance\ScheduleListResource;

class ScheduleController extends ApiBaseController
{
    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function getSchedule(Request $request)
    {
        try {
            $data = $this->scheduleService->getData($request);

            $user = new ScheduleListResource($data);
            return $this->respond($user);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
