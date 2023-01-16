<?php

namespace App\Http\Controllers\Settings\Leave;

use App\Http\Controllers\AdminBaseController;
use App\Http\Resources\Settings\Leave\SubmitLeaveGeneralResource;
use App\Services\Settings\Leave\LeaveGeneralService;
use Illuminate\Http\Request;

class LeaveGeneralController extends AdminBaseController
{
    public function __construct(LeaveGeneralService $leaveGeneralService)
    {
        $this->leaveGeneralService = $leaveGeneralService;
    }
    public function updateResetLeave(Request $request)
    {
        try {
            $data = $this->leaveGeneralService->updateResetLeave($request);

            $result = new SubmitLeaveGeneralResource($data, 'Success Change Date Reset Leave Qouta');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
