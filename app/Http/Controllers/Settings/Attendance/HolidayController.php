<?php

namespace App\Http\Controllers\Settings\Attendance;

use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\Settings\Attendance\CreateHolidayCalendar;
use App\Http\Resources\Settings\Attendance\HolidayListResource;
use App\Http\Resources\Settings\Attendance\SubmitHolidayResource;
use App\Services\Settings\Attendance\HolidayCalendarService;
use Illuminate\Http\Request;

class HolidayController extends AdminBaseController
{
    public function __construct(HolidayCalendarService $holidayCalendarService)
    {
        $this->holidayCalendarService = $holidayCalendarService;
    }

    public function getHolidayCalendarList(Request $request)
    {
        try {
            $data = $this->holidayCalendarService->getData($request);
            $result = new HolidayListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createHolidayCalendar(CreateHolidayCalendar $request)
    {
        try {
            $data = $this->holidayCalendarService->storeData($request);
            $result = new SubmitHolidayResource($data, 'Success create holiday calendar');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateHolidayCalendar($id, CreateHolidayCalendar $request)
    {
        try {
            $data = $this->holidayCalendarService->updateData($id, $request);
            $result = new SubmitHolidayResource($data, 'Success update holiday calendar');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteHolidayCalendar($id)
    {
        try {
            $data = $this->holidayCalendarService->deleteData($id);
            $result = new SubmitHolidayResource($data, 'Success delete holiday calendar');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
